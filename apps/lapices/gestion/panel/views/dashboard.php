<section class="welcome">
    <div>
        <span class="eyebrow">Tu espacio de trabajo</span>
        <h1><?php echo app_escape($panel_greeting); ?>, <?php echo app_escape(isset($nombre) ? $nombre : ''); ?>.</h1>
        <p>Aquí tienes los accesos y la información que necesitas para gestionar la jornada.</p>
    </div>
    <div class="welcome__date" aria-label="<?php echo app_escape($panel_today_label); ?>">
        <strong><?php echo app_escape(date('d')); ?></strong>
        <span><?php echo app_escape(strtoupper(substr($panel_months[(int) date('n')], 0, 3))); ?></span>
    </div>
</section>

<?php if (!empty($_SESSION['panel_flash']) && is_array($_SESSION['panel_flash'])) { ?>
    <?php
    $panel_flash = $_SESSION['panel_flash'];
    unset($_SESSION['panel_flash']);
    ?>
    <div class="alert alert--<?php echo app_escape($panel_flash['type']); ?>" role="status">
        <?php echo app_escape($panel_flash['message']); ?>
    </div>
<?php } ?>

<?php if (!$panel_database_available) { ?>
    <div class="alert alert--warning" role="status">
        <strong>No se ha podido conectar con la base de datos.</strong>
        Los accesos siguen disponibles, pero los datos de jornada y centros no se pueden mostrar ahora mismo.
    </div>
<?php } ?>

<?php if ($panel_can_change_centre && count($panel_centres) > 0) { ?>
    <section class="centre-switcher" aria-labelledby="centre-switcher-title">
        <div>
            <span class="eyebrow">Centro activo</span>
            <h2 id="centre-switcher-title">
                <?php echo app_escape($nombre_centro_actual !== '' ? $nombre_centro_actual : 'Selecciona un centro'); ?>
            </h2>
            <?php if (!empty($poblacion_centro_actual)) { ?>
                <p><?php echo app_escape($poblacion_centro_actual); ?></p>
            <?php } ?>
        </div>
        <form method="post" action="/index.php" class="centre-switcher__form">
            <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
            <input type="hidden" name="panel_action" value="change_centre">
            <label for="id_centro_seleccionado">Cambiar de centro</label>
            <select id="id_centro_seleccionado" name="id_centro_seleccionado" data-centre-select>
                <option value="">Selecciona un centro</option>
                <?php foreach ($panel_centres as $centre) { ?>
                    <option
                        value="<?php echo (int) $centre['id_centro']; ?>"
                        <?php echo (int) $centro_actual === (int) $centre['id_centro'] ? 'selected' : ''; ?>
                    >
                        <?php echo app_escape($centre['nombre_centro'] . ' · ' . $centre['poblacion_centro']); ?>
                    </option>
                <?php } ?>
            </select>
        </form>
    </section>
<?php } ?>

<?php if ($app_dashboard_view === 'analisis') { ?>
    <section
        class="centre-overview"
        aria-labelledby="centre-overview-title"
        data-centre-summary
        data-summary-endpoint="/gestion/panel/api/resumen-centro.php"
    >
        <div class="section-heading centre-overview__heading">
            <div>
                <span class="eyebrow">Resumen en directo</span>
                <h2 id="centre-overview-title">Así está el centro hoy</h2>
            </div>
            <p>
                Actualizado a las
                <time data-stat-updated><?php echo app_escape($panel_centre_stats['actualizado']); ?></time>
            </p>
        </div>

        <div class="centre-stats" aria-live="polite">
            <a class="centre-stat centre-stat--students" href="/alumnos/gestion_alumnos.php">
                <span class="centre-stat__icon" aria-hidden="true">AL</span>
                <span class="centre-stat__copy">
                    <small>Alumnado activo</small>
                    <strong data-stat="alumnos"><?php echo (int) $panel_centre_stats['alumnos']; ?></strong>
                    <em>matriculados actualmente</em>
                </span>
            </a>
            <a class="centre-stat centre-stat--attendance" href="/aulas/gestion_aulas.php">
                <span class="centre-stat__icon" aria-hidden="true">AS</span>
                <span class="centre-stat__copy">
                    <small>Asistencia de hoy</small>
                    <strong data-stat="asistencia"><?php echo (int) $panel_centre_stats['asistencia']; ?></strong>
                    <em><span data-stat="porcentaje_asistencia"><?php echo (int) $panel_centre_stats['porcentaje_asistencia']; ?></span>% del alumnado</em>
                </span>
                <span class="centre-stat__progress" aria-hidden="true">
                    <i
                        data-stat-progress
                        style="width: <?php echo (int) $panel_centre_stats['porcentaje_asistencia']; ?>%"
                    ></i>
                </span>
            </a>
            <a class="centre-stat centre-stat--dining" href="/cocina/gestion_comedor.php">
                <span class="centre-stat__icon" aria-hidden="true">CO</span>
                <span class="centre-stat__copy">
                    <small>Comedor previsto</small>
                    <strong data-stat="comedor"><?php echo (int) $panel_centre_stats['comedor']; ?></strong>
                    <em>niños y niñas se quedan a comer</em>
                </span>
            </a>
            <a class="centre-stat centre-stat--classrooms" href="/gestion/centros/gestion_aulas.php">
                <span class="centre-stat__icon" aria-hidden="true">AU</span>
                <span class="centre-stat__copy">
                    <small>Aulas con alumnado</small>
                    <strong data-stat="aulas"><?php echo (int) $panel_centre_stats['aulas']; ?></strong>
                    <em>grupos activos en el centro</em>
                </span>
            </a>
        </div>
    </section>
<?php } ?>

<div class="dashboard-grid">
    <section class="card attendance-card" aria-labelledby="attendance-title">
        <div class="card__header">
            <div>
                <span class="eyebrow">Control de presencia</span>
                <h2 id="attendance-title">Mi jornada de hoy</h2>
            </div>
            <span class="status <?php echo $panel_open_attendance ? 'status--online' : ''; ?>">
                <span></span>
                <?php echo $panel_open_attendance ? 'Jornada abierta' : 'Sin fichaje abierto'; ?>
            </span>
        </div>

        <?php if (count($panel_attendance) > 0) { ?>
            <div class="attendance-list">
                <?php foreach ($panel_attendance as $attendance) { ?>
                    <div class="attendance-row">
                        <div>
                            <strong><?php echo app_escape($attendance['nombre_centro'] ? $attendance['nombre_centro'] : 'Centro sin indicar'); ?></strong>
                            <span>Entrada <?php echo app_escape(substr($attendance['hora_entrada'], 0, 5)); ?></span>
                        </div>
                        <div class="attendance-row__time">
                            <?php if (!empty($attendance['hora_salida'])) { ?>
                                <strong><?php echo app_escape(substr($attendance['hora_salida'], 0, 5)); ?></strong>
                                <span>Salida</span>
                            <?php } else { ?>
                                <strong>En curso</strong>
                                <span>Desde <?php echo app_escape(substr($attendance['hora_entrada'], 0, 5)); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="empty-state">
                <span class="empty-state__icon">HO</span>
                <div>
                    <strong>Aún no hay registros hoy</strong>
                    <p>Selecciona un centro y registra tu entrada para comenzar la jornada.</p>
                </div>
            </div>
        <?php } ?>

        <div class="attendance-card__footer">
            <div>
                <span>Tiempo completado</span>
                <strong><?php echo app_escape(panel_duration($panel_attendance_total)); ?> h</strong>
            </div>
            <?php if ($panel_open_attendance) { ?>
                <form method="post" action="/gestion/docentes/registro_salida.php">
                    <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                    <input type="hidden" name="id_registro" value="<?php echo (int) $panel_open_attendance['id_asistencia']; ?>">
                    <button class="button button--outline" type="submit">Registrar salida</button>
                </form>
            <?php } elseif (count($panel_centres) > 0) { ?>
                <form method="post" action="/gestion/docentes/registro_entrada.php" class="attendance-action">
                    <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                    <input type="hidden" name="turno" value="2">
                    <select name="id_centro_asistencia" aria-label="Centro del fichaje" required>
                        <option value="">Selecciona centro</option>
                        <?php foreach ($panel_centres as $centre) { ?>
                            <option
                                value="<?php echo (int) $centre['id_centro']; ?>"
                                <?php echo (int) $centro_actual === (int) $centre['id_centro'] ? 'selected' : ''; ?>
                            >
                                <?php echo app_escape($centre['nombre_centro']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button class="button button--primary" type="submit">Registrar entrada</button>
                </form>
            <?php } ?>
        </div>
    </section>

    <section class="card profile-card" aria-labelledby="profile-title">
        <div class="profile-card__top">
            <span class="avatar avatar--xl"><?php echo app_escape($panel_user_initials); ?></span>
            <div>
                <span class="eyebrow">Mi perfil</span>
                <h2 id="profile-title"><?php echo app_escape($nombre_completo); ?></h2>
                <p><?php echo app_escape($email); ?></p>
            </div>
        </div>
        <div class="profile-links">
            <a href="/gestion/usuarios/datos_usuario.php">
                <span>Datos personales</span>
                <strong>Editar información →</strong>
            </a>
            <a href="/gestion/personal/ficha.php">
                <span>Documentación laboral</span>
                <strong>Contratos y calendario →</strong>
            </a>
            <a href="/gestion/docentes/resumen_horas.php">
                <span>Registro horario</span>
                <strong>Consultar mis horas →</strong>
            </a>
        </div>
    </section>
</div>

<section class="modules" aria-labelledby="modules-title">
    <div class="section-heading">
        <div>
            <span class="eyebrow">Herramientas</span>
            <h2 id="modules-title">Áreas de gestión</h2>
        </div>
        <p>Los módulos se muestran según tus permisos.</p>
    </div>

    <div class="module-grid">
        <?php foreach ($panel_sections as $section) { ?>
            <article class="module-card module-card--<?php echo app_escape($section['accent']); ?>" id="modulo-<?php echo app_escape($section['key']); ?>">
                <div class="module-card__heading">
                    <span class="module-card__icon">
                        <?php echo app_escape(function_exists('mb_substr') ? mb_substr($section['title'], 0, 2, 'UTF-8') : substr($section['title'], 0, 2)); ?>
                    </span>
                    <div>
                        <h3><?php echo app_escape($section['title']); ?></h3>
                        <p><?php echo app_escape($section['summary']); ?></p>
                    </div>
                </div>
                <div class="module-card__links">
                    <?php foreach ($section['items'] as $item) { ?>
                        <a href="<?php echo app_escape($item['href']); ?>">
                            <span><?php echo app_escape($item['label']); ?></span>
                            <span aria-hidden="true">→</span>
                        </a>
                    <?php } ?>
                </div>
            </article>
        <?php } ?>
    </div>
</section>

<?php if (count($panel_admin_items) > 0) { ?>
    <section class="card admin-card" aria-labelledby="admin-title">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Configuración</span>
                <h2 id="admin-title">Administración</h2>
            </div>
            <p>Opciones avanzadas para la gestión del sistema.</p>
        </div>
        <div class="admin-grid">
            <?php foreach ($panel_admin_items as $admin_item) { ?>
                <?php
                $panel_admin_is_gabinete =
                    isset($admin_item['link']) &&
                    strpos((string) $admin_item['link'], '/gestion/gabinete') !== false;
                $panel_admin_is_personalization =
                    isset($admin_item['link']) &&
                    strpos((string) $admin_item['link'], '/gestion/panel/personalizacion.php') !== false;
                $panel_admin_is_reports =
                    isset($admin_item['link']) &&
                    strpos((string) $admin_item['link'], '/gestion/informes') !== false;
                $panel_admin_item_classes = array();
                if ($panel_admin_is_gabinete) {
                    $panel_admin_item_classes[] = 'admin-grid__item--gabinete';
                }
                if ($panel_admin_is_personalization) {
                    $panel_admin_item_classes[] = 'admin-grid__item--personalization';
                }
                if ($panel_admin_is_reports) {
                    $panel_admin_item_classes[] = 'admin-grid__item--reports';
                }
                ?>
                <a
                    class="<?php echo app_escape(implode(' ', $panel_admin_item_classes)); ?>"
                    href="<?php echo app_escape(panel_safe_link($admin_item['link'])); ?>"
                >
                    <strong><?php echo app_escape($admin_item['Texto']); ?></strong>
                    <span><?php echo app_escape($admin_item['texto_ayuda']); ?></span>
                </a>
            <?php } ?>
        </div>
    </section>
<?php } ?>
