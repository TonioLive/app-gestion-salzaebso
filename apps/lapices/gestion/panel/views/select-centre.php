<?php if (!empty($_SESSION['panel_flash']) && is_array($_SESSION['panel_flash'])) { ?>
    <?php
    $panel_flash = $_SESSION['panel_flash'];
    unset($_SESSION['panel_flash']);
    ?>
    <div class="alert alert--<?php echo app_escape($panel_flash['type']); ?>" role="status">
        <?php echo app_escape($panel_flash['message']); ?>
    </div>
<?php } ?>

<section class="centre-gate" aria-labelledby="centre-gate-title">
    <div class="centre-gate__intro">
        <span class="centre-gate__mark" aria-hidden="true">CE</span>
        <span class="eyebrow">Centro de trabajo</span>
        <h1 id="centre-gate-title">Selecciona un centro para comenzar</h1>
        <p>
            Las opciones de gestión permanecerán ocultas hasta que elijas el
            centro con el que vas a trabajar. De este modo, todos los listados,
            aulas y operaciones se abrirán con el contexto correcto.
        </p>
    </div>

    <div class="centre-gate__form-card">
        <?php if (!$panel_database_available) { ?>
            <div class="alert alert--warning" role="status">
                <strong>No se ha podido cargar la lista de centros.</strong>
                Comprueba la conexión con la base de datos y vuelve a intentarlo.
            </div>
        <?php } elseif ($panel_can_change_centre && count($panel_centres) > 0) { ?>
            <form method="post" action="/index.php" class="centre-gate__form">
                <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                <input type="hidden" name="panel_action" value="change_centre">

                <label for="centre-gate-select">Centro</label>
                <select
                    id="centre-gate-select"
                    name="id_centro_seleccionado"
                    data-centre-select
                    required
                    autofocus
                >
                    <option value="">Elige un centro</option>
                    <?php foreach ($panel_centres as $centre) { ?>
                        <option value="<?php echo (int) $centre['id_centro']; ?>">
                            <?php echo app_escape($centre['nombre_centro'] . ' · ' . $centre['poblacion_centro']); ?>
                        </option>
                    <?php } ?>
                </select>

                <button class="button button--primary button--full" type="submit">
                    Entrar en el centro
                </button>
            </form>
            <p class="centre-gate__help">
                Nada más seleccionar el centro se cargará el inicio completo
                con todos los módulos permitidos para tu usuario.
            </p>
        <?php } elseif (!$panel_can_change_centre) { ?>
            <div class="alert alert--error" role="alert">
                <strong>Tu usuario no tiene un centro asignado.</strong>
                Pide a administración que revise la ficha de tu usuario.
            </div>
        <?php } else { ?>
            <div class="alert alert--warning" role="status">
                <strong>No hay centros disponibles para seleccionar.</strong>
                Revisa la configuración de centros visibles.
            </div>
        <?php } ?>
    </div>
</section>
