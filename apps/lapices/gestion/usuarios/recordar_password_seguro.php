<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/autentifica.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';

$panel_active_section = 'personal';
$panel_topbar_label = 'Seguridad';
$panel_topbar_title = 'Restablecer una contraseña';
require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="module-landing">
    <span class="eyebrow">Credenciales protegidas</span>
    <h1>Las contraseñas ya no se pueden recuperar</h1>
    <p>
        Por seguridad se almacenan cifradas. Si una persona no recuerda su clave,
        asígnale una nueva desde su ficha de usuario; la contraseña anterior nunca se enviará por correo.
    </p>
    <div class="module-landing__actions">
        <a class="button button--primary" href="/gestion/usuarios/gestion_u.php">Abrir gestión de usuarios</a>
        <a class="button button--outline" href="/index.php">Volver al inicio</a>
    </div>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
