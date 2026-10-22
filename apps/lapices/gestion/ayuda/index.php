<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';
require_once __DIR__ . '/catalogo.php';

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}

$help_catalog = lapices_help_catalog();
$help_from = (string) app_request_value($_GET, 'desde', '/index.php');
$help_return_url = $help_from;
if (
    $help_return_url === ''
    || strpos($help_return_url, '/') !== 0
    || strpos($help_return_url, '//') === 0
) {
    $help_return_url = '/index.php';
}
$help_context = lapices_help_for_path($help_from);
$panel_active_section = 'ayuda';
$panel_topbar_label = 'Centro de ayuda';
$panel_topbar_title = 'Manual de uso del programa';

require dirname(__DIR__) . '/panel/views/header.php';
?>
<section class="help-center" aria-labelledby="help-center-title" data-help-center>
    <header class="help-center__hero">
        <div>
            <span class="eyebrow">Ayuda paso a paso</span>
            <h1 id="help-center-title">¿Qué necesitas hacer?</h1>
            <p>
                Busca una tarea o abre un apartado. Las instrucciones están escritas
                con pasos cortos y sirven tanto en ordenador como en móvil o tablet.
            </p>
        </div>
        <a class="button button--outline" href="<?php echo app_escape($help_return_url); ?>">
            Volver a mi pantalla
        </a>
    </header>

    <section class="help-first-steps" aria-labelledby="help-first-steps-title">
        <div class="help-first-steps__mark" aria-hidden="true">1·2·3</div>
        <div>
            <span class="eyebrow">Para empezar</span>
            <h2 id="help-first-steps-title">Los cinco pasos que evitan la mayoría de errores</h2>
            <ol>
                <li><strong>Selecciona el centro</strong> antes de abrir un módulo.</li>
                <li><strong>Comprueba el curso, el aula y la fecha</strong> de la pantalla.</li>
                <li><strong>Busca primero</strong> para no crear fichas duplicadas.</li>
                <li><strong>Guarda una sola vez</strong> y espera la confirmación.</li>
                <li><strong>Cierra la sesión</strong> cuando termines de trabajar.</li>
            </ol>
        </div>
    </section>

    <section class="help-current" aria-labelledby="help-current-title">
        <div class="help-current__mark" aria-hidden="true"><?php echo app_escape($help_context['mark']); ?></div>
        <div>
            <span class="eyebrow">Ayuda de la pantalla desde la que vienes</span>
            <h2 id="help-current-title"><?php echo app_escape($help_context['title']); ?></h2>
            <p><?php echo app_escape($help_context['summary']); ?></p>
        </div>
        <button
            class="button button--primary"
            type="button"
            data-help-open
            data-help-route="<?php echo app_escape($help_from); ?>"
        >
            Ver pasos de esta pantalla
        </button>
    </section>

    <div class="help-search">
        <label for="help-search-input">Buscar en el manual</label>
        <div class="help-search__control">
            <span aria-hidden="true">⌕</span>
            <input
                id="help-search-input"
                type="search"
                placeholder="Ejemplo: inscribir alumno, merienda, uniforme…"
                autocomplete="off"
                data-help-search
            >
        </div>
        <p data-help-count><?php echo count($help_catalog) - 1; ?> apartados disponibles</p>
    </div>

    <div class="help-cards" data-help-list>
        <?php foreach ($help_catalog as $help_entry) {
            if ($help_entry['id'] === 'general') {
                continue;
            }
            $help_search_text = $help_entry['title'] . ' '
                . $help_entry['summary'] . ' '
                . $help_entry['keywords'] . ' '
                . implode(' ', $help_entry['steps']);
            ?>
            <article
                class="help-card"
                id="ayuda-<?php echo app_escape($help_entry['id']); ?>"
                data-help-card
                data-help-search-text="<?php echo app_escape($help_search_text); ?>"
            >
                <details>
                    <summary>
                        <span class="help-card__mark" aria-hidden="true"><?php echo app_escape($help_entry['mark']); ?></span>
                        <span class="help-card__heading">
                            <strong><?php echo app_escape($help_entry['title']); ?></strong>
                            <small><?php echo app_escape($help_entry['summary']); ?></small>
                            <em><?php echo app_escape($help_entry['audience']); ?></em>
                        </span>
                        <span class="help-card__chevron" aria-hidden="true">›</span>
                    </summary>
                    <div class="help-card__body">
                        <h3>Pasos</h3>
                        <ol>
                            <?php foreach ($help_entry['steps'] as $help_step) { ?>
                                <li><?php echo app_escape($help_step); ?></li>
                            <?php } ?>
                        </ol>

                        <?php if (!empty($help_entry['tips'])) { ?>
                            <div class="help-card__tips">
                                <strong>Ten en cuenta</strong>
                                <ul>
                                    <?php foreach ($help_entry['tips'] as $help_tip) { ?>
                                        <li><?php echo app_escape($help_tip); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>

                        <div class="help-card__links">
                            <?php foreach ($help_entry['links'] as $help_link) { ?>
                                <a class="button button--outline" href="<?php echo app_escape($help_link['href']); ?>">
                                    <?php echo app_escape($help_link['label']); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </details>
            </article>
        <?php } ?>
    </div>

    <p class="help-empty" data-help-empty hidden>
        No encontramos esa tarea. Prueba con una palabra más sencilla, como
        “alumno”, “comedor”, “factura” o “uniforme”.
    </p>

    <section class="help-support" aria-labelledby="help-support-title">
        <span class="help-support__icon" aria-hidden="true">!</span>
        <div>
            <h2 id="help-support-title">Si algo no coincide con estas instrucciones</h2>
            <p>
                Anota el nombre de la pantalla, el centro y qué estabas intentando
                hacer. Si puedes, adjunta una captura sin datos personales de otras familias.
            </p>
        </div>
    </section>
</section>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
