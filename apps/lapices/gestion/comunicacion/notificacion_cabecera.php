<link rel="stylesheet" href="/gestion/comunicacion/assets/notificaciones-equipo.css?v=20261005.1">
<script src="/gestion/comunicacion/assets/notificaciones-equipo.js?v=20261005.1" charset="utf-8" defer></script>
<a class="team-notification-link" href="/herramientas/enviar_mensaje_edu.php" aria-label="Abrir chat del equipo" title="Chat del equipo" data-team-notifications data-user="<?=(int)$idUsuario?>" data-centre="<?=(int)$centro_actual?>" data-centre-name="<?=app_escape($nombre_centro_actual)?>">
    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 11a8 8 0 0 1-8 8H5l-4 3V11a9 9 0 0 1 19 0Z"/><path d="M6 10h9M6 14h6"/></svg>
    <span class="team-notification-badge" data-team-count hidden>0</span>
</a>
