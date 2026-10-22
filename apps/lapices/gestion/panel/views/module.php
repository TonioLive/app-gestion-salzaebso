<section class="module-landing module-landing--<?php echo app_escape($panel_selected_section['accent']); ?>">
    <a class="module-landing__back" href="/index.php">
        <span aria-hidden="true">←</span>
        Volver al inicio
    </a>

    <header class="module-landing__header">
        <span class="module-card__icon" aria-hidden="true">
            <?php echo app_escape(function_exists('mb_substr') ? mb_substr($panel_selected_section['title'], 0, 2, 'UTF-8') : substr($panel_selected_section['title'], 0, 2)); ?>
        </span>
        <div>
            <span class="eyebrow">Opciones del módulo</span>
            <h1><?php echo app_escape($panel_selected_section['title']); ?></h1>
            <p><?php echo app_escape($panel_selected_section['summary']); ?></p>
        </div>
    </header>

    <div class="module-option-grid">
        <?php foreach ($panel_selected_section['items'] as $item) { ?>
            <a class="module-option-card" href="<?php echo app_escape(panel_safe_link($item['href'])); ?>">
                <span class="module-option-card__mark" aria-hidden="true">
                    <?php echo app_escape(function_exists('mb_substr') ? mb_substr($item['label'], 0, 1, 'UTF-8') : substr($item['label'], 0, 1)); ?>
                </span>
                <span class="module-option-card__copy">
                    <strong><?php echo app_escape($item['label']); ?></strong>
                    <small>Abrir esta herramienta</small>
                </span>
                <span class="module-option-card__arrow" aria-hidden="true">→</span>
            </a>
        <?php } ?>
    </div>
</section>
