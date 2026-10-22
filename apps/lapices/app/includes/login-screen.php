<?php
if (!function_exists('familias_escape')) {
    function familias_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$familias_title = 'Escuelas Infantiles Lápices';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#174b41">
    <meta name="color-scheme" content="light">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="description" content="Área privada para las familias de las Escuelas Infantiles Lápices">
    <title>Familias · <?php echo familias_escape($familias_title); ?></title>
    <link rel="icon" href="/favicon.ico">
    <link rel="stylesheet" href="/app/assets/familias-login.css?v=20260727.4">
    <script src="/app/assets/familias-login.js?v=20260727.4" defer></script>
</head>
<body class="familias-login">
<main class="familias-login__page">
    <section class="familias-login__welcome" aria-labelledby="familias-welcome-title">
        <div class="familias-login__brand">
            <img src="/app/imagenes/logo_redondo.jpg" width="112" height="112" alt="Escuelas Infantiles Lápices">
            <div>
                <span>Escuelas Infantiles</span>
                <strong>Lápices</strong>
            </div>
        </div>

        <div class="familias-login__welcome-copy">
            <span class="familias-login__eyebrow">Área de familias</span>
            <h1 id="familias-welcome-title">Su día a día, siempre contigo.</h1>
            <p>Consulta la agenda, los menús, los mensajes y la documentación del centro desde cualquier lugar.</p>
        </div>

        <div class="familias-login__features" aria-label="Servicios disponibles">
            <span>Agenda</span>
            <span>Menús</span>
            <span>Mensajes</span>
            <span>Recibos</span>
        </div>
    </section>

    <section class="familias-login__card" aria-labelledby="familias-login-title">
        <div class="familias-login__mobile-brand" aria-hidden="true">
            <img src="/app/imagenes/logo_redondo.jpg" width="72" height="72" alt="">
            <strong>Lápices</strong>
        </div>

        <header class="familias-login__card-header">
            <span class="familias-login__eyebrow">Bienvenida/o</span>
            <h2 id="familias-login-title">Accede a tu espacio familiar</h2>
            <p>Introduce los datos que te ha facilitado el centro.</p>
        </header>

        <?php if ($login_notice !== '') { ?>
            <div class="familias-login__notice" role="status">
                <?php echo familias_escape($login_notice); ?>
            </div>
        <?php } ?>

        <?php if (count($login_errors) > 0) { ?>
            <div class="familias-login__alert" role="alert" aria-live="polite">
                <strong>No hemos podido iniciar sesión</strong>
                <?php foreach ($login_errors as $login_error) { ?>
                    <span><?php echo familias_escape($login_error); ?></span>
                <?php } ?>
            </div>
        <?php } ?>

        <form class="familias-login__form" method="post" action="/app/acceso_familias.php" data-familias-login-form>
            <input type="hidden" name="_csrf" value="<?php echo familias_escape(app_csrf_token()); ?>">
            <input type="hidden" name="redirect" value="<?php echo familias_escape($redirect); ?>">

            <label class="familias-login__field" for="user">
                <span>Usuario</span>
                <span class="familias-login__control">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 8a7 7 0 0 0-14 0"/>
                    </svg>
                    <input id="user" name="user" type="text" inputmode="text" autocomplete="username" autocapitalize="none" spellcheck="false" placeholder="Tu nombre de usuario" required autofocus>
                </span>
            </label>

            <label class="familias-login__field" for="pass">
                <span>Contraseña</span>
                <span class="familias-login__control">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="10" rx="2"/>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    </svg>
                    <input id="pass" name="pass" type="password" autocomplete="current-password" placeholder="Tu contraseña" required>
                    <button class="familias-login__password-toggle" type="button" aria-label="Mostrar contraseña" aria-pressed="false" data-password-toggle>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                            <circle cx="12" cy="12" r="2.5"/>
                        </svg>
                    </button>
                </span>
            </label>

            <button class="familias-login__submit" type="submit" data-login-submit>
                <span>Entrar en mi espacio</span>
                <span aria-hidden="true">→</span>
            </button>
        </form>

        <div class="familias-login__help">
            <strong>¿No recuerdas tus datos?</strong>
            <span>Solicita unas nuevas credenciales en la escuela.</span>
        </div>

        <p class="familias-login__privacy">
            Acceso privado y exclusivo para las familias del centro.
        </p>
    </section>
</main>
</body>
</html>
