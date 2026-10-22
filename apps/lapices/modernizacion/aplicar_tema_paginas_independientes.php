<?php
/**
 * Adds the scoped administrative theme to standalone management documents
 * that render their own <body>. Parent-facing applications are out of scope.
 */

$root = dirname(__DIR__);
$directories = array(
    'gestion',
    'alumnos',
    'aulas',
    'aulas_docentes',
    'facturacion',
    'cocina',
    'almacen',
);

$resources = <<<'HTML'
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20260908.3">
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
HTML;

$filesScanned = 0;
$filesChanged = 0;
$bodyClasses = 0;
$resourceBlocks = 0;

foreach ($directories as $directory) {
    $path = $root . '/' . $directory;
    if (!is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }

        $fullPath = $file->getRealPath();
        $normalized = str_replace('\\', '/', $fullPath);
        if (strpos($normalized, '/gestion/panel/') !== false) {
            continue;
        }

        $source = file_get_contents($fullPath);
        if ($source === false || stripos($source, '<body') === false) {
            continue;
        }

        $hasInlineBody = false;
        foreach (token_get_all($source) as $token) {
            if (is_array($token)
                && $token[0] === T_INLINE_HTML
                && stripos($token[1], '<body') !== false
            ) {
                $hasInlineBody = true;
                break;
            }
        }
        if (!$hasInlineBody) {
            continue;
        }

        $filesScanned++;
        $updated = $source;
        if (strpos($updated, 'class="legacy-admin"') === false
            && strpos($updated, 'class=\'legacy-admin\'') === false
        ) {
            $updated = preg_replace_callback(
                '/<body\b([^>]*)>/i',
                function ($matches) use (&$bodyClasses) {
                    $attributes = $matches[1];
                    if (preg_match('/\bclass\s*=\s*(["\'])(.*?)\1/i', $attributes, $classMatch)) {
                        if (preg_match('/(^|\s)legacy-admin(\s|$)/', $classMatch[2])) {
                            return $matches[0];
                        }

                        $newClass = $classMatch[2] . ' legacy-admin';
                        $attributes = str_replace(
                            $classMatch[0],
                            'class=' . $classMatch[1] . $newClass . $classMatch[1],
                            $attributes
                        );
                    } else {
                        $attributes = ' class="legacy-admin"' . $attributes;
                    }

                    $bodyClasses++;
                    return '<body' . $attributes . '>';
                },
                $updated,
                1
            );
        }

        if (strpos($updated, 'legacy-admin.css') === false) {
            if (stripos($updated, '</head>') !== false) {
                $updated = preg_replace(
                    '/<\/head>/i',
                    $resources . "\n</head>",
                    $updated,
                    1
                );
            } else {
                $updated = preg_replace(
                    '/<body\b/i',
                    $resources . "\n<body",
                    $updated,
                    1
                );
            }
            $resourceBlocks++;
        }

        if ($updated === $source) {
            continue;
        }

        if (file_put_contents($fullPath, $updated) === false) {
            fwrite(STDERR, "No se ha podido escribir: {$fullPath}\n");
            exit(2);
        }
        $filesChanged++;
    }
}

echo json_encode(array(
    'files_scanned' => $filesScanned,
    'files_changed' => $filesChanged,
    'body_classes_added' => $bodyClasses,
    'resource_blocks_added' => $resourceBlocks,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";

