<?php
/**
 * Replaces calls to the removed mysql_* extension with the application's
 * native mysqli-backed db_* adapter.
 *
 * Usage:
 *   php convertir_mysql_a_mysqli.php [project-root] [--dry-run]
 */

$root = isset($argv[1]) && strpos($argv[1], '--') !== 0
    ? $argv[1]
    : dirname(__DIR__);
$dryRun = in_array('--dry-run', $argv, true);
$root = realpath($root);

if ($root === false || !is_dir($root)) {
    fwrite(STDERR, "No se ha encontrado la raíz del proyecto.\n");
    exit(1);
}

$map = array(
    'mysql_connect' => 'db_connect',
    'mysql_pconnect' => 'db_pconnect',
    'mysql_select_db' => 'db_select_db',
    'mysql_query' => 'db_query',
    'mysql_unbuffered_query' => 'db_unbuffered_query',
    'mysql_fetch_array' => 'db_fetch_array',
    'mysql_fetch_assoc' => 'db_fetch_assoc',
    'mysql_fetch_row' => 'db_fetch_row',
    'mysql_fetch_field' => 'db_fetch_field',
    'mysql_num_rows' => 'db_num_rows',
    'mysql_num_fields' => 'db_num_fields',
    'mysql_data_seek' => 'db_data_seek',
    'mysql_free_result' => 'db_free_result',
    'mysql_result' => 'db_result',
    'mysql_insert_id' => 'db_insert_id',
    'mysql_affected_rows' => 'db_affected_rows',
    'mysql_real_escape_string' => 'db_real_escape_string',
    'mysql_escape_string' => 'db_escape_string',
    'mysql_error' => 'db_error',
    'mysql_errno' => 'db_errno',
    'mysql_close' => 'db_close',
);

$excluded = array(
    realpath(__FILE__),
);

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$filesChanged = 0;
$callsChanged = 0;
$byFunction = array();

foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }

    $path = $file->getRealPath();
    if (in_array($path, $excluded, true)) {
        continue;
    }

    $source = file_get_contents($path);
    if ($source === false || strpos($source, 'mysql_') === false) {
        continue;
    }

    $tokens = token_get_all($source);
    $output = '';
    $changedInFile = 0;

    foreach ($tokens as $token) {
        if (is_array($token)) {
            $text = $token[1];
            if ($token[0] === T_STRING) {
                $lower = strtolower($text);
                if (isset($map[$lower])) {
                    $text = $map[$lower];
                    $changedInFile++;
                    $callsChanged++;
                    if (!isset($byFunction[$lower])) {
                        $byFunction[$lower] = 0;
                    }
                    $byFunction[$lower]++;
                }
            }
            $output .= $text;
        } else {
            $output .= $token;
        }
    }

    if ($changedInFile === 0) {
        continue;
    }

    $filesChanged++;
    if (!$dryRun && file_put_contents($path, $output) === false) {
        fwrite(STDERR, "No se ha podido escribir: {$path}\n");
        exit(2);
    }
}

ksort($byFunction);
echo json_encode(array(
    'dry_run' => $dryRun,
    'files_changed' => $filesChanged,
    'calls_changed' => $callsChanged,
    'by_function' => $byFunction,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
