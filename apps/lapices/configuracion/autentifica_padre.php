<?php 
if (empty($_SESSION['permitido_padre'])) {
        header("Location: ".$url_web."/configuracion/acceso_denegado_padre.htm");
        exit;
        } else {
        $nombre_usuario=(string) app_request_value($_SESSION, 'nombre_padre', '');

        // Busca usuario

        $busca_usuario=db_query_params(
                "SELECT * FROM padres WHERE usuario_webcam=? LIMIT 1",
                "s",
                array($nombre_usuario)
        );

        if (db_num_rows($busca_usuario)==0) {
                echo("El nombre de usuario no existe.");
                die();
                } else {
                $datos_usuario=db_fetch_array($busca_usuario);
                $id_usuario=$datos_usuario["id_usuario"];

                // Busca datos del padre.
                $busca_datos_padre=db_query_params("SELECT * FROM usuarios WHERE id_usuario=? LIMIT 1", "i", array((int) $id_usuario));

                $datos_padre=db_fetch_array($busca_datos_padre);
                if ($datos_padre["sexo"]=="V") {
                        $sufijo_padre="o";
                        } else {
                        $sufijo_padre="a";
                        }
                }
        }

/*
 * Proteccion transversal para las pantallas privadas heredadas de familias.
 * Los formularios renovados pueden enviar _csrf; los antiguos se mantienen
 * operativos siempre que la peticion proceda realmente del mismo sitio.
 */
$app_parent_method = strtoupper((string) app_request_value($_SERVER, 'REQUEST_METHOD', 'GET'));
$app_parent_path = (string) parse_url((string) app_request_value($_SERVER, 'REQUEST_URI', '/'), PHP_URL_PATH);
$app_parent_basename = strtolower((string) basename($app_parent_path));
$app_parent_unsafe = !in_array($app_parent_method, array('GET', 'HEAD', 'OPTIONS'), true);
$app_parent_write_get = $app_parent_method === 'GET' && preg_match(
        '/(?:eliminar|borrar|quitar|anular|guardar|guarda|actualiza|editar_.+1)/',
        $app_parent_basename
);

if ($app_parent_unsafe || $app_parent_write_get) {
        $app_parent_source = $app_parent_method === 'GET' ? $_GET : $_POST;
        $app_parent_token = (string) app_request_value($app_parent_source, '_csrf', '');
        if (!app_verify_csrf($app_parent_token) && !app_request_is_same_origin()) {
                http_response_code(403);
                exit('Solicitud bloqueada por seguridad. Recarga la pantalla e intentalo de nuevo.');
        }
}
?>
