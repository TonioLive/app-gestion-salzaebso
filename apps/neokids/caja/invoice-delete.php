<?php
require_once __DIR__.'/invoice-common.php';app_check_csrf();$input=caja_invoice_request();
try{
    if(defined('CAJA_INVOICE_BATCH')){$raw=trim((string)app_value($input,'facturas'),', ');if(!preg_match('/^\d+(?:,\d+)*$/D',$raw))throw new InvalidArgumentException('Selecciona las facturas que deseas eliminar.');$ids=array_values(array_unique(array_map('intval',explode(',',$raw))));}
    else $ids=[caja_id($input,'id_factura')];
    if(!$ids||count($ids)>200||min($ids)<1)throw new InvalidArgumentException('Selecciona entre una y 200 facturas.');
    caja_invoice_delete($ids);
    $query=[];foreach(['mes','ejercicio','centro_factura','id_cliente'] as $key){$value=(string)app_value($input,$key);if($value!==''&&ctype_digit($value))$query[$key]=$value;}
    $target='/facturacion/listado_facturas.php';if(defined('CAJA_INVOICE_STUDENT')){$suffix=(string)app_value($input,'origen');if(!in_array($suffix,['','_ampliado','_modal'],true))$suffix='';$target='/facturacion/listado_facturas_alumno'.$suffix.'.php';}
    if(defined('CAJA_INVOICE_BATCH')){caja_header('Facturas eliminadas');echo '<div class="caja-alert success">Las facturas seleccionadas se han eliminado correctamente.</div><p><a class="caja-button" href="/facturacion/listado_facturas.php">Volver al listado</a></p>';caja_footer();exit;}
    header('Location: '.$target.($query?'?'.http_build_query($query):''),true,303);exit;
}catch(InvalidArgumentException $error){caja_fail($error->getMessage());}catch(Throwable $error){error_log('Invoice removal: '.$error->getMessage());caja_fail('No se han eliminado las facturas. Vuelve a revisar el listado.',500);}
