<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
chdir($projectRoot);

$options = getopt('', [
    'source::',
    'components::',
    'domain::',
    'extensions::',
    'dry-run',
]);

$source = $options['source'] ?? 'languages';
$componentsDir = $options['components'] ?? 'src/components';
$domain = $options['domain'] ?? 'aiya-cms';
$extensionsRaw = $options['extensions'] ?? 'js,jsx,ts,tsx';
$dryRun = array_key_exists('dry-run', $options);

if (!is_string($source) || trim($source) === '') {
    fwrite(STDERR, "Invalid --source option.\n");
    exit(1);
}

if (!is_string($componentsDir) || trim($componentsDir) === '') {
    fwrite(STDERR, "Invalid --components option.\n");
    exit(1);
}

if (!is_string($domain) || trim($domain) === '') {
    fwrite(STDERR, "Invalid --domain option.\n");
    exit(1);
}

$extensions = array_values(array_filter(array_map(
    static function ($value): string {
        $trimmed = strtolower(trim($value));
        return ltrim($trimmed, '.');
    },
    explode(',', (string) $extensionsRaw)
)));

if ($extensions === []) {
    fwrite(STDERR, "No valid extensions provided. Example: --extensions=tsx,ts\n");
    exit(1);
}

$componentsPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $componentsDir);
if (!is_dir($componentsPath)) {
    fwrite(STDERR, "Components directory not found: {$componentsPath}\n");
    exit(1);
}

$sourcePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $source);
if (!is_dir($sourcePath) && !is_file($sourcePath)) {
    fwrite(STDERR, "Source path not found: {$sourcePath}\n");
    exit(1);
}

$bootFs = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'wp-cli' . DIRECTORY_SEPARATOR . 'wp-cli' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'boot-fs.php';
if (!is_file($bootFs)) {
    fwrite(STDERR, "WP-CLI boot file not found: {$bootFs}\n");
    exit(1);
}

$componentsPrefix = trim(str_replace('\\', '/', (string) $componentsDir), '/');
if ($componentsPrefix !== '') {
    $componentsPrefix .= '/';
}

$map = [];
$poFiles = [];
if (is_dir($sourcePath)) {
    foreach (new DirectoryIterator($sourcePath) as $fileInfo) {
        if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'po') {
            $poFiles[] = $fileInfo->getPathname();
        }
    }
} else {
    $poFiles[] = $sourcePath;
}

if ($poFiles === []) {
    fwrite(STDERR, "No PO files found in {$source}\n");
    exit(1);
}

foreach ($poFiles as $poFile) {
    $contents = file_get_contents($poFile);
    if (!is_string($contents) || $contents === '') {
        continue;
    }

    $lines = preg_split("/\r\n|\n|\r/", $contents);
    if (!is_array($lines)) {
        continue;
    }

    foreach ($lines as $line) {
        if (!str_starts_with($line, '#:')) {
            continue;
        }

        $parts = preg_split('/\s+/', trim(substr($line, 2)));
        if (!is_array($parts)) {
            continue;
        }

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            if (!preg_match('/^(.+?):\d+$/', $part, $m)) {
                continue;
            }

            $file = str_replace('\\', '/', $m[1]);
            $file = ltrim($file, './');

            if ($componentsPrefix !== '' && !str_starts_with($file, $componentsPrefix)) {
                continue;
            }

            $ext = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $extensions, true)) {
                continue;
            }

            $map[$file] = $file;
        }
    }
}

if ($map === []) {
    fwrite(STDERR, "No matching references found in PO files under {$componentsDir} for extensions: " . implode(',', $extensions) . "\n");
    fwrite(STDERR, "Tip: Run i18n:pot and i18n update-po first, and ensure POT keeps location lines.\n");
    exit(1);
}

$mapJson = json_encode($map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if (!is_string($mapJson) || $mapJson === '') {
    fwrite(STDERR, "Failed to build --use-map JSON.\n");
    exit(1);
}

$tmpMapPath = $projectRoot . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . '.i18n-components-map.json';
if (file_put_contents($tmpMapPath, $mapJson) === false) {
    fwrite(STDERR, "Failed to write map file: {$tmpMapPath}\n");
    exit(1);
}

$cmd = sprintf(
    '%s %s i18n make-json %s --domain=%s --extensions=%s --use-map=%s',
    escapeshellarg(PHP_BINARY),
    escapeshellarg($bootFs),
    escapeshellarg($source),
    escapeshellarg($domain),
    escapeshellarg(implode(',', $extensions)),
    escapeshellarg($tmpMapPath)
);

fwrite(STDOUT, "Matched component files: " . count($map) . "\n");
fwrite(STDOUT, "Running: {$cmd}\n");

if ($dryRun) {
    exit(0);
}

passthru($cmd, $exitCode);
if (is_file($tmpMapPath)) {
    @unlink($tmpMapPath);
}
exit((int) $exitCode);
