<?php
require_once __DIR__.'/invoice-common.php';
app_check_csrf();$invoiceId=caja_id($_POST,'id_factura');
try{
    $invoice=caja_invoice($invoiceId);$centre=$invoice['invoice_centre'];[$token,$state]=caja_post_guard('invoice_pay_'.$invoiceId,$centre);
    $amount=caja_parse_money(app_value($_POST,'importe_pago'));$method=(string)app_value($_POST,'forma_pago');
    if(!array_key_exists($method,caja_methods()))throw new InvalidArgumentException('Selecciona una forma de pago correcta.');
    $date=(string)app_value($_POST,'fecha_pago');
    if($date==='')$date=sprintf('%04d-%02d-%02d',caja_id($_POST,'pago_fechains_ano'),caja_id($_POST,'pago_fechains_mes'),caja_id($_POST,'pago_fechains_dia'));
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$date)||date('Y-m-d',strtotime($date))!==$date||$date>date('Y-m-d'))throw new InvalidArgumentException('La fecha del movimiento no es correcta o esta en el futuro.');
    $observations=trim(app_text(app_value($_POST,'observaciones_pago')));if(mb_strlen($observations)>2000)throw new InvalidArgumentException('Las observaciones admiten hasta 2000 caracteres.');
    caja_write($centre,$state['previous'],function($period)use($invoiceId,$centre,$amount,$method,$date,$observations){
        $invoice=caja_invoice($invoiceId,true);if($invoice['invoice_centre']!==$centre)throw new InvalidArgumentException('La factura ha cambiado de centro. Recarga la pantalla.');
        $totals=caja_invoice_totals($invoiceId);$remaining=$totals['remaining'];
        if($remaining===0||$amount>abs($remaining))throw new InvalidArgumentException('El importe supera el saldo pendiente de la factura. Recarga para ver los ultimos cobros.');
        $sign=$remaining<0?-1:1;$time=date('H:i:s');$previous=$period['previous'];
        if($previous&&(int)$previous['version_control']===0&&$date.' '.$time<=$previous['fecha'].' '.$previous['hora'])throw new InvalidArgumentException('Esa fecha pertenece a una caja cerrada. Registra el movimiento en la fecha actual e indica la fecha original en observaciones.');
        if($sign<0&&$method==='0'&&$amount>caja_totals($period)['cash'])throw new InvalidArgumentException('No hay efectivo suficiente para esta devolucion. Revisa la forma de pago o registra una aportacion a caja.');
        $comment=($sign<0?'Devolucion':'Cobro').' de factura '.trim((string)$invoice['serie'].'-'.(string)$invoice['num_factura'],'-');
        // Invoice PDFs and the remaining billing screens use the signed amount.
        // Keep their established refund representation (negative amount, sign 1).
        caja_query('INSERT INTO pagos_a_cuenta(id_factura,id_centro,id_centro_cobro,fecha_pago,importe_pago,forma_pago,observaciones_pago,comentario,hora_pago,signo,anotado_por) VALUES(?,?,?,?,?,?,?,?,?,?,?)','iiissssssii',[$invoiceId,$centre,(int)($GLOBALS['centro_actual']??$centre),$date,caja_decimal($amount*$sign),$method,$observations,$comment,$time,1,(int)$GLOBALS['idUsuario']]);
    });
    unset($_SESSION['_caja_actions'][$token]);caja_invoice_redirect($invoiceId);
}catch(InvalidArgumentException $error){caja_fail($error->getMessage());}catch(Throwable $error){error_log('Invoice payment: '.$error->getMessage());caja_fail('No se ha guardado el pago. Revisa la factura antes de volver a intentarlo.',500);}
