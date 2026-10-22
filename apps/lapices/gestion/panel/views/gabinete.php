<section class="gabinete-landing">
    <a class="module-landing__back" href="/index.php#admin-title">
        <span aria-hidden="true">←</span>
        Volver a Administración
    </a>

    <header class="gabinete-hero">
        <div class="gabinete-hero__icon" aria-hidden="true">GP</div>
        <div class="gabinete-hero__copy">
            <span class="eyebrow">Área especializada</span>
            <h1>Gabinete psicopedagógico</h1>
            <p>
                Evaluación, seguimiento e informes del alumnado reunidos en un
                único espacio de trabajo.
            </p>
        </div>
        <a class="button button--primary gabinete-hero__action" href="/gestion/gabinete/gestion_aulas.php">
            Abrir aulas y alumnado
            <span aria-hidden="true">→</span>
        </a>
    </header>

    <div class="gabinete-route" aria-label="Cómo utilizar el gabinete">
        <div>
            <span>1</span>
            <strong>Selecciona el curso</strong>
            <small>Elige el curso académico que quieres consultar.</small>
        </div>
        <div>
            <span>2</span>
            <strong>Abre un aula</strong>
            <small>Accede al listado y a las herramientas del aula.</small>
        </div>
        <div>
            <span>3</span>
            <strong>Elige al alumno</strong>
            <small>Desde su ficha podrás utilizar todas las opciones.</small>
        </div>
    </div>

    <?php foreach ($panel_gabinete_groups as $panel_gabinete_group) { ?>
        <section
            class="gabinete-group gabinete-group--<?php echo app_escape($panel_gabinete_group['key']); ?>"
            aria-labelledby="gabinete-<?php echo app_escape($panel_gabinete_group['key']); ?>"
        >
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Herramientas del gabinete</span>
                    <h2 id="gabinete-<?php echo app_escape($panel_gabinete_group['key']); ?>">
                        <?php echo app_escape($panel_gabinete_group['title']); ?>
                    </h2>
                </div>
                <p><?php echo app_escape($panel_gabinete_group['summary']); ?></p>
            </div>

            <div class="gabinete-option-grid">
                <?php foreach ($panel_gabinete_group['items'] as $panel_gabinete_item) { ?>
                    <a
                        class="gabinete-option-card"
                        href="<?php echo app_escape(panel_safe_link($panel_gabinete_item['href'])); ?>"
                    >
                        <span class="gabinete-option-card__mark" aria-hidden="true">
                            <?php echo app_escape($panel_gabinete_item['mark']); ?>
                        </span>
                        <span class="gabinete-option-card__copy">
                            <strong><?php echo app_escape($panel_gabinete_item['title']); ?></strong>
                            <span><?php echo app_escape($panel_gabinete_item['description']); ?></span>
                            <small><?php echo app_escape($panel_gabinete_item['meta']); ?></small>
                        </span>
                        <span class="gabinete-option-card__arrow" aria-hidden="true">→</span>
                    </a>
                <?php } ?>
            </div>
        </section>
    <?php } ?>
</section>
