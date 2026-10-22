<?php if (empty($permitido)) { ?><a href="/index.php">Iniciar sesi&oacute;n</a><?php } else { ?>
<header class="legacy-topbar" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;padding:18px 24px;background:white;border:1px solid #dbe8e3;border-radius:18px;margin-bottom:20px">
<a href="/index.php" style="font-size:20px;font-weight:bold;color:inherit">Sant Pau &middot; Inicio</a>
<span><?= htmlspecialchars($nombre_centro_actual,ENT_QUOTES,'Windows-1252'); ?></span>
<nav style="display:flex;gap:18px;align-items:center"><a href="/jornada.php">Agenda y jornada</a><a href="/gestion/usuarios/datos_usuario.php">Mis datos</a>
<form method="post" action="/configuracion/cerrar_sesion.php" style="margin:0"><input type="hidden" name="_csrf" value="<?= app_csrf_token(); ?>"><button type="submit">Cerrar sesi&oacute;n</button></form></nav></header>
<?php } ?>
