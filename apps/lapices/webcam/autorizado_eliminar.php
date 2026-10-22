<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/configuracion/autentifica_padre.php';

$id_autorizado = (int) app_request_value($_GET, 'id_autorizado', 0);
$id_hijo = (int) app_request_value($_GET, 'id_hijo', 0);
if ($id_autorizado <= 0 || $id_hijo <= 0) {
    http_response_code(400);
    exit('Datos de la solicitud no validos.');
}

$propietario = db_query_params(
    'SELECT id_padre FROM padres WHERE id_usuario=? AND padre_de=? AND parentesco=1 LIMIT 1',
    'ii',
    array((int) $id_usuario, $id_hijo)
);
if (db_num_rows($propietario) === 0) {
    http_response_code(403);
    exit('No tienes permiso para gestionar las personas autorizadas de este alumno.');
}

$objetivo = db_query_params(
    'SELECT id_usuario, parentesco FROM padres WHERE id_padre=? AND padre_de=? LIMIT 1',
    'ii',
    array($id_autorizado, $id_hijo)
);
if (db_num_rows($objetivo) === 0) {
    http_response_code(404);
    exit('La persona autorizada no existe.');
}
$datos_objetivo = db_fetch_assoc($objetivo);
$id_usuario_objetivo = (int) $datos_objetivo['id_usuario'];
if ((int) $datos_objetivo['parentesco'] === 1 || $id_usuario_objetivo === (int) $id_usuario) {
    http_response_code(403);
    exit('No se puede eliminar a un padre o madre desde esta opcion.');
}

db_query_params(
    'DELETE FROM padres WHERE id_padre=? AND padre_de=? AND id_usuario=? AND parentesco<>1',
    'iii',
    array($id_autorizado, $id_hijo, $id_usuario_objetivo)
);
db_query_params(
    'DELETE FROM usuarios WHERE id_usuario=? AND NOT EXISTS (SELECT 1 FROM padres WHERE padres.id_usuario=usuarios.id_usuario)',
    'i',
    array($id_usuario_objetivo)
);

$nombre_archivo = 'dni_usu' . $id_usuario_objetivo . '.jpg';
$ruta_origen = rtrim($upload_path_dni, '/\\') . DIRECTORY_SEPARATOR . $nombre_archivo;
if (is_file($ruta_origen)) {
    $ruta_respaldo = rtrim($upload_path_dni, '/\\') . DIRECTORY_SEPARATOR
        . 'dni_usu' . $id_usuario_objetivo . '_eliminado_' . date('YmdHis') . '.jpg';
    @rename($ruta_origen, $ruta_respaldo);
}

header('Location: autorizados.php?id_usuario=' . (int) $id_usuario . '&id_usuhijo=' . $id_hijo);
exit;
