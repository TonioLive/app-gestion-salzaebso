<?php
/**
 * Migra credenciales heredadas legibles a bcrypt de forma reanudable.
 * No imprime ni guarda nunca las contraseñas originales.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

ini_set('session.save_path', sys_get_temp_dir());
require_once dirname(__DIR__) . '/configuracion/config.php';

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    fwrite(STDERR, "No se ha podido conectar con la base de datos.\n");
    exit(1);
}

$schemaChanges = array(
    "ALTER TABLE usuarios MODIFY pass VARCHAR(255) NOT NULL DEFAULT ''",
    "ALTER TABLE padres MODIFY password_webcam VARCHAR(255) NOT NULL DEFAULT ''",
);
foreach ($schemaChanges as $schemaChange) {
    if (!mysqli_query($database, $schemaChange)) {
        fwrite(STDERR, "No se ha podido ampliar una columna de credenciales.\n");
        exit(1);
    }
}

function migrate_password_table($database, $table, $idColumn, $passwordColumn)
{
    $allowed = array(
        'usuarios.id_usuario.pass',
        'padres.id_padre.password_webcam',
    );
    if (!in_array($table . '.' . $idColumn . '.' . $passwordColumn, $allowed, true)) {
        throw new RuntimeException('Tabla de migración no permitida.');
    }

    $result = mysqli_query(
        $database,
        "SELECT `$idColumn` AS id, `$passwordColumn` AS password_value
         FROM `$table` WHERE `$passwordColumn`<>''"
    );
    if (!$result) {
        throw new RuntimeException('No se pueden leer las credenciales de ' . $table . '.');
    }

    $update = mysqli_prepare(
        $database,
        "UPDATE `$table` SET `$passwordColumn`=? WHERE `$idColumn`=?"
    );
    if (!$update) {
        throw new RuntimeException('No se puede preparar la actualización de ' . $table . '.');
    }

    $migrated = 0;
    $alreadySecure = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $stored = (string) $row['password_value'];
        $info = password_get_info($stored);
        if (!empty($info['algo'])) {
            $alreadySecure++;
            continue;
        }

        $hash = password_hash($stored, PASSWORD_BCRYPT, array('cost' => 10));
        $id = (int) $row['id'];
        if ($hash === false) {
            throw new RuntimeException('No se ha podido cifrar una credencial de ' . $table . '.');
        }
        mysqli_stmt_bind_param($update, 'si', $hash, $id);
        if (!mysqli_stmt_execute($update)) {
            throw new RuntimeException('No se ha podido guardar una credencial de ' . $table . '.');
        }
        $migrated++;
        if ($migrated % 250 === 0) {
            echo $table . ': ' . $migrated . " credenciales protegidas.\n";
        }
    }
    mysqli_free_result($result);
    mysqli_stmt_close($update);
    echo $table . ': ' . $migrated . ' migradas, ' . $alreadySecure . " ya estaban protegidas.\n";
}

try {
    migrate_password_table($database, 'usuarios', 'id_usuario', 'pass');
    migrate_password_table($database, 'padres', 'id_padre', 'password_webcam');
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}

echo "Migración de credenciales completada.\n";
