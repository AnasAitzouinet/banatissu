<?php

$file = __DIR__ . '/../vendor/voku/portable-ascii/src/voku/helper/ASCII.php';

if (! file_exists($file)) {
    echo "portable-ascii not installed; patch skipped.\n";
    return;
}

$content = file_get_contents($file);
$vulnerable = 'array_merge([], ...$CHARS_ARRAY[$cacheKey])';
$patched = 'array_merge([], ...array_values($CHARS_ARRAY[$cacheKey] ?? []))';

if (strpos($content, $patched) !== false) {
    echo "portable-ascii patch already applied.\n";
    return;
}

if (strpos($content, $vulnerable) === false) {
    echo "portable-ascii vulnerable expression not found; patch skipped.\n";
    return;
}

file_put_contents($file, str_replace($vulnerable, $patched, $content));
echo "portable-ascii patched for PHP 8.1.\n";
