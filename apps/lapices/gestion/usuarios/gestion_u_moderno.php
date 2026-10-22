<?php
require('../../configuracion/parte_superior_privado.php');

if (empty($permitido)) {
    require('../../configuracion/parte_inferior.php');
    return;
}

require_once dirname(dirname(__DIR__)) . '/gestion/panel/paginacion.php';

$mostrar_todos = app_request_int($_GET, 'f', 0) === 1;
$busqueda_usuarios = trim((string) app_request_value($_GET, 'q', ''));
$por_pagina = app_pagination_per_page($_GET, 'por_pagina');
$pagina = app_pagination_page($_GET, 'pagina');
$condiciones = array('u.tipo_inscripcion>=3');
$tipos = '';
$parametros = array();

if ((int) $nivel_acceso === 3) {
    $condiciones[] = 'u.id_centro=?';
    $tipos .= 'i';
    $parametros[] = (int) $centro_actual;
}

if (!$mostrar_todos) {
    $condiciones[] = 'EXISTS ('
        . 'SELECT 1 FROM personal_contratos pc '
        . 'WHERE pc.id_empleado=u.id_usuario '
        . 'AND ('
        . '(pc.fecha_inicio<=CURDATE() AND pc.fecha_fin>=CURDATE()) '
        . 'OR (pc.fecha_inicio<=CURDATE() AND (pc.fecha_fin IS NULL OR pc.fecha_fin="0000-00-00")) '
        . 'OR pc.fecha_inicio>CURDATE()'
        . ')'
        . ')';
}

if ($busqueda_usuarios !== '') {
    $valor_busqueda = '%' . $busqueda_usuarios . '%';
    $condiciones[] = '('
        . 'u.Nombre LIKE ? OR u.Apellido1 LIKE ? OR u.Apellido2 LIKE ? '
        . 'OR u.email LIKE ? OR u.usuario LIKE ? OR c.nombre_centro LIKE ? '
        . 'OR u.telefono1 LIKE ? OR u.telefono2 LIKE ? OR u.movil LIKE ?'
        . ')';
    $tipos .= 'sssssssss';
    for ($indice_busqueda = 0; $indice_busqueda < 9; $indice_busqueda++) {
        $parametros[] = $valor_busqueda;
    }
}

$campo_password = "CASE WHEN COALESCE(u.pass,'')='' THEN 0 ELSE 1 END AS tiene_contrasena";

$consulta_total_usuarios = db_query_params(
    'SELECT COUNT(*) AS total FROM usuarios u '
        . 'LEFT JOIN centros c ON c.id_centro=u.id_centro '
        . 'WHERE ' . implode(' AND ', $condiciones),
    $tipos,
    $parametros
);
$total_usuarios = 0;
if ($consulta_total_usuarios) {
    $fila_total_usuarios = db_fetch_assoc($consulta_total_usuarios);
    $total_usuarios = (int) $fila_total_usuarios['total'];
}
$total_paginas = max(1, (int) ceil($total_usuarios / $por_pagina));
$pagina = min($pagina, $total_paginas);
$desplazamiento = ($pagina - 1) * $por_pagina;

$consulta_usuarios = db_query_params(
    'SELECT u.id_usuario, u.nivel_acceso, u.fechaalta, u.Nombre, u.Apellido1, '
    . 'u.Apellido2, u.email, u.telefono1, u.telefono2, u.movil, u.usuario, '
    . $campo_password . ', c.nombre_centro, pa.contrato_archivo, pa.pendiente AS perfil_pendiente '
    . 'FROM usuarios u '
    . 'LEFT JOIN centros c ON c.id_centro=u.id_centro '
    . 'LEFT JOIN personal_altas pa ON pa.id_usuario=u.id_usuario '
    . 'WHERE ' . implode(' AND ', $condiciones) . ' '
    . 'ORDER BY u.Apellido1, u.Apellido2, u.Nombre LIMIT ? OFFSET ?',
    $tipos . 'ii',
    array_merge($parametros, array($por_pagina, $desplazamiento))
);

$usuarios = array();
if ($consulta_usuarios) {
    while ($usuario = db_fetch_assoc($consulta_usuarios)) {
        $usuarios[] = $usuario;
    }
}

function gestion_usuario_telefonos($usuario)
{
    $telefonos = array();
    foreach (array('telefono1', 'telefono2', 'movil') as $campo) {
        $telefono = trim((string) $usuario[$campo]);
        if ($telefono !== '' && !in_array($telefono, $telefonos, true)) {
            $telefonos[] = $telefono;
        }
    }

    return implode(' / ', $telefonos);
}

function gestion_usuario_texto_utf8($valor)
{
    $valor = (string) $valor;
    if ($valor === '') {
        return '';
    }

    if (function_exists('mb_check_encoding') && !mb_check_encoding($valor, 'UTF-8')) {
        return mb_convert_encoding($valor, 'UTF-8', 'Windows-1252');
    }

    if (!function_exists('mb_check_encoding') && !preg_match('//u', $valor)) {
        $convertido = @iconv('Windows-1252', 'UTF-8//TRANSLIT', $valor);
        return $convertido === false ? $valor : $convertido;
    }

    return $valor;
}

function gestion_usuario_escape($valor)
{
    return app_escape(gestion_usuario_texto_utf8($valor));
}

function gestion_usuario_nombre($usuario)
{
    $partes = array();
    foreach (array('Nombre', 'Apellido1', 'Apellido2') as $campo) {
        $parte = trim(gestion_usuario_texto_utf8($usuario[$campo]));
        if ($parte !== '') {
            $partes[] = $parte;
        }
    }

    return empty($partes) ? 'Sin nombre registrado' : implode(' ', $partes);
}
?>
<section class="legacy-module-page legacy-users-page" aria-labelledby="gestion-usuarios-title">
    <header class="legacy-module-page__heading">
        <div>
            <span class="legacy-module-page__eyebrow">Personal</span>
            <h1 id="gestion-usuarios-title">Usuarios de gestión</h1>
            <p>Personal con acceso al programa, presentado en un listado adaptable a toda la pantalla.</p>
        </div>
        <?php if ((int) $nivel_acceso >= 4) { ?>
            <a class="legacy-card-action legacy-card-action--primary legacy-heading-action" href="/gestion/usuarios/nuevo_usuario.php">
                Nuevo usuario
            </a>
        <?php } ?>
    </header>

    <div class="legacy-results-heading">
        <div>
            <strong><?= $mostrar_todos ? 'Listado completo' : 'Personal activo o con contrato próximo'; ?></strong>
            <span><?= (int) $total_usuarios; ?> usuario(s)</span>
        </div>
        <a class="legacy-card-action" href="?f=<?= $mostrar_todos ? '0' : '1'; ?>&amp;por_pagina=<?= (int) $por_pagina; ?>">
            <?= $mostrar_todos ? 'Ver solo personal activo' : 'Ver listado completo'; ?>
        </a>
    </div>

    <?php if (empty($usuarios)) { ?>
        <div class="legacy-empty-state">
            <strong>No hay usuarios para este filtro</strong>
            <p>Cambia el filtro o revisa el centro seleccionado.</p>
        </div>
    <?php } else { ?>
        <div class="legacy-users-toolbar">
            <form class="legacy-users-search" method="get" action="/gestion/usuarios/gestion_u.php">
                <input type="hidden" name="f" value="<?= $mostrar_todos ? '1' : '0'; ?>">
                <input type="hidden" name="por_pagina" value="<?= (int) $por_pagina; ?>">
                <span>Buscar en el listado</span>
                <div class="legacy-users-search__controls">
                    <input
                        id="gestion-usuarios-busqueda"
                        type="search"
                        name="q"
                        value="<?= gestion_usuario_escape($busqueda_usuarios); ?>"
                        placeholder="Nombre, correo, usuario, centro..."
                        autocomplete="off"
                    >
                    <button type="submit" class="legacy-card-action legacy-card-action--primary">Buscar</button>
                    <?php if ($busqueda_usuarios !== '') { ?>
                        <a class="legacy-card-action" href="?f=<?= $mostrar_todos ? '1' : '0'; ?>&amp;por_pagina=<?= (int) $por_pagina; ?>">Limpiar</a>
                    <?php } ?>
                </div>
            </form>
            <?php if ((int) $nivel_acceso === 4) { ?>
                <button
                    class="legacy-card-action legacy-users-passwords-toggle"
                    type="button"
                    data-password-help-toggle
                    aria-expanded="false"
                    aria-controls="gestion-password-help"
                >Mostrar contraseñas</button>
            <?php } ?>
        </div>
        <div id="gestion-password-help" class="legacy-password-help" hidden>
            <strong>Consulta de contraseñas del listado</strong>
            <p>Las contraseñas nuevas asignadas desde «Gestionar acceso» por un administrador se pueden consultar aquí. Las anteriores solo tienen hash y aparecerán como «No disponible para consulta».</p>
            <?php if ((int) $nivel_acceso === 4) { ?>
            <form data-password-reveal-form action="/gestion/usuarios/consultar_passwords.php" method="post" autocomplete="off">
                <input type="hidden" name="_csrf" value="<?= app_escape(app_csrf_token()); ?>">
                <label for="confirmacion-password-admin">Confirma tu contraseña de administrador</label>
                <div class="legacy-password-confirm">
                    <input type="password" id="confirmacion-password-admin" name="confirmacion" autocomplete="current-password" required maxlength="255">
                    <button class="legacy-card-action" type="submit">Confirmar y mostrar</button>
                    <button class="legacy-card-action" type="button" data-password-hide>Ocultar</button>
                </div>
                <p data-password-feedback role="status" aria-live="polite">Se mostrarán durante un minuto y se ocultarán al salir de esta pestaña.</p>
            </form>
            <?php } ?>
        </div>

        <div class="legacy-data-table-wrap legacy-users-table-wrap">
            <table class="legacy-data-table legacy-users-table" data-pagination="server">
                <colgroup>
                    <col class="legacy-users-table__personal">
                    <col class="legacy-users-table__type">
                    <col class="legacy-users-table__centre">
                    <col class="legacy-users-table__contact">
                    <col class="legacy-users-table__username">
                    <col class="legacy-users-table__password">
                    <col class="legacy-users-table__date">
                    <col class="legacy-users-table__actions">
                </colgroup>
                <thead>
                    <tr>
                        <th>Personal</th>
                        <th>Tipo</th>
                        <th>Centro</th>
                        <th>Contacto</th>
                        <th>Usuario</th>
                        <th>Estado de contraseña</th>
                        <th>Alta</th>
                        <th><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario) {
                        $codigo = (int) $usuario['id_usuario'];
                        $nombre_usuario = gestion_usuario_nombre($usuario);
                        $telefonos = gestion_usuario_telefonos($usuario);
                        $tipo = isset($tipo_usuario[(int) $usuario['nivel_acceso']])
                            ? $tipo_usuario[(int) $usuario['nivel_acceso']]
                            : 'Personal';
                        $fecha_alta = '';
                        if (!empty($usuario['fechaalta']) && $usuario['fechaalta'] !== '0000-00-00') {
                            $fecha_alta = date('d/m/Y', strtotime($usuario['fechaalta']));
                        }
                        $texto_busqueda = implode(' ', array(
                            $nombre_usuario,
                            gestion_usuario_texto_utf8($usuario['email']),
                            gestion_usuario_texto_utf8($usuario['usuario']),
                            gestion_usuario_texto_utf8($usuario['nombre_centro']),
                            gestion_usuario_texto_utf8($tipo),
                            gestion_usuario_texto_utf8($telefonos),
                        ));
                        ?>
                        <tr data-user-row data-user-id="<?= $codigo; ?>" data-search="<?= gestion_usuario_escape($texto_busqueda); ?>">
                            <td data-label="Personal">
                                <a
                                    class="legacy-student-name"
                                    href="#"
                                    onclick="window.open('/gestion/usuarios/datos_cand.php?dato=s&amp;id_usu=<?= $codigo; ?>','Datos_usuario_<?= $codigo; ?>','toolbar=no,scrollbars=yes,resizable=yes,width=760,height=700'); return false;"
                                ><?= gestion_usuario_escape($nombre_usuario); ?></a>
                                <?php if ($usuario['email'] !== '') { ?>
                                    <small><a href="mailto:<?= gestion_usuario_escape($usuario['email']); ?>"><?= gestion_usuario_escape($usuario['email']); ?></a></small>
                                <?php } ?>
                            </td>
                            <td data-label="Tipo"><span class="legacy-table-tag"><?= gestion_usuario_escape($tipo); ?></span></td>
                            <td data-label="Centro"><?= gestion_usuario_escape($usuario['nombre_centro']); ?></td>
                            <td data-label="Contacto"><?= gestion_usuario_escape($telefonos); ?></td>
                            <td data-label="Usuario"><strong class="legacy-users-username"><?= gestion_usuario_escape($usuario['usuario']); ?></strong></td>
                            <td data-label="Estado de contraseña">
                                <span class="legacy-password-status"><?= !empty($usuario['tiene_contrasena']) ? 'Configurada' : 'Sin configurar'; ?></span>
                                <?php if ((int) $nivel_acceso === 4) { ?>
                                    <span class="legacy-password-reveal" data-password-value hidden></span>
                                <?php } ?>
                                <?php if ((int) $nivel_acceso >= 4) { ?>
                                    <small><a href="/gestion/usuarios/config_cand.php?cod=<?= $codigo; ?>">Gestionar acceso</a></small>
                                <?php } ?>
                            </td>
                            <td data-label="Alta"><?= gestion_usuario_escape($fecha_alta); ?></td>
                            <td data-label="Acciones" class="legacy-data-table__actions">
                                <a
                                    href="#"
                                    onclick="window.open('/gestion/usuarios/datos_cand.php?dato=s&amp;id_usu=<?= $codigo; ?>','Datos_usuario_<?= $codigo; ?>','toolbar=no,scrollbars=yes,resizable=yes,width=760,height=700'); return false;"
                                >Editar</a>
                                <?php if (!empty($usuario['contrato_archivo'])) { ?>
                                    <a href="/gestion/personal/contrato.php?id=<?= $codigo; ?>">Contrato PDF</a>
                                <?php } if (!empty($usuario['perfil_pendiente'])) { ?>
                                    <small>Pendiente de completar sus datos</small>
                                <?php } ?>
                                <?php if ((int) $nivel_acceso >= 4) { ?>
                                    <a
                                        href="/gestion/usuarios/elim_u_b.php?Cod=<?= $codigo; ?>"
                                        onclick="return confirm('Se va a eliminar este usuario. Deseas continuar?');"
                                    >Eliminar</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php app_render_pagination(
            $total_usuarios,
            $pagina,
            $por_pagina,
            array('label' => 'usuarios')
        ); ?>
        <div class="legacy-empty-state legacy-users-no-results" data-users-empty hidden>
            <strong>No hay coincidencias</strong>
            <p>Prueba con otro nombre, correo, usuario o centro.</p>
        </div>
    <?php } ?>
</section>
<script src="/gestion/panel/assets/gestion-usuarios.js?v=20261006.2" charset="utf-8" defer></script>
<?php require('../../configuracion/parte_inferior.php'); ?>
