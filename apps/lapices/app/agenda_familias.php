<?php
require dirname(__DIR__) . '/configuracion/config.php';

if (empty($_SESSION['permitido_padre'])) {
    header('Location: /app/login.php');
    exit;
}

require __DIR__ . '/parte_superior_padres.php';
?>

<div class="content-wrapper familias-agenda-page">
    <section class="content-header">
        <span class="familias-section-eyebrow">Organizaci&oacute;n familiar</span>
        <h1>
            Agenda
            <small>Fechas, actividades, asistencia y avisos del centro.</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="/app/index.php"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="active"><i class="fa fa-calendar"></i> Agenda</li>
        </ol>
    </section>

    <section class="content">
        <div class="familias-agenda-card">
            <div class="familias-agenda-mobile" data-familias-agenda-mobile>
                <header class="familias-agenda-mobile__toolbar">
                    <button
                        type="button"
                        data-agenda-action="prev"
                        aria-label="Mes anterior"
                    >
                        <i class="fa fa-chevron-left" aria-hidden="true"></i>
                    </button>
                    <span class="familias-agenda-mobile__period">
                        <small>Tu agenda</small>
                        <strong data-agenda-period></strong>
                    </span>
                    <button
                        type="button"
                        data-agenda-action="next"
                        aria-label="Mes siguiente"
                    >
                        <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    </button>
                    <button
                        class="familias-agenda-mobile__today"
                        type="button"
                        data-agenda-action="today"
                    >
                        Hoy
                    </button>
                </header>

                <div class="familias-agenda-mobile__loading" data-agenda-loading>
                    <i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>
                    Cargando agenda...
                </div>

                <div
                    class="familias-agenda-mobile__days"
                    data-agenda-days
                    aria-live="polite"
                ></div>

                <div class="familias-agenda-mobile__empty" data-agenda-empty hidden>
                    <span><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
                    <strong>No hay eventos este mes</strong>
                    <p>Cuando el centro publique una fecha aparecer&aacute; aqu&iacute;.</p>
                </div>
            </div>

            <div class="familias-agenda-desktop">
                <div id="calendar"></div>
            </div>
        </div>
    </section>
</div>

<?php
$familias_footer_scripts = array(
    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js',
    '/app/plugins/fullcalendar/fullcalendar.min.js',
    '/app/plugins/fullcalendar/lang/es.js',
    '/app/assets/familias-agenda.js?v=20260727.1',
);
require __DIR__ . '/parte_inferior_padres.php';
?>
