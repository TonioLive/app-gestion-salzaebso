<?php
/**
 * Hardens the shared CP1252 configuration files with exact, byte-preserving
 * replacements. The operation is idempotent.
 */

$files = array(
    dirname(__DIR__) . '/configuracion/config.php',
    dirname(__DIR__) . '/configuracion/configuracion/config.php',
);

$replacements = array(
    '$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$_SESSION[\'centro_actual\']);' => <<<'PHP'
$centro_sesion=(int) $_SESSION['centro_actual'];
                                        $busca_datos_centro=db_query_params(
                                                "SELECT * FROM centros WHERE id_centro=?",
                                                "i",
                                                array($centro_sesion),
                                                $db_validacion
                                        );
PHP
,
    '$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=".$centro_enviado.$filtro_centro);' => <<<'PHP'
$sql_cambio_centro="SELECT * FROM centros WHERE id_centro=?";
                                if ($filtro_centro!=="") {
                                        $sql_cambio_centro.=" AND mostrar_en_lapices=1";
                                }
                                $busca_datos_centro=db_query_params(
                                        $sql_cambio_centro,
                                        "i",
                                        array($centro_enviado),
                                        $db_cambio_centro
                                );
PHP
,
    '$busca_datos_centro=db_query("SELECT * FROM centros WHERE id_centro=$centro_enviado");' => <<<'PHP'
$busca_datos_centro=db_query_params(
                                        "SELECT * FROM centros WHERE id_centro=?",
                                        "i",
                                        array((int) $centro_enviado),
                                        $db_validacion
                                );
PHP
,
    'db_query("UPDATE control_acceso_padres SET logacceso=CONCAT(logacceso, \'$string_log\') WHERE sesion=\'".session_id()."\'");' => <<<'PHP'
db_query_params(
                "UPDATE control_acceso_padres SET logacceso=CONCAT(COALESCE(logacceso, ''), ?) WHERE sesion=?",
                "ss",
                array($string_log, session_id())
        );
PHP
,
);

$changedFiles = 0;
$changedBlocks = 0;

foreach ($files as $path) {
    $source = file_get_contents($path);
    if ($source === false) {
        fwrite(STDERR, "No se ha podido leer: {$path}\n");
        exit(1);
    }

    $updated = $source;
    $identifierValidation = <<<'PHP'
if (!preg_match('/^[A-Za-z0-9_]+$/', $table) || !preg_match('/^[A-Za-z0-9_]+$/', $field)) {
        return array();
    }
    
PHP;
    $updated = str_replace(
        $identifierValidation . $identifierValidation,
        $identifierValidation,
        $updated,
        $cleanupCount
    );
    $changedBlocks += $cleanupCount;
    foreach ($replacements as $old => $new) {
        if (strpos($updated, $old) === false) {
            continue;
        }

        $updated = str_replace($old, $new, $updated, $count);
        $changedBlocks += $count;
    }

    if ($updated === $source) {
        continue;
    }

    if (file_put_contents($path, $updated) === false) {
        fwrite(STDERR, "No se ha podido escribir: {$path}\n");
        exit(2);
    }
    $changedFiles++;
}

echo json_encode(array(
    'files_changed' => $changedFiles,
    'blocks_changed' => $changedBlocks,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
