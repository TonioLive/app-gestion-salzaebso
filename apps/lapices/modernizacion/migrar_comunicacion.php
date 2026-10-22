<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/configuracion/config.php';
$sql = file_get_contents(__DIR__ . '/sql/004_avisos_chat_equipo.sql');
foreach (explode(';', $sql) as $statement) {
    if (trim($statement) !== '' && !db_query($statement)) {
        fwrite(STDERR, "No se ha podido preparar la comunicación del centro.\n");
        exit(1);
    }
}
echo "Tablas de avisos y chat preparadas.\n";
