<?php
$messageNotificationVariant = isset($message_notification_variant)
    ? (string) $message_notification_variant
    : 'panel';
$messageNotificationId = 'message-notification-panel-' . $messageNotificationVariant;
?>
<div
    class="message-notifications message-notifications--<?php echo app_escape($messageNotificationVariant); ?>"
    data-message-notifications
    data-user-id="<?php echo isset($idUsuario) ? (int) $idUsuario : 0; ?>"
>
    <button
        class="message-notifications__trigger"
        type="button"
        aria-label="Notificaciones de familias"
        aria-controls="<?php echo app_escape($messageNotificationId); ?>"
        aria-expanded="false"
        data-message-notification-toggle
    >
        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
        <span class="message-notifications__badge" data-message-notification-count hidden>0</span>
    </button>
    <section
        class="message-notifications__panel"
        id="<?php echo app_escape($messageNotificationId); ?>"
        data-message-notification-panel
        hidden
    >
        <header>
            <span>
                <strong>Mensajes de familias</strong>
                <small data-message-notification-summary>Todo al día</small>
            </span>
            <a href="/gestion/mensajes/mensajeria.php">Abrir centro</a>
        </header>
        <div class="message-notifications__list" data-message-notification-list>
            <div class="message-notifications__empty">Buscando mensajes nuevos…</div>
        </div>
        <footer>
            <a href="/gestion/mensajes/mensajeria.php">Ver todas las conversaciones</a>
        </footer>
    </section>
</div>


<?php require dirname(__DIR__) . '/comunicacion/notificacion_cabecera.php'; ?>
