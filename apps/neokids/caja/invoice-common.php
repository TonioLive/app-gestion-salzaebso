<?php
require_once __DIR__.'/common.php';
function caja_invoice($id,$lock=false) {
    $rows=caja_rows("SELECT f.*,a.id_centro AS invoice_centre,CONCAT_WS(' ',u.Nombre,u.Apellido1,u.Apellido2) AS alumno FROM facturas_emitidas f LEFT JOIN alumnos a ON a.id_alumno=f.id_cliente AND COALESCE(f.tipo_cliente,0)=0 LEFT JOIN usuarios u ON u.id_usuario=a.id_usuario WHERE f.id_factura=?".($lock?' FOR UPDATE':''),'i',[$id]);
    if(!$rows)throw new InvalidArgumentException('La factura no existe.');
    $invoice=$rows[0];$centre=$invoice['invoice_centre'];
    if($centre===null){
        $centres=caja_rows('SELECT DISTINCT id_centro FROM pagos_a_cuenta WHERE id_factura=?','i',[$id]);
        if(count($centres)!==1)throw new InvalidArgumentException('No se puede identificar el centro de esta factura. Revisa la ficha del alumno antes de modificarla.');
        $centre=$centres[0]['id_centro'];
    }
    if(!security_owned_centre((int)$centre))caja_fail('No tienes acceso a la factura de este centro.',403);
    $invoice['invoice_centre']=(int)$centre;
    return $invoice;
}
function caja_invoice_totals($id){
    $row=caja_rows('SELECT COUNT(*) AS n,COALESCE(SUM(cantidad*precio*(1-COALESCE(dto,0)/100)*(1+COALESCE(tipo_iva,0))),0) AS total FROM detalles_factura WHERE id_factura=?','i',[$id])[0];
    $total=(int)round((float)$row['total']*100);
    if(!(int)$row['n'])$total=(int)round((float)caja_rows('SELECT total_factura FROM facturas_emitidas WHERE id_factura=?','i',[$id])[0]['total_factura']*100);
    $paid=(int)caja_rows('SELECT COALESCE(SUM(ROUND(importe_pago*100)*signo),0) AS paid FROM pagos_a_cuenta WHERE id_factura=?','i',[$id])[0]['paid'];
    return ['total'=>$total,'paid'=>$paid,'remaining'=>$total-$paid];
}
function caja_payment_is_open($row,$period){
    if((int)$row['id_centro']!==$period['centre'])return false;
    $last=$period['previous'];if(!$last)return true;
    if((int)($last['version_control']??0)>=1)return (int)$row['id_pago']>(int)$last['ultimo_movimiento'];
    return ((string)$row['fecha_pago'].' '.($row['hora_pago']?:'00:00:00'))>((string)$last['fecha'].' '.($last['hora']?:'00:00:00'));
}
function caja_invoice_redirect($id){header('Location: /facturacion/pagos_a_cuenta.php?id_factura='.(int)$id,true,303);exit;}
function caja_invoice_request(){return array_merge($_GET,$_POST);}
function caja_invoice_block_closed(){throw new InvalidArgumentException("Este cobro pertenece a una caja cerrada. No se puede borrar ni eliminar su factura. Conserva el historial y registra la devoluci\u{00f3}n o la factura rectificativa que corresponda en la caja actual.");}
function caja_invoice_delete($ids){
    if(!in_array((int)($GLOBALS['nivel_acceso']??0),[3,4],true))caja_fail('Solo direccion o administracion puede eliminar facturas.',403);
    $db=app_db();$locks=[];
    try{
        $centres=[];foreach($ids as $id){$invoice=caja_invoice($id);$centres[$invoice['invoice_centre']]=true;}
        ksort($centres);
        foreach(array_keys($centres) as $centre){$key='neokids_caja_'.$centre;if((int)caja_rows('SELECT GET_LOCK(?,5) AS locked','s',[$key])[0]['locked']!==1)throw new RuntimeException('La caja esta ocupada.');$locks[]=$key;}
        if(!$db->begin_transaction())throw new RuntimeException('No se pudo iniciar la operacion.');
        $periods=[];foreach(array_keys($centres) as $centre){caja_rows('SELECT id_pago FROM pagos_a_cuenta WHERE id_centro=? ORDER BY fecha_pago,hora_pago,id_pago FOR UPDATE','i',[$centre]);$periods[$centre]=caja_period($centre);}
        foreach($ids as $id){$invoice=caja_invoice($id,true);if(!isset($periods[$invoice['invoice_centre']]))throw new InvalidArgumentException('La factura ha cambiado de centro. Recarga el listado.');
            foreach(caja_rows('SELECT * FROM pagos_a_cuenta WHERE id_factura=? FOR UPDATE','i',[$id]) as $p){if(!isset($periods[(int)$p['id_centro']])||!caja_payment_is_open($p,$periods[(int)$p['id_centro']]))caja_invoice_block_closed();}
        }
        $marks=implode(',',array_fill(0,count($ids),'?'));$types=str_repeat('i',count($ids));
        foreach(['pagos_a_cuenta','detalles_factura','facturas_emitidas'] as $table)caja_query("DELETE FROM $table WHERE id_factura IN ($marks)",$types,$ids);
        if(!$db->commit())throw new RuntimeException('No se pudo confirmar la operacion.');
    }catch(Throwable $error){$db->rollback();throw $error;}finally{foreach($locks as $key)caja_query('SELECT RELEASE_LOCK(?)','s',[$key]);}
}
