<?php
/**
 * Converts legacy PHP short open tags to standard <?php tags.
 *
 * It uses PHP's tokenizer instead of a raw regular expression so strings and
 * inline content are preserved. Run with PHP 5.6 while short_open_tag is On.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script solo se puede ejecutar desde la linea de comandos.\n");
    exit(1);
}

$root = isset($argv[1]) ? realpath($argv[1]) : false;
if (!$root || !is_dir($root)) {
    fwrite(STDERR, "Uso: php convertir_etiquetas_cortas.php <directorio-raiz>\n");
    exit(1);
}

$files_scanned = 0;
$files_changed = 0;
$tags_changed = 0;
$errors = array();

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $source = file_get_contents($path);
    if ($source === false) {
        $errors[] = $path . ': no se pudo leer';
        continue;
    }

    $files_scanned++;
    $tokens = token_get_all($source);
    $rebuilt = '';
    $offset = 0;
    $file_changes = 0;

    foreach ($tokens as $token) {
        $text = is_array($token) ? $token[1] : $token;

        if (is_array($token) && $token[0] === T_OPEN_TAG) {
            $looks_like_xml = strtolower(substr($source, $offset, 5)) === '<?xml';
            $is_standard_tag = stripos($text, '<?php') === 0;

            if ($is_standard_tag && strpos($text, '<?php') !== 0) {
                $text = '<?php' . substr($text, 5);
                $file_changes++;
            } elseif (!$looks_like_xml && !$is_standard_tag && strpos($text, '<?') === 0) {
                $suffix = substr($text, 2);
                if ($suffix === '' || !preg_match('/^\s/', $suffix)) {
                    $suffix = ' ' . $suffix;
                }
                $text = '<?php' . $suffix;
                $file_changes++;
            }
        }

        $rebuilt .= $text;
        $offset += strlen(is_array($token) ? $token[1] : $token);
    }

    /*
     * A few malformed legacy files leave short tags inside inline HTML from
     * the tokenizer's point of view. This narrow fallback handles only a
     * standalone tag or "<?php if"; standard PHP, echo and XML tags are excluded.
     */
    $rebuilt = preg_replace(
        '/<\?(?!php\b|=|xml\b)(?=\s|if\b)/i',
        '<?php ',
        $rebuilt,
        -1,
        $fallback_changes
    );
    $file_changes += $fallback_changes;

    if ($file_changes === 0) {
        continue;
    }

    if (file_put_contents($path, $rebuilt) === false) {
        $errors[] = $path . ': no se pudo escribir';
        continue;
    }

    $files_changed++;
    $tags_changed += $file_changes;
}

echo json_encode(array(
    'root' => $root,
    'files_scanned' => $files_scanned,
    'files_changed' => $files_changed,
    'tags_changed' => $tags_changed,
    'errors' => $errors,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit(count($errors) > 0 ? 2 : 0);
