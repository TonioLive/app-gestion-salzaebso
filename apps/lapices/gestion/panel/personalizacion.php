<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once __DIR__ . '/bootstrap.php';

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}

if (!$panel_has_selected_centre) {
    header('Location: /index.php');
    exit;
}

$personalization_error = '';

if (app_request_value($_SERVER, 'REQUEST_METHOD', '') === 'POST') {
    if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
        $personalization_error = 'La sesión del formulario ha caducado. Vuelve a intentarlo.';
    } elseif (!($panel_database instanceof mysqli)) {
        $personalization_error = 'No se puede guardar la personalización en este momento.';
    } else {
        $action = app_request_value($_POST, 'personalization_action', 'save');
        $defaults = app_user_preferences_defaults();
        $color = $action === 'reset'
            ? $defaults['color_principal']
            : app_request_value($_POST, 'color_principal', $defaults['color_principal']);
        $view = $action === 'reset'
            ? $defaults['vista_inicio']
            : app_request_value($_POST, 'vista_inicio', $defaults['vista_inicio']);

        if (!preg_match('/^#[0-9a-fA-F]{6}$/', (string) $color)) {
            $personalization_error = 'Selecciona un color válido.';
        } elseif (!in_array($view, array('clasica', 'analisis'), true)) {
            $personalization_error = 'Selecciona una vista de inicio válida.';
        } elseif (app_save_user_preferences(
            $panel_database,
            (int) $idUsuario,
            $color,
            $view
        )) {
            header('Location: /gestion/panel/personalizacion.php?guardado=1');
            exit;
        } else {
            $personalization_error = 'No se han podido guardar los cambios.';
        }
    }
}

$panel_active_section = 'personalizacion';
$panel_topbar_label = 'Administración';
$panel_topbar_title = 'Personalización del programa';

require __DIR__ . '/views/header.php';
?>
<section
    class="personalization-page"
    aria-labelledby="personalization-title"
    data-personalization-page
>
    <header class="personalization-hero">
        <div>
            <span class="eyebrow">Tu programa, a tu manera</span>
            <h1 id="personalization-title">Personalización del programa</h1>
            <p>Estos ajustes se guardan en tu usuario y te acompañan en cualquier dispositivo.</p>
        </div>
        <a class="button button--outline" href="/index.php">Volver al inicio</a>
    </header>

    <?php if (app_request_int($_GET, 'guardado', 0) === 1) { ?>
        <div class="alert alert--success" role="status">
            <strong>Preferencias guardadas.</strong>
            El nuevo aspecto ya está activo.
        </div>
    <?php } ?>

    <?php if ($personalization_error !== '') { ?>
        <div class="alert alert--error" role="alert">
            <?php echo app_escape($personalization_error); ?>
        </div>
    <?php } ?>

    <form method="post" class="personalization-form" data-personalization-form>
        <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">

        <section class="card personalization-card" aria-labelledby="theme-title">
            <div class="personalization-card__heading">
                <div>
                    <span class="eyebrow">Apariencia</span>
                    <h2 id="theme-title">Color principal</h2>
                </div>
                <span
                    class="personalization-color-value"
                    data-theme-color-value
                ><?php echo app_escape($app_theme_color); ?></span>
            </div>

            <div class="theme-picker">
                <label class="theme-picker__custom" for="color_principal">
                    <span>Elige cualquier color</span>
                    <input
                        id="color_principal"
                        name="color_principal"
                        type="color"
                        value="<?php echo app_escape($app_theme_color); ?>"
                        data-theme-color
                    >
                </label>
                <div class="theme-swatches" aria-label="Colores recomendados">
                    <?php
                    $recommended_colors = array(
                        '#21675A' => 'Verde Lápices',
                        '#2F6FBE' => 'Azul',
                        '#7A4EAB' => 'Violeta',
                        '#C94F72' => 'Rosa',
                        '#B85C28' => 'Terracota',
                        '#3E596B' => 'Pizarra',
                    );
                    foreach ($recommended_colors as $recommended_color => $recommended_label) {
                        ?>
                        <button
                            type="button"
                            class="theme-swatch<?php echo strtoupper($app_theme_color) === $recommended_color ? ' theme-swatch--active' : ''; ?>"
                            style="--swatch-color: <?php echo app_escape($recommended_color); ?>"
                            data-theme-swatch="<?php echo app_escape($recommended_color); ?>"
                            aria-label="<?php echo app_escape($recommended_label); ?>"
                            title="<?php echo app_escape($recommended_label); ?>"
                        ></button>
                    <?php } ?>
                </div>
            </div>

            <div class="theme-preview" data-theme-preview>
                <div class="theme-preview__sidebar">
                    <span></span><span></span><span></span>
                </div>
                <div class="theme-preview__content">
                    <span class="theme-preview__line theme-preview__line--title"></span>
                    <span class="theme-preview__line"></span>
                    <div><span></span><span></span><span></span></div>
                </div>
            </div>
        </section>

        <section class="card personalization-card" aria-labelledby="view-title">
            <div class="personalization-card__heading">
                <div>
                    <span class="eyebrow">Pantalla principal</span>
                    <h2 id="view-title">Elige tu vista de inicio</h2>
                </div>
            </div>

            <div class="dashboard-view-options">
                <label class="dashboard-view-option">
                    <input
                        type="radio"
                        name="vista_inicio"
                        value="clasica"
                        <?php echo $app_dashboard_view === 'clasica' ? 'checked' : ''; ?>
                    >
                    <span class="dashboard-view-option__visual dashboard-view-option__visual--classic">
                        <i></i><i></i><i></i>
                    </span>
                    <strong>Vista clásica</strong>
                    <small>Conserva el inicio actual con jornada, perfil y todos los módulos.</small>
                </label>

                <label class="dashboard-view-option">
                    <input
                        type="radio"
                        name="vista_inicio"
                        value="analisis"
                        <?php echo $app_dashboard_view === 'analisis' ? 'checked' : ''; ?>
                    >
                    <span class="dashboard-view-option__visual dashboard-view-option__visual--analytics">
                        <i></i><i></i><i></i><i></i>
                    </span>
                    <strong>Vista con análisis</strong>
                    <small>Añade alumnado, asistencia, comedor y aulas con actualización automática.</small>
                </label>
            </div>
        </section>

        <div class="personalization-actions">
            <button
                class="button button--primary"
                type="submit"
                name="personalization_action"
                value="save"
            >Guardar personalización</button>
            <button
                class="button button--outline"
                type="submit"
                name="personalization_action"
                value="reset"
            >Restaurar aspecto original</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/views/footer.php'; ?>
