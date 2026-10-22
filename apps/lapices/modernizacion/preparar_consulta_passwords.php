<?php
// Local CLI setup. Never converts a hash, reads original passwords or changes a login.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/gestion/usuarios/password_consulta_lib.php';
try {
    $path = gestion_password_key_path();
    if (!is_dir(dirname($path)) && !mkdir(dirname($path),0700,true)) { throw new RuntimeException('No se ha podido crear el directorio privado.'); }
    if (!file_exists($path)) {
        $key = gestion_password_random(64);
        $handle = fopen($path,'xb');
        if (!$handle) { throw new RuntimeException('No se ha podido crear la clave privada.'); }
        $written = fwrite($handle,$key); fclose($handle); @chmod($path,0600);
        if ($written !== 64) { throw new RuntimeException('La clave privada está incompleta.'); }
    }
    gestion_password_key();
    foreach (array('pass_consulta'=>'TEXT CHARACTER SET ascii COLLATE ascii_bin NULL','pass_consulta_hash'=>'CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NULL') as $column=>$type) {
        $found=db_query_params('SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?','ss',array('usuarios',$column));
        if (!$found) { throw new RuntimeException('No se ha podido revisar el esquema.'); }
        if (!db_fetch_row($found) && !db_query('ALTER TABLE usuarios ADD COLUMN '.$column.' '.$type)) { throw new RuntimeException('No se ha podido preparar la columna de consulta.'); }
    }
    if (!db_query("CREATE TABLE IF NOT EXISTS usuarios_password_consulta_log (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, administrador_id INT NOT NULL, evento VARCHAR(16) NOT NULL, cantidad INT NOT NULL DEFAULT 0, fecha DATETIME NOT NULL, INDEX consultas_admin (administrador_id,evento,fecha)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4")) { throw new RuntimeException('No se ha podido preparar el registro de consultas.'); }
    echo "Consulta preparada; cuentas y contraseñas existentes sin cambios.\n";
} catch (Exception $error) { fwrite(STDERR,$error->getMessage()."\n"); exit(1); }
