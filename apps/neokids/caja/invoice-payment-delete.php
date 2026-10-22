<?php
require_once __DIR__.'/invoice-common.php';app_check_csrf();$input=caja_invoice_request();$invoiceId=caja_id($input,'id_factura');$paymentId=caja_id($input,'id_pago');
try{
    caja_invoice($invoiceId);$rows=caja_rows('SELECT * FROM pagos_a_cuenta WHERE id_pago=? AND id_factura=?','ii',[$paymentId,$invoiceId]);
    if(!$rows)throw new InvalidArgumentException('El movimiento no pertenece a la factura seleccionada.');$centre=(int)$rows[0]['id_centro'];if(!security_owned_centre($centre))caja_fail('No tienes acceso al centro de este cobro.',403);
    $period=caja_period($centre);
    caja_write($centre,$period['previous_id'],function($period)use($invoiceId,$paymentId){caja_invoice($invoiceId,true);$rows=caja_rows('SELECT * FROM pagos_a_cuenta WHERE id_pago=? AND id_factura=? FOR UPDATE','ii',[$paymentId,$invoiceId]);if(!$rows)throw new InvalidArgumentException('Este movimiento ya no existe.');if(!caja_payment_is_open($rows[0],$period))caja_invoice_block_closed();caja_query('DELETE FROM pagos_a_cuenta WHERE id_pago=? AND id_factura=?','ii',[$paymentId,$invoiceId]);});
    caja_invoice_redirect($invoiceId);
}catch(InvalidArgumentException $error){caja_fail($error->getMessage());}catch(Throwable $error){error_log('Invoice payment removal: '.$error->getMessage());caja_fail('No se ha eliminado el cobro. Vuelve a consultar la factura.',500);}
