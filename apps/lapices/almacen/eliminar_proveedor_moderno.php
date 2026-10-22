<?php
require dirname(__DIR__).'/configuracion/config.php';
require dirname(__DIR__).'/configuracion/autentifica_almacen.php';
require_once __DIR__.'/proveedores_lib.php';
$id=app_request_int($_GET,'id_proveedor',0);$error='';$supplier=null;$dependencies=0;
try{
    $rows=lap_wf_rows('SELECT ID_PROVEEDOR,RAZON_SOCIAL FROM proveedores WHERE ID_PROVEEDOR=?','i',array($id));
    if(!$rows)throw new InvalidArgumentException('El proveedor no existe.');$supplier=$rows[0];
    if(app_request_value($_SERVER,'REQUEST_METHOD')==='POST'){lap_wf_post();$_SESSION['proveedor_aviso']=proveedores_borrar($id);header('Location: listado_proveedores.php',true,303);exit;}
    $dependencies=proveedores_dependencias($id);
}catch(Exception $e){http_response_code(422);$error=$e->getMessage();}
lap_wf_start('Eliminar proveedor','Revisa el proveedor seleccionado antes de confirmar.','almacen');lap_wf_error($error);
if($supplier){ ?><div class="wf-card"><h2><?=app_escape($supplier['RAZON_SOCIAL'])?></h2><p class="wf-help"><?=$dependencies?'Tiene artículos, pedidos u otros movimientos asociados. Se dará de baja y desaparecerá de los proveedores activos, conservando su historial.':'No tiene movimientos asociados. Se eliminará su ficha.'?></p><form method="post"><?php lap_wf_token(); ?><div class="wf-actions"><button class="wf-btn danger" type="submit"><?=$dependencies?'Confirmar baja':'Confirmar eliminación'?></button><a class="wf-btn secondary" href="listado_proveedores.php">Cancelar</a></div></form></div><?php }lap_wf_end(); ?>
