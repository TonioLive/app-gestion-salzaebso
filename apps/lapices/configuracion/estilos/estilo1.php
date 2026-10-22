<?php
require_once dirname(__DIR__) . '/user_preferences.php';
$app_legacy_preferences = app_load_user_preferences(
    isset($db_validacion) ? $db_validacion : db_link(),
    isset($idUsuario) ? (int) $idUsuario : 0
);
$app_legacy_palette = app_theme_palette($app_legacy_preferences['color_principal']);
?>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="<?= app_escape($app_legacy_palette['brand']); ?>">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="stylesheet" href="/gestion/panel/assets/legacy-admin.css?v=20261006.3">
<link rel="stylesheet" href="/gestion/panel/assets/table-pagination.css?v=20260908.2">
<link rel="stylesheet" href="/gestion/panel/assets/session-timeout.css?v=20260909.1" data-session-timeout-style>
<style>
:root {
    --legacy-admin-green-950: <?= app_escape($app_legacy_palette['brand_dark']); ?>;
    --legacy-admin-green-800: <?= app_escape($app_legacy_palette['brand']); ?>;
    --legacy-admin-green-700: <?= app_escape($app_legacy_palette['primary_dark']); ?>;
    --legacy-admin-green-100: <?= app_escape($app_legacy_palette['soft']); ?>;
}
</style>
<script src="/gestion/panel/assets/legacy-admin.js?v=20260911.1" defer></script>
<script src="/gestion/panel/assets/table-pagination.js?v=20260908.1" defer></script>
<script src="/gestion/panel/assets/session-timeout.js?v=20260909.1" defer></script>
