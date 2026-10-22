<?php
require dirname(__DIR__).'/configuracion/config.php';
require dirname(__DIR__).'/configuracion/autentifica_almacen.php';
require_once __DIR__.'/proveedores_lib.php';
$state=app_request_int($_GET,'e',0)===1?1:0;$search=isset($_GET['q'])&&is_string($_GET['q'])?trim($_GET['q']):'';$error='';$rows=array();
try{
    $pattern='%'.$search.'%';
    $rows=lap_wf_rows('SELECT p.*, (SELECT MAX(fecha_valoracion) FROM proveedores_evaluaciones e WHERE e.id_proveedor=p.ID_PROVEEDOR) AS ultima FROM proveedores p WHERE estado=? AND (RAZON_SOCIAL LIKE ? OR nombre_comercial LIKE ? OR NIF LIKE ? OR EMAIL LIKE ?) ORDER BY RAZON_SOCIAL','issss',array($state,$pattern,$pattern,$pattern,$pattern));
}catch(Exception $e){$error=$e->getMessage();}
lap_wf_start('Proveedores','Añade, consulta y gestiona tus proveedores desde una sola pantalla.','almacen');lap_wf_error($error);
if(!empty($_SESSION['proveedor_aviso'])){echo '<p class="wf-success" role="status">'.app_escape($_SESSION['proveedor_aviso']).'</p>';unset($_SESSION['proveedor_aviso']);}
?>
<div class="wf-actions"><a class="wf-btn" href="nuevo_proveedor_b.php">Nuevo proveedor</a><a class="wf-btn secondary" href="/almacen/gestion_almacen.php">Volver al almacén</a></div>
<form method="get" class="wf-card wf-form-filters"><label>Buscar proveedor<input type="search" name="q" value="<?=app_escape($search)?>" placeholder="Nombre, NIF o correo"></label><label>Estado<select name="e"><option value="0">Activos</option><option value="1" <?=$state===1?'selected':''?>>Inactivos</option></select></label><button class="wf-btn">Buscar</button></form>
<p class="wf-help"><?=count($rows)?> proveedores <?=$state===1?'inactivos':'activos'?></p><div class="wf-list">
<?php foreach($rows as $row){$id=(int)$row['ID_PROVEEDOR']; ?>
<article class="wf-supplier"><div><h2><?=app_escape($row['RAZON_SOCIAL'])?></h2><p><?=app_escape($row['nombre_comercial'])?> · <?=app_escape($row['NIF'])?></p><p><?=app_escape($row['EMAIL'])?> <?=app_escape($row['TELEFONO1'])?></p><p class="wf-small"><?=app_escape($row['subtipo_proveedor'])?></p><?php if($row['ultima']){ ?><p class="wf-small">Última evaluación: <?=date('d/m/Y',strtotime($row['ultima']))?></p><?php } ?></div><div class="wf-actions"><a class="wf-btn secondary" href="nuevo_proveedor_b.php?u=<?=$id?>">Editar<?=$state===1?' / Reactivar':''?></a><a class="wf-btn secondary" href="ver_pedidos.php?proveedor=<?=$id?>">Pedidos</a><a class="wf-btn danger" href="eliminar_proveedor.php?id_proveedor=<?=$id?>&amp;_csrf=<?=rawurlencode(app_csrf_token())?>">Eliminar</a></div></article>
<?php } if(!$rows){ ?><p class="wf-empty">No hay proveedores que coincidan con esta búsqueda.</p><?php } ?></div>
<?php lap_wf_end(); ?>
