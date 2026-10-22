<main class="login-shell">
    <section class="login-card" aria-labelledby="login-title">
        <div class="login-card__intro">
            <a class="login-brand" href="/" aria-label="Lápices, inicio">
                <img src="/imagen/logo_redondo.png" alt="Lápices" width="96" height="96">
                <span>
                    <strong>Lápices</strong>
                    <small>Gestión de centros</small>
                </span>
            </a>
            <div class="login-card__message">
                <span class="eyebrow">Área de personal</span>
                <h1 id="login-title">Todo tu centro,<br>en un mismo lugar.</h1>
                <p>Organiza el día a día, consulta la agenda y accede a las herramientas de gestión con una interfaz más clara.</p>
            </div>
            <p class="login-card__support">Acceso exclusivo para personal autorizado.</p>
        </div>

        <div class="login-card__form">
            <div class="login-form__heading">
                <span class="eyebrow">Bienvenido de nuevo</span>
                <h2>Inicia sesión</h2>
                <p>Introduce tus credenciales habituales de Lápices.</p>
            </div>

            <?php if (app_request_value($_GET, 'sesion', '') === 'expirada') { ?>
                <div class="login-alert" role="status">
                    La sesión se ha cerrado tras 30 minutos sin actividad.
                </div>
            <?php } ?>
            <?php if (!empty($_SESSION['login_error'])) { ?>
                <div class="alert alert--error" role="alert">
                    <?php
                    echo app_escape($_SESSION['login_error']);
                    unset($_SESSION['login_error']);
                    ?>
                </div>
            <?php } ?>

            <form class="login-form" method="post" action="/configuracion/inicio_sesion.php">
                <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                <input type="hidden" name="redirect" value="/index.php">

                <label class="field">
                    <span>Usuario</span>
                    <input type="text" name="user" autocomplete="username" required autofocus>
                </label>

                <label class="field">
                    <span>Contraseña</span>
                    <span class="password-field">
                        <input type="password" name="pass" autocomplete="current-password" required data-password-input>
                        <button type="button" data-password-toggle aria-label="Mostrar contraseña">Mostrar</button>
                    </span>
                </label>

                <button class="button button--primary button--full" type="submit">Entrar al panel</button>
            </form>

            <p class="login-help">Si necesitas recuperar el acceso, contacta con la administración de tu centro.</p>
        </div>
    </section>
</main>
