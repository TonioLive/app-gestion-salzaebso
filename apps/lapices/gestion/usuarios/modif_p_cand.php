<?php
require '../../configuracion/config.php';
require '../../configuracion/autentifica.php';
require '../../configuracion/autentifica_admin.php';
require_once __DIR__ . '/acceso_usuario_lib.php';
require_once __DIR__ . '/password_consulta_lib.php';
header('Content-Type: text/html; charset=UTF-8');
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Utiliza el formulario de gestión de acceso.'); }
    if (!app_verify_csrf((string) app_request_value($_POST, '_csrf', ''))) { http_response_code(419); exit('Recarga el formulario para actualizar tu sesión.'); }
    $id = app_request_int($_POST, 'id_tabla_usu', 0);
    if ($id !== app_request_int($_GET, 'Cod', 0)) { throw new InvalidArgumentException('El usuario no coincide con el formulario.'); }
    $actual = gestion_acceso_usuario($id);
    $administrador = (int) $nivel_acceso >= 4;
    $usuario = trim((string) app_request_value($_POST, 'usuario', ''));
    if ($usuario === '' || strlen($usuario) > 50 || !preg_match('/^[a-zA-Z0-9@._-]+$/D', $usuario)) {
        throw new InvalidArgumentException('Escribe un nombre de usuario válido, de hasta 50 caracteres.');
    }
    $nivel = $administrador ? app_request_int($_POST, 'nivel_acceso', (int) $actual['nivel_acceso']) : (int) $actual['nivel_acceso'];
    $centro = $administrador ? app_request_int($_POST, 'id_centro_usuario', 0) : (int) $actual['id_centro'];
    if (!in_array($nivel, array(2,3,4,10,11), true)) { throw new InvalidArgumentException('Selecciona un nivel de acceso válido.'); }
    if ($nivel <= 3 && $centro <= 0) { throw new InvalidArgumentException('Selecciona el centro de este usuario.'); }
    if ($centro > 0) {
        $centros = db_query_params('SELECT id_centro FROM centros WHERE id_centro=?', 'i', array($centro));
        if (!$centros || !db_fetch_assoc($centros)) { throw new InvalidArgumentException('El centro seleccionado no existe.'); }
    }
    $duplicados = db_query_params('SELECT id_usuario FROM usuarios WHERE usuario=? AND id_usuario<>? LIMIT 1', 'si', array($usuario,$id));
    if (!$duplicados) { throw new RuntimeException('No se ha podido comprobar el nombre de usuario.'); }
    if (db_fetch_assoc($duplicados)) { throw new InvalidArgumentException('Ese nombre de usuario ya está en uso.'); }
    $password = gestion_acceso_password(app_request_value($_POST,'password',''),$actual['pass']);
    if ($password === '') { throw new InvalidArgumentException('Este usuario todavía no tiene contraseña. Introduce una para configurar su acceso.'); }
    $campos = array('usuario=?','pass=?','nivel_acceso=?','id_centro=?');
    $tipos = 'ssii'; $valores = array($usuario,$password,$nivel,$centro);
    $nueva = (string) app_request_value($_POST,'password','');
    if ($nueva !== '' && $nueva !== '******') {
        if ($administrador) {
            gestion_password_admin();
            $copia = gestion_password_seal($id,$nueva,$password,gestion_password_key());
            $campos[] = 'pass_consulta=?'; $campos[] = 'pass_consulta_hash=?';
            $tipos .= 'ss'; $valores[] = $copia; $valores[] = hash('sha256',$password);
        } else {
            $campos[] = 'pass_consulta=NULL'; $campos[] = 'pass_consulta_hash=NULL';
        }
    }
    if ($administrador) {
        foreach (array('multicentro','zona_alumnos','zona_aulas','zona_facturacion','zona_comedor','zona_almacen','zona_incidencias','zona_personal','zona_pedidos','zona_inferior','zona_mensajes','zona_compras','zona_gabinete','zona_corregir_pruebas','zona_actividades') as $campo) {
            $campos[] = $campo . '=?'; $tipos .= 'i'; $valores[] = app_request_int($_POST,$campo,0) ? 1 : 0;
        }
    }
    $tipos .= 'i'; $valores[] = $id;
    if (!db_query_params('UPDATE usuarios SET '.implode(',',$campos).' WHERE id_usuario=?',$tipos,$valores)) {
        throw new RuntimeException('No se han podido guardar los cambios. Inténtalo de nuevo.');
    }
    header('Location: datos_cand.php?dato=s&id_usu='.$id);
    exit;
} catch (DomainException $error) { http_response_code(403); }
catch (InvalidArgumentException $error) { http_response_code(400); }
catch (Exception $error) { http_response_code(500); }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Revisar acceso · Lápices</title></head><body><main><h1>No se han guardado los cambios</h1><p><?= app_escape($error->getMessage()); ?></p><button type="button" onclick="history.back()">Volver al formulario</button></main></body></html>
