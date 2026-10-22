<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/configuracion/autentifica_padre.php';

if (strtoupper((string) app_request_value($_SERVER, 'REQUEST_METHOD', 'GET')) !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metodo no permitido.');
}

$id_autorizado = (int) app_request_value($_POST, 'id_autorizado', 0);
$id_usu_autorizado = (int) app_request_value($_POST, 'id_usu_autorizado', 0);
$id_hijo = (int) app_request_value($_POST, 'id_hijo', 0);

if ($id_hijo <= 0 || $id_autorizado < 0 || $id_usu_autorizado < 0) {
    http_response_code(400);
    exit('Datos de la solicitud no validos.');
}

/* Solo un padre/madre relacionado con el alumno puede gestionar autorizados. */
$busca_datos_padre = db_query_params(
    'SELECT id_padre FROM padres WHERE id_usuario=? AND padre_de=? AND parentesco=1 LIMIT 1',
    'ii',
    array((int) $id_usuario, $id_hijo)
);
if (db_num_rows($busca_datos_padre) === 0) {
    http_response_code(403);
    exit('No tienes permiso para gestionar las personas autorizadas de este alumno.');
}

/* En edicion, la relacion, el usuario autorizado y el alumno deben coincidir. */
if ($id_autorizado > 0 || $id_usu_autorizado > 0) {
    if ($id_autorizado <= 0 || $id_usu_autorizado <= 0) {
        http_response_code(400);
        exit('La persona autorizada indicada no es valida.');
    }
    $relacion = db_query_params(
        'SELECT parentesco FROM padres WHERE id_padre=? AND id_usuario=? AND padre_de=? LIMIT 1',
        'iii',
        array($id_autorizado, $id_usu_autorizado, $id_hijo)
    );
    if (db_num_rows($relacion) === 0) {
        http_response_code(403);
        exit('No tienes permiso para modificar esta persona autorizada.');
    }
    $datos_relacion = db_fetch_assoc($relacion);
    if ((int) $datos_relacion['parentesco'] === 1 && $id_usu_autorizado !== (int) $id_usuario) {
        http_response_code(403);
        exit('No tienes permiso para modificar los datos de otro padre o madre.');
    }
}

$usuario_nombre = trim((string) app_request_value($_POST, 'Nombre', ''));
$usuario_apellido1 = trim((string) app_request_value($_POST, 'Apellido1', ''));
$usuario_apellido2 = trim((string) app_request_value($_POST, 'Apellido2', ''));
$usuario_sexo = trim((string) app_request_value($_POST, 'sexo', ''));
$usuario_direccion = trim((string) app_request_value($_POST, 'Direccion', ''));
$usuario_DNI = trim((string) app_request_value($_POST, 'DNI', ''));
$usuario_profesion = trim((string) app_request_value($_POST, 'profesion', ''));
$usuario_cp = trim((string) app_request_value($_POST, 'CP', ''));
$usuario_poblacion = trim((string) app_request_value($_POST, 'poblacion', ''));
$usuario_provincia = trim((string) app_request_value($_POST, 'provincia', ''));
$usuario_telefono1 = trim((string) app_request_value($_POST, 'telefono1', ''));
$usuario_telefono2 = trim((string) app_request_value($_POST, 'telefono2', ''));
$usuario_movil = trim((string) app_request_value($_POST, 'movil', ''));
$usuario_email = trim((string) app_request_value($_POST, 'email', ''));
$parentesco = (int) app_request_value($_POST, 'parentesco', 0);

if ($usuario_nombre === '' || $parentesco < 1 || $parentesco > 4) {
    http_response_code(422);
    exit('Indica al menos el nombre y una relacion de parentesco valida.');
}
if ($usuario_email !== '' && !filter_var($usuario_email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    exit('La direccion de correo electronico no es valida.');
}

if ($id_usu_autorizado === 0) {
    db_query_params(
        'INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) VALUES (2, ?, 1)',
        's',
        array(date('Y-m-d'))
    );
    $id_usu_autorizado = (int) db_insert_id();

    $base_usuario = strtoupper(substr($usuario_nombre, 0, 2) . substr($usuario_apellido1, 0, 2) . substr($usuario_apellido2, 0, 2));
    $base_usuario = preg_replace('/[^A-Z0-9]/', '', $base_usuario);
    if ($base_usuario === '') {
        $base_usuario = 'FAMILIA';
    }

    $nombre_usu = $base_usuario;
    $contador = 0;
    do {
        $candidato = $contador === 0 ? $nombre_usu : $nombre_usu . $contador;
        $consulta_nombre_usu = db_query_params(
            'SELECT id_padre FROM padres WHERE usuario_webcam=? LIMIT 1',
            's',
            array($candidato)
        );
        $existe = db_num_rows($consulta_nombre_usu) > 0;
        $contador++;
    } while ($existe && $contador < 10000);

    if ($existe) {
        http_response_code(409);
        exit('No se ha podido generar un usuario unico. Intentalo de nuevo.');
    }
    $nombre_usu = $candidato;

    if (function_exists('random_bytes')) {
        $password_temporal = substr(bin2hex(random_bytes(8)), 0, 10);
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $password_temporal = substr(bin2hex(openssl_random_pseudo_bytes(8)), 0, 10);
    } else {
        $password_temporal = substr(hash('sha256', uniqid(mt_rand(), true)), 0, 10);
    }
    $password_hash = app_password_hash_if_needed($password_temporal);

    db_query_params(
        'INSERT INTO padres (id_usuario, padre_de, parentesco, usuario_webcam, password_webcam) VALUES (?, ?, ?, ?, ?)',
        'iiiss',
        array($id_usu_autorizado, $id_hijo, $parentesco, $nombre_usu, $password_hash)
    );
    $id_autorizado = (int) db_insert_id();

    /* La clave en claro solo vive en la sesion durante cinco minutos. */
    $_SESSION['credencial_familiar_temporal'] = array(
        'usuario' => $nombre_usu,
        'password' => $password_temporal,
        'expires' => time() + 300,
    );
}

db_query_params(
    'UPDATE usuarios SET Nombre=?, Apellido1=?, Apellido2=?, sexo=?, DNI=?, profesion=?, Direccion=?, cp=?, Poblacion=?, Provincia=?, telefono1=?, telefono2=?, movil=?, email=? WHERE id_usuario=?',
    'ssssssssssssssi',
    array(
        $usuario_nombre,
        $usuario_apellido1,
        $usuario_apellido2,
        $usuario_sexo,
        $usuario_DNI,
        $usuario_profesion,
        $usuario_direccion,
        $usuario_cp,
        $usuario_poblacion,
        $usuario_provincia,
        $usuario_telefono1,
        $usuario_telefono2,
        $usuario_movil,
        $usuario_email,
        $id_usu_autorizado,
    )
);
db_query_params(
    'UPDATE padres SET parentesco=? WHERE id_padre=? AND id_usuario=? AND padre_de=?',
    'iiii',
    array($parentesco, $id_autorizado, $id_usu_autorizado, $id_hijo)
);

header('Location: autorizados.php?id_usuario=' . (int) $id_usuario . '&id_usuhijo=' . $id_hijo);
exit;
