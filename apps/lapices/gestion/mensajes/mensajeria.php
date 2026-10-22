<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';
require_once __DIR__ . '/centro_mensajes_lib.php';

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}
if (!$panel_has_selected_centre) {
    header('Location: /index.php');
    exit;
}
if (!staff_messages_user_can_access()) {
    http_response_code(403);
}

$messageClassrooms = $panel_database instanceof mysqli
    ? staff_messages_classrooms($panel_database)
    : array();
$requestedClassroom = app_request_int($_GET, 'id_aula', 0);
$requestedStudent = app_request_int($_GET, 'id_alumno', 0);
$validClassroomIds = array();
foreach ($messageClassrooms as $messageClassroom) {
    $validClassroomIds[] = (int) $messageClassroom['id_aula'];
}
if ($requestedClassroom > 0 && !in_array($requestedClassroom, $validClassroomIds, true)) {
    $requestedClassroom = 0;
}

$panel_active_section = 'mensajes';
$panel_topbar_label = 'Comunicación con familias';
$panel_topbar_title = 'Centro de mensajes';
$panel_body_class = 'panel-page--message-center';
$panel_extra_styles = array('/gestion/mensajes/assets/centro-mensajes.css?v=20260907.2');
$panel_extra_scripts = array('/gestion/mensajes/assets/centro-mensajes.js?v=20260907.2');

require dirname(__DIR__) . '/panel/views/header.php';
?>
<?php if (!staff_messages_user_can_access()) { ?>
    <section class="message-access-denied">
        <span class="eyebrow">Mensajes</span>
        <h1>No tienes acceso a esta sección</h1>
        <p>Pide a administración que revise los permisos de mensajería de tu usuario.</p>
        <a class="button button--primary" href="/index.php">Volver al inicio</a>
    </section>
<?php } else { ?>
<section
    class="message-center-page"
    data-message-center
    data-state-endpoint="/gestion/mensajes/api.php?action=state"
    data-chat-endpoint="/gestion/mensajes/api.php?action=messages"
    data-read-endpoint="/gestion/mensajes/api.php?action=read"
    data-send-endpoint="/gestion/mensajes/api.php?action=send"
>
    <header class="message-center-hero">
        <div>
            <span class="eyebrow">Familias y escuela, en un solo lugar</span>
            <h1>Centro de mensajes</h1>
            <p>Elige un aula y abre cualquier familia, aunque todavía no haya escrito.</p>
        </div>
        <div class="message-center-hero__status" aria-live="polite">
            <span class="message-center-hero__badge" data-total-unread>0</span>
            <span><strong>Por leer</strong><small>Se actualiza automáticamente</small></span>
        </div>
    </header>

    <div class="message-center-toolbar" aria-label="Filtros de conversaciones">
        <label class="message-center-field" for="message-classroom">
            <span>Aula</span>
            <select id="message-classroom" data-classroom-select>
                <option value="0">Todas mis aulas</option>
                <?php foreach ($messageClassrooms as $messageClassroom) { ?>
                    <option value="<?php echo (int) $messageClassroom['id_aula']; ?>"<?php echo $requestedClassroom === (int) $messageClassroom['id_aula'] ? ' selected' : ''; ?>><?php echo app_escape($messageClassroom['nombre']); ?> · <?php echo (int) $messageClassroom['total_alumnos']; ?> alumnos</option>
                <?php } ?>
            </select>
        </label>
        <label class="message-center-search" for="message-family-search">
            <span class="sr-only">Buscar alumno o familiar</span>
            <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>
            <input id="message-family-search" type="search" placeholder="Buscar alumno o familiar..." autocomplete="off" data-conversation-search>
        </label>
    </div>

    <div class="message-center-shell" data-message-layout>
        <aside class="message-conversations" aria-label="Conversaciones">
            <div class="message-conversations__heading">
                <div><strong>Conversaciones</strong><small data-conversation-summary>Cargando familias…</small></div>
                <button type="button" class="message-refresh" data-message-refresh aria-label="Actualizar mensajes" title="Actualizar mensajes">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 4v5h5m-5 4a8.1 8.1 0 0 0 15.5 2M20 20v-5h-5"/></svg>
                </button>
            </div>
            <div class="message-conversation-list" data-conversation-list aria-live="polite">
                <div class="message-list-loading"><span></span><span></span><span></span><span></span></div>
            </div>
        </aside>

        <article class="message-chat" data-message-chat>
            <div class="message-chat-empty" data-chat-empty>
                <div class="message-chat-empty__illustration" aria-hidden="true"><span></span><span></span><span></span></div>
                <h2>Selecciona una familia</h2>
                <p>Aquí verás todo el historial en orden y podrás responder como en un chat.</p>
            </div>

            <div class="message-chat-active" data-chat-active hidden>
                <header class="message-chat-header">
                    <button class="message-chat-back" type="button" data-chat-back aria-label="Volver a las conversaciones">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <span class="message-avatar message-avatar--large" data-chat-avatar aria-hidden="true">F</span>
                    <div class="message-chat-header__copy">
                        <strong data-chat-student>Familia</strong>
                        <span data-chat-family></span>
                        <small data-chat-classroom></small>
                    </div>
                    <span class="message-chat-header__state" data-chat-state>Comunicación del centro</span>
                </header>

                <div class="message-chat-history" data-chat-history aria-live="polite"></div>

                <form class="message-composer" data-message-composer>
                    <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
                    <input type="hidden" name="id_alumno" value="" data-composer-student>
                    <input type="hidden" name="id_hilo" value="0" data-composer-thread>
                    <label class="sr-only" for="message-text">Escribe un mensaje</label>
                    <textarea id="message-text" name="texto" rows="1" maxlength="5000" placeholder="Escribe un mensaje…" data-message-text required></textarea>
                    <button type="submit" data-send-message aria-label="Enviar mensaje">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/></svg>
                    </button>
                    <span class="message-composer__status" data-composer-status aria-live="polite"></span>
                </form>
            </div>
        </article>
    </div>

    <script type="application/json" data-message-config><?php
        echo json_encode(
            array('classroom_id' => $requestedClassroom, 'student_id' => $requestedStudent, 'csrf' => app_csrf_token()),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
    ?></script>
</section>
<?php } ?>
<?php require dirname(__DIR__) . '/panel/views/footer.php'; ?>
