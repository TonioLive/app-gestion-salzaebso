<?php
/**
 * Token-based audit of executable database calls. Comments and string
 * literals are deliberately ignored.
 */

$root = isset($argv[1]) ? realpath($argv[1]) : dirname(__DIR__);
if ($root === false || !is_dir($root)) {
    fwrite(STDERR, "No se ha encontrado la raíz del proyecto.\n");
    exit(1);
}

$excluded = array_filter(array(
    realpath($root . '/configuracion/database.php'),
    realpath($root . '/modernizacion/convertir_mysql_a_mysqli.php'),
    realpath(__FILE__),
));

$tracked = array(
    'mysql_connect',
    'mysql_pconnect',
    'mysql_select_db',
    'mysql_query',
    'mysql_unbuffered_query',
    'mysql_fetch_array',
    'mysql_fetch_assoc',
    'mysql_fetch_row',
    'mysql_fetch_field',
    'mysql_num_rows',
    'mysql_num_fields',
    'mysql_data_seek',
    'mysql_free_result',
    'mysql_result',
    'mysql_insert_id',
    'mysql_affected_rows',
    'mysql_real_escape_string',
    'mysql_escape_string',
    'mysql_error',
    'mysql_errno',
    'mysql_close',
    'db_query',
    'db_query_params',
    'db_prepare',
    'mysqli_prepare',
);

$counts = array_fill_keys($tracked, 0);
$files = array_fill_keys($tracked, array());
$phpFiles = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }

    $path = $file->getRealPath();
    if (in_array($path, $excluded, true)) {
        continue;
    }

    $phpFiles++;
    $source = file_get_contents($path);
    if ($source === false) {
        continue;
    }

    foreach (token_get_all($source) as $token) {
        if (!is_array($token) || $token[0] !== T_STRING) {
            continue;
        }

        $name = strtolower($token[1]);
        if (!array_key_exists($name, $counts)) {
            continue;
        }

        $counts[$name]++;
        $files[$name][$path] = true;
    }
}

$legacyCalls = 0;
$legacyFiles = array();
foreach ($tracked as $name) {
    if (strpos($name, 'mysql_') !== 0) {
        continue;
    }
    $legacyCalls += $counts[$name];
    $legacyFiles += $files[$name];
}

$report = array(
    'php_files_scanned' => $phpFiles,
    'executable_mysql_calls' => $legacyCalls,
    'executable_mysql_files' => count($legacyFiles),
    'mysqli_adapter' => array(
        'db_query' => $counts['db_query'],
        'db_query_files' => count($files['db_query']),
        'db_query_params' => $counts['db_query_params'],
        'db_query_params_files' => count($files['db_query_params']),
        'db_prepare' => $counts['db_prepare'],
        'mysqli_prepare' => $counts['mysqli_prepare'],
    ),
);

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
