<?php
require_once dirname(__DIR__).'/gestion/panel/workflow.php';
function proveedores_referencias(){return array('tarifa'=>'PROVEEDOR','tarifa_old'=>'PROVEEDOR','tarifa_2019-10-17'=>'PROVEEDOR','pedidos_proveedor'=>'id_proveedor','albaranes_proveedor'=>'id_proveedor','gestion_costes'=>'proveedor','proveedores_evaluaciones'=>'id_proveedor');}
function proveedores_dependencias($id){
    $total=0;foreach(proveedores_referencias() as $table=>$field){$rows=lap_wf_rows('SELECT COUNT(*) AS n FROM `'.$table.'` WHERE `'.$field.'`=?','i',array($id));$total+=(int)$rows[0]['n'];}return $total;
}
function proveedores_borrar($id){
    $rows=lap_wf_rows('SELECT ID_PROVEEDOR FROM proveedores WHERE ID_PROVEEDOR=?','i',array($id));
    if(!$rows)throw new InvalidArgumentException('El proveedor ya no existe.');
    $conditions=array();$values=array($id);foreach(proveedores_referencias() as $table=>$field){$conditions[]='NOT EXISTS (SELECT 1 FROM `'.$table.'` WHERE `'.$field.'`=?)';$values[]=$id;}
    lap_wf_exec('DELETE FROM proveedores WHERE ID_PROVEEDOR=? AND '.implode(' AND ',$conditions),str_repeat('i',count($values)),$values);
    if(db_affected_rows()>0)return 'Proveedor eliminado.';
    lap_wf_exec('UPDATE proveedores SET estado=1 WHERE ID_PROVEEDOR=?','i',array($id));
    return 'Proveedor dado de baja. Su historial se conserva y puedes recuperarlo desde Inactivos.';
}
