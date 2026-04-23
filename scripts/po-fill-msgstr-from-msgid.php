<?php

declare(strict_types=1);

$options = getopt('', ['in:', 'out::', 'match-msgid::', 'include-fuzzy::']);

$in = $options['in'] ?? null;
if (!is_string($in) || trim($in) === '') {
    fwrite(STDERR, "Missing required option: --in\n");
    exit(1);
}

$projectRoot = dirname(__DIR__);
$toAbsolutePath = static function (string $path) use ($projectRoot): string {
    $p = $path;
    if (!str_starts_with($p, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Za-z]:\\\\/', $p)) {
        $p = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
    }
    return $p;
};

$inPath = $toAbsolutePath($in);
if (!is_file($inPath)) {
    fwrite(STDERR, "Input file not found: {$inPath}\n");
    exit(1);
}

$out = $options['out'] ?? null;
$outPath = $inPath;
if (is_string($out) && trim($out) !== '') {
    $outPath = $toAbsolutePath($out);
}

$matchMsgid = $options['match-msgid'] ?? null;
if (!is_string($matchMsgid) || trim($matchMsgid) === '') {
    $matchMsgid = null;
}

$includeFuzzy = $options['include-fuzzy'] ?? '0';
$includeFuzzyBool = in_array((string) $includeFuzzy, ['1', 'true', 'yes', 'on'], true);

$input = file_get_contents($inPath);
if (!is_string($input)) {
    fwrite(STDERR, "Failed to read input file: {$inPath}\n");
    exit(1);
}

$encodePo = static function (string $value): string {
    return str_replace(
        ["\\", "\"", "\n", "\r", "\t"],
        ["\\\\", "\\\"", "\\n", "\\r", "\\t"],
        $value
    );
};

$decodePo = static function (string $value): string {
    return stripcslashes($value);
};

$extractQuoted = static function (string $line): ?string {
    if (!preg_match('/"((?:\\\\.|[^"\\\\])*)"/', $line, $m)) {
        return null;
    }
    return $m[1];
};

$parseMultiline = static function (array $lines, int $startIndex, string $prefixPattern) use ($extractQuoted, $decodePo): array {
    $value = '';
    $i = $startIndex;

    $quoted = $extractQuoted($lines[$i]);
    if ($quoted !== null) {
        $value .= $decodePo($quoted);
    }
    $i++;

    while (isset($lines[$i]) && str_starts_with($lines[$i], '"')) {
        $quoted = $extractQuoted($lines[$i]);
        if ($quoted !== null) {
            $value .= $decodePo($quoted);
        }
        $i++;
    }

    return [$value, $i];
};

$lines = preg_split("/\r\n|\n|\r/", $input);
if (!is_array($lines)) {
    fwrite(STDERR, "Failed to parse input file: {$inPath}\n");
    exit(1);
}

$entries = [];
$current = [];
foreach ($lines as $line) {
    if (trim($line) === '' && $current !== []) {
        $entries[] = $current;
        $current = [];
        continue;
    }
    $current[] = $line;
}
if ($current !== []) {
    $entries[] = $current;
}

$outputEntries = [];
$changedCount = 0;

foreach ($entries as $entryLines) {
    $raw = $entryLines;

    $comments = [];
    $body = [];
    foreach ($raw as $line) {
        if (str_starts_with($line, '#') && $body === []) {
            $comments[] = $line;
            continue;
        }
        $body[] = $line;
    }

    $isFuzzy = false;
    foreach ($comments as $c) {
        if (str_starts_with($c, '#,') && str_contains($c, 'fuzzy')) {
            $isFuzzy = true;
            break;
        }
    }

    $i = 0;
    $msgctxt = null;
    $msgid = null;
    $msgidPlural = null;
    $msgstr = [];

    while (isset($body[$i])) {
        $line = $body[$i];

        if (preg_match('/^msgctxt\b/', $line)) {
            [$value, $next] = $parseMultiline($body, $i, 'msgctxt');
            $msgctxt = $value;
            $i = $next;
            continue;
        }

        if (preg_match('/^msgid_plural\b/', $line)) {
            [$value, $next] = $parseMultiline($body, $i, 'msgid_plural');
            $msgidPlural = $value;
            $i = $next;
            continue;
        }

        if (preg_match('/^msgid\b/', $line)) {
            [$value, $next] = $parseMultiline($body, $i, 'msgid');
            $msgid = $value;
            $i = $next;
            continue;
        }

        if (preg_match('/^msgstr\[(\d+)\]\b/', $line, $m)) {
            $idx = (int) $m[1];
            [$value, $next] = $parseMultiline($body, $i, 'msgstr[' . $idx . ']');
            $msgstr[(string) $idx] = $value;
            $i = $next;
            continue;
        }

        if (preg_match('/^msgstr\b/', $line)) {
            [$value, $next] = $parseMultiline($body, $i, 'msgstr');
            $msgstr[''] = $value;
            $i = $next;
            continue;
        }

        $i++;
    }

    if ($msgid === null) {
        $outputEntries[] = $raw;
        continue;
    }

    if ($msgid === '') {
        $outputEntries[] = $raw;
        continue;
    }

    if ($matchMsgid !== null && $msgid !== $matchMsgid) {
        $outputEntries[] = $raw;
        continue;
    }

    if ($isFuzzy && !$includeFuzzyBool) {
        $outputEntries[] = $raw;
        continue;
    }

    $hasPlurals = $msgidPlural !== null;
    $newMsgstr = $msgstr;

    if ($hasPlurals) {
        foreach ($newMsgstr as $k => $v) {
            if ($v !== '') {
                continue;
            }
            $idx = ($k === '') ? 0 : (int) $k;
            $newMsgstr[$k] = $idx === 0 ? $msgid : ($msgidPlural ?? $msgid);
        }
        if ($newMsgstr === []) {
            $newMsgstr['0'] = $msgid;
            $newMsgstr['1'] = $msgidPlural ?? $msgid;
        }
    } else {
        $currentMsgstr = $newMsgstr[''] ?? '';
        if ($currentMsgstr === '') {
            $newMsgstr[''] = $msgid;
        }
    }

    if ($newMsgstr !== $msgstr) {
        $changedCount++;
        $rebuilt = [];
        foreach ($comments as $c) {
            $rebuilt[] = $c;
        }
        if ($msgctxt !== null) {
            $rebuilt[] = 'msgctxt "' . $encodePo($msgctxt) . '"';
        }
        $rebuilt[] = 'msgid "' . $encodePo($msgid) . '"';
        if ($hasPlurals) {
            $rebuilt[] = 'msgid_plural "' . $encodePo((string) $msgidPlural) . '"';
            ksort($newMsgstr, SORT_NATURAL);
            foreach ($newMsgstr as $k => $v) {
                $idx = ($k === '') ? 0 : (int) $k;
                $rebuilt[] = 'msgstr[' . $idx . '] "' . $encodePo($v) . '"';
            }
        } else {
            $rebuilt[] = 'msgstr "' . $encodePo($newMsgstr[''] ?? '') . '"';
        }

        $outputEntries[] = $rebuilt;
        continue;
    }

    $outputEntries[] = $raw;
}

$outputLines = [];
foreach ($outputEntries as $entry) {
    foreach ($entry as $l) {
        $outputLines[] = $l;
    }
    $outputLines[] = '';
}

if ($outputLines !== [] && end($outputLines) === '') {
    array_pop($outputLines);
}

$output = implode("\n", $outputLines) . "\n";

$outDir = dirname($outPath);
if (!is_dir($outDir)) {
    if (!mkdir($outDir, 0777, true) && !is_dir($outDir)) {
        fwrite(STDERR, "Failed to create output directory: {$outDir}\n");
        exit(1);
    }
}

if (file_put_contents($outPath, $output) === false) {
    fwrite(STDERR, "Failed to write output file: {$outPath}\n");
    exit(1);
}

fwrite(STDOUT, "Filled {$changedCount} entr" . ($changedCount === 1 ? 'y' : 'ies') . ": {$inPath} -> {$outPath}\n");
