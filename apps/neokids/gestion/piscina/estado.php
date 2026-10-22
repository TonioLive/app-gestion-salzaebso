<?php
require __DIR__.'/common.php';pool_require_administration();
require __DIR__.'/connector.php';$dashboard=pool_dashboard_cached();$counts=$dashboard['counts'];
function pool_status_time($time){return $time?date('d/m · H:i',(int)$time):'Sin registro';}
pool_header('Estado de la piscina','estado');
if(!empty($_SESSION['pool_access_notice'])){echo '<p class="notice" role="status">'.app_escape($_SESSION['pool_access_notice']).'</p>';unset($_SESSION['pool_access_notice']);}
?>
<section class="pool-overview" data-overall-state="<?php echo $dashboard['state']; ?>" aria-labelledby="pool-overall-title">
 <div class="pool-overview-main"><span class="pool-overview-icon" aria-hidden="true">≈</span><div><span class="eyebrow">Control de los parámetros</span><h2 id="pool-overall-title"><?php echo app_escape($dashboard['title']); ?></h2><p>Seguimiento automático cada 10 minutos, también con esta pantalla cerrada.</p></div></div>
 <div class="pool-overview-counts"><div><strong id="count-ok"><?php echo $counts['ok']; ?></strong><span>en rango</span></div><div><strong id="count-outside"><?php echo $counts['outside']; ?></strong><span>fuera de rango</span></div><div><strong id="count-unknown"><?php echo $counts['unknown']; ?></strong><span>sin dato actual</span></div></div>
</section>
<div class="pool-monitor-bar"><div><span class="monitor-dot" id="monitor-dot" data-active="<?php echo $dashboard['monitor']['active']?'yes':'no'; ?>"></span><strong id="monitor-status"><?php echo $dashboard['monitor']['active']?'Seguimiento automático activo':'Seguimiento automático sin confirmar'; ?></strong><span id="last-check">Última comprobación: <?php echo pool_status_time($dashboard['monitor']['last_run']); ?></span></div><button class="quiet" type="button" id="refresh" data-csrf="<?php echo app_csrf_token(); ?>">↻ Comprobar ahora</button></div>
<p id="refresh-result" class="pool-refresh-result" role="status" aria-live="polite"></p>
<section class="card pool-alert-settings" id="pool-alerts" data-csrf="<?php echo app_csrf_token(); ?>" aria-labelledby="alerts-title"><div><span class="eyebrow">Avisos a administración</span><h2 id="alerts-title">Que un fallo no pase desapercibido</h2><p id="alerts-message" role="status">Consultando avisos de los equipos…</p><button class="primary" type="button" id="alerts-toggle" hidden>Activar avisos en este dispositivo</button></div><ul id="alerts-incidents" aria-label="Incidencias de los equipos"></ul></section>
<section class="pool-gauges" aria-label="Lecturas de la piscina">
<?php foreach($dashboard['metrics'] as $key=>$m){ ?>
 <article class="card pool-metric" id="metric-<?php echo $key; ?>" data-key="<?php echo $key; ?>" data-state="<?php echo $m['state']; ?>">
  <div class="metric-heading"><h2><?php echo app_escape($m['title']); ?></h2><span class="metric-status" data-status><?php echo app_escape($m['label']); ?></span></div>
  <?php echo pool_gauge_svg($m); ?>
  <div class="metric-value"><strong data-reading><?php echo pool_status_format($m['value']); ?></strong><span><?php echo $m['unit']; ?></span></div>
  <?php if($m['monitored']){ ?><div class="metric-target"><span>Rango objetivo</span><strong><?php echo pool_status_format($m['min']).' – '.pool_status_format($m['max']).' '.$m['unit']; ?></strong></div><div class="metric-limits"><span>Mín. <?php echo pool_status_format($m['min']); ?></span><span>Máx. <?php echo pool_status_format($m['max']); ?></span></div><?php }else{ ?><div class="metric-target metric-target-info"><span>Solo informativo</span><strong>Sin alertas por valor</strong></div><div class="metric-limits"><span>No interviene en el estado general</span></div><?php } ?>
  <p class="metric-detail"><?php echo app_escape($m['detail']); ?></p><p class="metric-age" data-age><?php echo in_array($m['state'],array('offline','stale'),true)?'Último dato guardado · ':'';echo pool_status_time($m['time']); ?></p>
 </article>
<?php } ?>
</section>
<div class="pool-reading-notes"><span><i class="legend-dot target"></i> Verde: rango objetivo</span><span><i class="legend-dot outside"></i> Naranja: fuera de rango</span><span><i class="legend-dot old"></i> Gris: dato sin confirmar</span></div>
<p class="footnote pool-redox-note">El redox se mide en mV: es un indicador de desinfección y no equivale a una medición de cloro total en mg/L. Los rangos mostrados son los configurados por la piscina.</p>
<section class="pool-filtration card"><div><span class="eyebrow">Filtración</span><strong id="filtration-value"><?php echo app_escape($dashboard['filtration']['value']); ?></strong></div><p id="filtration-age"><?php echo app_escape($dashboard['sources']['vista']['label']).' · '.pool_status_time($dashboard['filtration']['time']); ?></p><span class="metric-status">Informativo</span></section>
<div class="section-title pool-equipment-heading"><div><span class="eyebrow">Conexiones</span><h2>Equipos de la piscina</h2></div><span class="footnote">Las consignas se gestionan en cada equipo</span></div>
<div class="equipment pool-equipment">
<?php foreach(array('water'=>array('Agua y calefacción','Controlador del vaso',pool_equipment_link('water'),'Abrir programación'),'air'=>array('Ambiente','Deshumidificación',pool_equipment_link('air'),'Abrir programación'),'vista'=>array('Tratamiento del agua','VistaPool','https://vistapool.es/login?from=%2Fdashboard','Abrir VistaPool')) as $key=>$equipment){ ?>
 <article class="card"><span class="eyebrow"><?php echo $equipment[0]; ?></span><h3><?php echo $equipment[1]; ?></h3><p id="status-<?php echo $key; ?>"><?php echo app_escape($dashboard['sources'][$key]['label']).' · '.pool_status_time($dashboard['sources'][$key]['time']); ?></p><?php if($key==='water'){ ?><form method="post" action="abrir-controlador.php" target="_blank" rel="noopener"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="source" value="<?php echo $key; ?>"><button class="primary" type="submit">Entrar sin escribir las claves ↗</button></form><form method="post" action="abrir-controlador.php"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="source" value="<?php echo $key; ?>"><input type="hidden" name="action" value="close"><button class="quiet" type="submit">Cerrar acceso al equipo</button></form><?php } ?><a class="quiet" href="<?php echo app_escape($equipment[2]); ?>" target="_blank" rel="noopener noreferrer"><?php echo $key==='vista'?'Abrir VistaPool':'Acceso habitual'; ?> ↗</a></article>
<?php } ?>
</div>
<p class="footnote">Al terminar de programar, utiliza «Cerrar acceso al equipo». El controlador de ambiente mantiene su inicio de sesión porque no se ha podido transferir su sesión al navegador. VistaPool ya aporta las lecturas automáticamente; su web externa mantiene su propio inicio de sesión. Una lectura antigua o un equipo desconectado no se marcan como correctos, aunque su último valor esté dentro del rango. El seguimiento necesita que el servidor de la piscina esté encendido. Este panel solo consulta lecturas.</p>
<script src="/gestion/piscina/estado.js?v=20260928" defer></script>
<script src="/gestion/piscina/alertas.js?v=20260928" defer></script>
<?php pool_footer(); ?>
