<?php

declare(strict_types=1);

use Overtrue\PHPOpenCC\OpenCC;

$options = getopt('', ['in:', 'out::', 'strategy::', 'lang::']);

$in = $options['in'] ?? null;
if (!is_string($in) || trim($in) === '') {
    fwrite(STDERR, "Missing required option: --in\n");
    exit(1);
}

$projectRoot = dirname(__DIR__);
$inPath = $in;
if (!str_starts_with($inPath, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:\\\\/', $inPath)) {
    $inPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $inPath);
}

if (!is_file($inPath)) {
    fwrite(STDERR, "Input file not found: {$inPath}\n");
    exit(1);
}

$out = $options['out'] ?? null;
$lang = $options['lang'] ?? null;
$strategy = $options['strategy'] ?? 's2tw';

if (!is_string($strategy) || trim($strategy) === '') {
    $strategy = 's2tw';
}

if (!is_string($lang) || trim($lang) === '') {
    $lang = null;
}

if (!is_string($out) || trim($out) === '') {
    $basename = basename($inPath);
    if ($lang !== null) {
        $out = preg_replace('/^[a-z]{2}_[A-Z]{2}\.po$/', $lang . '.po', $basename) ?: ($lang . '.po');
        $out = dirname($inPath) . DIRECTORY_SEPARATOR . $out;
    } else {
        $out = dirname($inPath) . DIRECTORY_SEPARATOR . $basename;
    }
} else {
    $outPath = $out;
    if (!str_starts_with($outPath, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:\\\\/', $outPath)) {
        $outPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outPath);
    }
    $out = $outPath;
}

$autoload = $projectRoot . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Composer autoload not found: {$autoload}\n");
    exit(1);
}
require_once $autoload;

$input = file_get_contents($inPath);
if (!is_string($input)) {
    fwrite(STDERR, "Failed to read input file: {$inPath}\n");
    exit(1);
}

$lines = preg_split("/\r\n|\n|\r/", $input);
if (!is_array($lines)) {
    fwrite(STDERR, "Failed to parse input file: {$inPath}\n");
    exit(1);
}

$outputLines = [];
$inMsgstr = false;
$strategyUpper = strtoupper($strategy);

$encodePo = static function (string $value): string {
    return str_replace(
        ["\\", "\"", "\n", "\r", "\t"],
        ["\\\\", "\\\"", "\\n", "\\r", "\\t"],
        $value
    );
};

$convertQuotedLine = static function (string $line) use ($encodePo, $strategyUpper): string {
    if (!preg_match('/^(.*?")((?:\\\\.|[^"\\\\])*)(".*)$/', $line, $m)) {
        return $line;
    }

    $prefix = $m[1];
    $content = $m[2];
    $suffix = $m[3];

    $decoded = stripcslashes($content);
    $converted = OpenCC::convert($decoded, $strategyUpper);
    if (!is_string($converted)) {
        $converted = (string) $converted;
    }
    $encoded = $encodePo($converted);

    return $prefix . $encoded . $suffix;
};

foreach ($lines as $line) {
    if (preg_match('/^msgstr(\[\d+\])?\s+"/', $line)) {
        $inMsgstr = true;
        $outputLines[] = $convertQuotedLine($line);
        continue;
    }

    if (preg_match('/^(msgid|msgctxt|msgid_plural|msgstr\[\d+\])\b/', $line) && !preg_match('/^msgstr(\[\d+\])?\b/', $line)) {
        $inMsgstr = false;
        $outputLines[] = $line;
        continue;
    }

    if ($inMsgstr && str_starts_with($line, '"')) {
        $outputLines[] = $convertQuotedLine($line);
        continue;
    }

    $outputLines[] = $line;
}

$output = implode("\n", $outputLines) . "\n";

if ($lang !== null) {
    $output = preg_replace(
        '/"Language:\s*[^"\\\\]*\\\\n"/',
        '"Language: ' . $lang . '\\n"',
        $output,
        1
    ) ?? $output;
}

$outDir = dirname($out);
if (!is_dir($outDir)) {
    if (!mkdir($outDir, 0777, true) && !is_dir($outDir)) {
        fwrite(STDERR, "Failed to create output directory: {$outDir}\n");
        exit(1);
    }
}

if (file_put_contents($out, $output) === false) {
    fwrite(STDERR, "Failed to write output file: {$out}\n");
    exit(1);
}

fwrite(STDOUT, "Converted: {$inPath} -> {$out}\n");
