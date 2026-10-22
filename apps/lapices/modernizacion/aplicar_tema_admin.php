<?php
/**
 * Applies the modern administrative shell to shared legacy headers while
 * deliberately leaving parent and print layouts unchanged.
 */

$root = dirname(__DIR__) . '/configuracion';
$excluded = array(
    'parte_superior_padres.php',
    'parte_superior_privado_imprimir.php',
);

$filesChanged = 0;
$loginIncludesChanged = 0;
$bodyClassesChanged = 0;

foreach (glob($root . '/parte_superior*.php') as $path) {
    if (in_array(basename($path), $excluded, true)) {
        continue;
    }

    $source = file_get_contents($path);
    if ($source === false) {
        fwrite(STDERR, "No se ha podido leer: {$path}\n");
        exit(1);
    }

    $updated = $source;
    $updated = str_replace(
        'inc/login_sup.php',
        'inc/login_sup_moderno.php',
        $updated,
        $includeCount
    );

    if (strpos($updated, 'class="legacy-admin"') === false) {
        $updated = preg_replace(
            '/<body\b/',
            '<body class="legacy-admin"',
            $updated,
            1,
            $bodyCount
        );
    } else {
        $bodyCount = 0;
    }

    if ($updated === $source) {
        continue;
    }

    if (file_put_contents($path, $updated) === false) {
        fwrite(STDERR, "No se ha podido escribir: {$path}\n");
        exit(2);
    }

    $filesChanged++;
    $loginIncludesChanged += $includeCount;
    $bodyClassesChanged += $bodyCount;
}

echo json_encode(array(
    'files_changed' => $filesChanged,
    'login_includes_changed' => $loginIncludesChanged,
    'body_classes_changed' => $bodyClassesChanged,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";

