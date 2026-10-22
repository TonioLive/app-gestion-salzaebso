<?php
require __DIR__.'/../../configuracion/config.php';
require __DIR__.'/../../configuracion/autentifica_admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit; }
app_check_csrf();
function credential_input($name) { return isset($_POST[$name]) && is_string($_POST[$name]) ? $_POST[$name] : ''; }
function credential_error($message, $status = 422) {
    http_response_code($status);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Acceso del usuario</title></head><body><main><h1>No se han guardado los cambios</h1><p>'.app_escape($message).'</p><p><a href="javascript:history.back()">Volver al formulario</a></p></main></body></html>';
    exit;
}
$id = filter_var(credential_input('id_tabla_usu'), FILTER_VALIDATE_INT, array('options'=>array('min_range'=>1)));
$username = trim(credential_input('usuario'));
$password = credential_input('password');
$role = filter_var(credential_input('nivel_acceso'), FILTER_VALIDATE_INT);
$centreText = credential_input('id_centro_usuario');
$centre = $centreText === '' ? 0 : filter_var($centreText, FILTER_VALIDATE_INT, array('options'=>array('min_range'=>0)));
if (!$id || !in_array($role, array(2,3,4,10,11), true) || $centre === false) credential_error('Revisa el usuario, el centro y el nivel de acceso.');
if (!preg_match('/\A[A-Za-z0-9_.@-]{1,20}\z/', $username)) credential_error('El nombre de usuario debe tener entre 1 y 20 letras, números, puntos, guiones o @.');
$rows = app_rows('SELECT id_usuario, pass FROM usuarios WHERE id_usuario=?', 'i', array($id));
if (!$rows) credential_error('El usuario ya no existe.', 404);
$changePassword = $password !== '' && $password !== '******';
if ($changePassword && (strlen($password)<8 || strlen($password)>72 || strpos($password, "\0")!==false)) credential_error('La nueva contraseña debe tener entre 8 y 72 caracteres.');
if (!$changePassword && (string)$rows[0]['pass'] === '') credential_error('Introduce una contraseña para habilitar el acceso del usuario.');
if ($role === 2 && !$centre) credential_error('Selecciona el centro del educador o educadora.');
if ($centre && !app_rows('SELECT id_centro FROM centros WHERE id_centro=?', 'i', array($centre))) credential_error('El centro seleccionado no existe.');
if (app_rows('SELECT id_usuario FROM usuarios WHERE usuario=? AND id_usuario<>? LIMIT 1', 'si', array($username,$id))) credential_error('Ese nombre de usuario ya está asignado a otra cuenta.', 409);

// Only server-defined columns become SQL; all submitted values are bound separately.
$sets = array('usuario=?', 'nivel_acceso=?', 'id_centro=?');
$values = array($username,$role,$centre);
$types = 'sii';
foreach (array('multicentro','zona_alumnos','zona_aulas','zona_facturacion','zona_comedor','zona_almacen','zona_incidencias','zona_personal','zona_pedidos','zona_inferior','zona_mensajes','zona_compras','zona_gabinete','zona_corregir_pruebas','zona_actividades') as $field) {
    $sets[] = $field.'=?'; $values[] = credential_input($field)==='1' ? 1 : 0; $types .= 'i';
}
if ($changePassword) { $sets[]='pass=?'; $values[]=security_new_password($password); $types.='s'; }
$values[]=$id; $types.='i';
if (!db_query_params('UPDATE usuarios SET '.implode(', ', $sets).' WHERE id_usuario=?', $types, $values, app_db())) credential_error('No se han podido guardar los cambios. Inténtalo de nuevo.', 500);
header('Location: datos_cand.php?dato=s&id_usu='.$id, true, 303);
exit;
