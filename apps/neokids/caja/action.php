<?php
require_once __DIR__.'/common.php';
if(!defined('CAJA_ACTION'))caja_fail('La operacion no existe.',404);
$action=CAJA_ACTION;$centre=caja_centre($_POST);$id=(int)$centre['id_centro'];[$token,$state]=caja_post_guard($action,$id);
$user=(int)($GLOBALS['idUsuario']??$_SESSION['idusuario']??0);
try{
    $comment=trim(app_text(app_value($_POST,'comentario')));
    if(mb_strlen($comment)>255)throw new InvalidArgumentException('El concepto admite hasta 255 caracteres.');
    $amount=0;$counted=0;$withdrawn=0;$method='0';
    if(in_array($action,['income','expense','deposit','withdraw'],true)){
        $amount=caja_parse_money(app_value($_POST,'importe_pago'));
        if($comment==='')throw new InvalidArgumentException('Indica el concepto del movimiento.');
        if(in_array($action,['income','expense'],true)){$method=(string)app_value($_POST,'forma_pago');if(!array_key_exists($method,caja_methods()))throw new InvalidArgumentException('Selecciona una forma de pago correcta.');}
    }elseif($action==='close'){
        $counted=caja_parse_money(app_value($_POST,'importe_contado'),true);$withdrawn=caja_parse_money(app_value($_POST,'importe_retirado'),true);
        if($withdrawn>$counted)throw new InvalidArgumentException('No puedes retirar una cantidad superior al efectivo contado.');
    }
    caja_write($id,$state['previous'],function($period)use($id,$action,$user,$comment,$amount,$method,$counted,$withdrawn){
        $date=date('Y-m-d');$time=date('H:i:s');
        if(in_array($action,['income','expense'],true)){
            $sign=$action==='income'?1:-1;
            caja_query('INSERT INTO pagos_a_cuenta(id_factura,id_centro,id_centro_cobro,fecha_pago,importe_pago,forma_pago,observaciones_pago,comentario,hora_pago,signo,anotado_por) VALUES(0,?,?,?,?,?,?,?,?,?,?)','iissssssii',[$id,$id,$date,caja_decimal($amount),$method,'Movimiento manual desde caja.',$comment,$time,$sign,$user]);
        }elseif(in_array($action,['deposit','withdraw'],true)){
            if($action==='withdraw'&&$amount>caja_totals($period)['cash'])throw new InvalidArgumentException('La retirada supera el efectivo disponible. Revisa el importe o registra primero la aportacion que falta.');
            caja_query('INSERT INTO movimientos_caja(id_centro,fecha,importe,comentario,hora,signo,anotado_por,id_cierre_anterior) VALUES(?,?,?,?,?,?,?,?)','issssiii',[$id,$date,caja_decimal($amount),$comment,$time,$action==='deposit'?1:-1,$user,$period['previous_id']]);
        }elseif($action==='close'){
            $expected=caja_totals($period)['cash'];
            if($expected!==$counted&&$comment==='')throw new InvalidArgumentException('El recuento no coincide con el saldo previsto. Indica el motivo en comentarios.');
            $max=(int)caja_rows('SELECT COALESCE(MAX(id_pago),0) AS last_id FROM pagos_a_cuenta WHERE id_centro=?','i',[$id])[0]['last_id'];
            caja_query('INSERT INTO cierres_caja(id_centro,fecha,hora,comentario,cerrado_por,importe_al_cierre,importe_al_cierre_real,ultimo_movimiento,version_control) VALUES(?,?,?,?,?,?,?,?,1)','isssissi',[$id,$date,$time,$comment,$user,caja_decimal($expected),caja_decimal($counted),$max]);
            $close=(int)app_db()->insert_id;
            if($withdrawn)caja_query("INSERT INTO movimientos_caja(id_centro,fecha,importe,comentario,hora,signo,anotado_por,id_cierre_anterior) VALUES(?,?,?,'Retirada de efectivo al cierre de caja',?,-1,?,?)",'isssii',[$id,$date,caja_decimal($withdrawn),$time,$user,$period['previous_id']]);
            caja_query("INSERT INTO movimientos_caja(id_centro,fecha,importe,comentario,hora,signo,anotado_por,id_cierre_anterior) VALUES(?,?,?,'Importe inicial de la caja',?,1,?,?)",'isssii',[$id,$date,caja_decimal($counted-$withdrawn),$time,$user,$close]);
        }elseif($action==='delete_payment'){
            $payment=caja_id($_POST,'id_pago');[$where,$types,$values]=caja_where($period);$values[]=$payment;
            $rows=caja_rows("SELECT p.id_pago,p.id_factura FROM pagos_a_cuenta p WHERE $where AND p.id_pago=?",$types.'i',$values);
            if(!$rows)throw new InvalidArgumentException('El movimiento no pertenece a esta caja abierta.');
            if((int)$rows[0]['id_factura']!==0)throw new InvalidArgumentException('Los cobros de facturas se gestionan desde facturacion.');
            caja_query('DELETE FROM pagos_a_cuenta WHERE id_pago=? AND id_centro=? AND id_factura=0','ii',[$payment,$id]);
        }elseif($action==='delete_cash'){
            $movement=caja_id($_POST,'id_mov');$rows=caja_rows('SELECT comentario FROM movimientos_caja WHERE id_movimiento=? AND id_centro=? AND id_cierre_anterior=?','iii',[$movement,$id,$period['previous_id']]);
            if(!$rows)throw new InvalidArgumentException('El movimiento no pertenece a esta caja abierta.');
            if(in_array($rows[0]['comentario'],['Importe inical de la caja','Importe inicial de la caja'],true))throw new InvalidArgumentException('El saldo inicial procede del cierre anterior y no se puede eliminar.');
            caja_query('DELETE FROM movimientos_caja WHERE id_movimiento=? AND id_centro=? AND id_cierre_anterior=?','iii',[$movement,$id,$period['previous_id']]);
        }else throw new InvalidArgumentException('La operacion no existe.');
    });
    unset($_SESSION['_caja_actions'][$token]);caja_back($id,$action==='close'?'closed':(str_starts_with($action,'delete_')?'deleted':'saved'));
}catch(InvalidArgumentException $error){caja_fail($error->getMessage());}
catch(Throwable $error){error_log('Caja: '.$error->getMessage());caja_fail('No se ha guardado el cambio. Revisa la caja y vuelve a intentarlo.',500);}
