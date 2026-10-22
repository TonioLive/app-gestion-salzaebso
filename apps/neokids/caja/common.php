<?php
// UTF-8 output; source deliberately ASCII so legacy and modern entry points agree.
require_once dirname(__DIR__).'/configuracion/config.php';
require_once dirname(__DIR__).'/configuracion/autentifica.php';
if (!app_can('zona_facturacion')) { http_response_code(403); exit('No tienes permiso para consultar la caja.'); }
app_db()->set_charset('utf8mb4');

function caja_fail($message, $status=422) {
    http_response_code($status); caja_header('Revisa los datos');
    echo '<div class="caja-alert">'.app_escape($message).'</div><p><a class="caja-button" href="javascript:history.back()">Volver y corregir</a> <a href="/caja/listado_caja.php">Ir a caja</a></p>';
    caja_footer(); exit;
}
function caja_query($sql, $types='', $values=[]) {
    $r=$types==='' ? db_query($sql,app_db()) : db_query_params($sql,$types,$values,app_db());
    if($r===false)throw new RuntimeException('No se ha podido completar la consulta de caja.');
    return $r;
}
function caja_rows($sql,$types='',$values=[]) {$r=caja_query($sql,$types,$values);return $r instanceof mysqli_result?$r->fetch_all(MYSQLI_ASSOC):[];}
function caja_id($source,$key,$default=0) {
    if(!isset($source[$key])||$source[$key]==='')return (int)$default;
    if(!is_scalar($source[$key])||!ctype_digit((string)$source[$key])||strlen((string)$source[$key])>10)caja_fail('El identificador no es correcto.');
    return (int)$source[$key];
}
function caja_centre($source) {
    $id=caja_id($source,'id_centro_caja',(int)($GLOBALS['centro_actual']??$_SESSION['id_centro_usuario']??0));
    if(!security_owned_centre($id))caja_fail('No tienes acceso a la caja de este centro.',403);
    if($id===0)return ['id_centro'=>0,'nombre_centro'=>'Central'];
    $rows=caja_rows('SELECT id_centro,nombre_centro FROM centros WHERE id_centro=?','i',[$id]);
    if(!$rows)caja_fail('El centro no existe.',404);
    return $rows[0];
}
function caja_period($centre,$closeId=0) {
    $current=null;
    if($closeId){$rows=caja_rows('SELECT * FROM cierres_caja WHERE id_centro=? AND id_cierre=?','ii',[$centre,$closeId]);if(!$rows)caja_fail('Este cierre no pertenece al centro seleccionado.',404);$current=$rows[0];}
    $rows=$closeId?caja_rows('SELECT * FROM cierres_caja WHERE id_centro=? AND id_cierre<? ORDER BY id_cierre DESC LIMIT 1','ii',[$centre,$closeId]):caja_rows('SELECT * FROM cierres_caja WHERE id_centro=? ORDER BY id_cierre DESC LIMIT 1','i',[$centre]);
    return ['centre'=>(int)$centre,'close'=>$current,'previous'=>$rows[0]??null,'previous_id'=>(int)($rows[0]['id_cierre']??0)];
}
function caja_where($period,$alias='p') {
    $sql="$alias.id_centro=?";$types='i';$v=[$period['centre']];
    foreach(['previous'=>'>','close'=>'<='] as $key=>$op){$c=$period[$key];if(!$c)continue;
        if((int)($c['version_control']??0)>=1){$sql.=" AND $alias.id_pago $op ?";$types.='i';$v[]=(int)$c['ultimo_movimiento'];}
        else {$time=(string)$c['fecha'].' '.((string)$c['hora']?:'00:00:00');$sql.=" AND TIMESTAMP($alias.fecha_pago,COALESCE($alias.hora_pago,'00:00:00')) $op ?";$types.='s';$v[]=$time;}
    }
    return [$sql,$types,$v];
}
function caja_totals($period) {
    [$where,$types,$values]=caja_where($period);
    $methods=caja_rows("SELECT forma_pago,COALESCE(SUM(CASE WHEN ROUND(importe_pago*100)*signo>=0 THEN ROUND(importe_pago*100)*signo ELSE 0 END),0) AS income,COALESCE(SUM(CASE WHEN ROUND(importe_pago*100)*signo<0 THEN -ROUND(importe_pago*100)*signo ELSE 0 END),0) AS expense,COUNT(*) AS n FROM pagos_a_cuenta p WHERE $where GROUP BY forma_pago",$types,$values);
    $result=['income'=>0,'expense'=>0,'cash_net'=>0,'mov_in'=>0,'mov_out'=>0,'cash'=>0,'n'=>0,'methods'=>$methods];
    foreach($methods as $row){$result['income']+=(int)$row['income'];$result['expense']+=(int)$row['expense'];$result['n']+=(int)$row['n'];if((string)$row['forma_pago']==='0')$result['cash_net']+=(int)$row['income']-(int)$row['expense'];}
    $mov=caja_rows('SELECT COALESCE(SUM(CASE WHEN signo>0 THEN ROUND(importe*100)*signo ELSE 0 END),0) AS incoming,COALESCE(SUM(CASE WHEN signo<0 THEN -ROUND(importe*100)*signo ELSE 0 END),0) AS outgoing FROM movimientos_caja WHERE id_centro=? AND id_cierre_anterior=?','ii',[$period['centre'],$period['previous_id']])[0];
    $result['mov_in']=(int)$mov['incoming'];$result['mov_out']=(int)$mov['outgoing'];$result['cash']=$result['cash_net']+$result['mov_in']-$result['mov_out'];
    return $result;
}
function caja_money($cents){return number_format($cents/100,2,',','.').' &euro;';}
function caja_decimal($cents){return number_format($cents/100,2,'.','');}
function caja_parse_money($value,$zero=false) {
    if(!is_scalar($value))throw new InvalidArgumentException("Escribe un importe v\u{00e1}lido.");
    $v=trim((string)$value);
    if(preg_match('/^\d{1,3}(?:\.\d{3})+,\d{1,2}$/D',$v))$v=str_replace('.','',$v);
    $v=str_replace(',','.',$v);
    if(!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/D',$v))throw new InvalidArgumentException("Escribe un importe v\u{00e1}lido, con un m\u{00e1}ximo de dos decimales.");
    $parts=explode('.',$v);$cents=(int)$parts[0]*100+(int)str_pad($parts[1]??'',2,'0');
    if($cents<($zero?0:1)||$cents>999999999)throw new InvalidArgumentException('El importe debe ser mayor que cero y estar dentro del rango permitido.');
    return $cents;
}
function caja_methods(){return [0=>'Efectivo',1=>"Domiciliaci\u{00f3}n",2=>'Transferencia',3=>"Tal\u{00f3}n",4=>"Pagar\u{00e9}",5=>"Ticket guarder\u{00ed}a",6=>'Ingreso en cuenta'];}
function caja_method($id){return caja_methods()[$id]??('Otra forma ('.$id.')');}
function caja_token($action,$centre,$previous) {
    $token=bin2hex(random_bytes(24));$_SESSION['_caja_actions'][$token]=['action'=>$action,'centre'=>(int)$centre,'previous'=>(int)$previous,'expires'=>time()+1800];
    foreach($_SESSION['_caja_actions'] as $key=>$v)if($v['expires']<time())unset($_SESSION['_caja_actions'][$key]);
    return $token;
}
function caja_post_guard($action,$centre) {
    app_check_csrf();$token=(string)app_value($_POST,'caja_token');$state=$_SESSION['_caja_actions'][$token]??null;
    if(!$state||$state['action']!==$action||$state['centre']!==(int)$centre||$state['expires']<time())caja_fail('Este formulario ya se ha utilizado o ha caducado. Vuelve a abrirlo.',409);
    return [$token,$state];
}
function caja_write($centre,$previous,callable $operation) {
    $db=app_db();$lock='neokids_caja_'.$centre;
    if((int)caja_rows('SELECT GET_LOCK(?,5) AS locked','s',[$lock])[0]['locked']!==1)throw new RuntimeException("La caja se est\u{00e1} actualizando. Vuelve a intentarlo.");
    try{
        if(!$db->begin_transaction())throw new RuntimeException('No se ha podido iniciar la operacion de caja.');
        // Range locks also serialize invoice collections from the older billing pages.
        caja_rows('SELECT id_pago FROM pagos_a_cuenta WHERE id_centro=? ORDER BY fecha_pago,hora_pago,id_pago FOR UPDATE','i',[$centre]);
        caja_rows('SELECT id_movimiento FROM movimientos_caja WHERE id_centro=? FOR UPDATE','i',[$centre]);
        $period=caja_period($centre);
        if($period['previous_id']!==(int)$previous)throw new InvalidArgumentException('La caja ya se ha cerrado desde que abriste esta pantalla. Recarga antes de continuar.');
        $result=$operation($period);if(!$db->commit())throw new RuntimeException('No se ha podido confirmar la operacion de caja.');return $result;
    }catch(Throwable $e){$db->rollback();throw $e;}finally{caja_query('SELECT RELEASE_LOCK(?)','s',[$lock]);}
}
function caja_back($centre,$notice='saved') {header('Location: listado_caja.php?id_centro_caja='.(int)$centre.'&resultado='.rawurlencode($notice),true,303);exit;}
function caja_header($title) {
    header('Content-Type: text/html; charset=UTF-8');
    ?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?=app_escape($title)?> &middot; Neo Kids</title><link rel="stylesheet" href="/caja/caja.css?v=20260930"><script src="/caja/caja.js?v=20260930" defer></script></head><body class="caja-app"><header class="caja-top"><a class="caja-brand" href="/">Neo Kids <span>Gesti&oacute;n de piscina</span></a><nav aria-label="Navegaci&oacute;n"><a href="/">Inicio</a><a href="/facturacion/gestion_facturacion.php">Facturaci&oacute;n</a><a href="/caja/listado_caja.php">Caja</a><a href="/caja/resumen_caja.php">Historial de cobros</a></nav></header><main class="caja-main"><div class="caja-heading"><div><p class="caja-eyebrow">Control de caja</p><h1><?=app_escape($title)?></h1></div></div><?php
}
function caja_footer(){echo '</main><footer class="caja-footer">Neo Kids &middot; &Aacute;rea de personal</footer></body></html>';}
function caja_url($changes=[]) {$query=array_merge($_GET,$changes);return 'listado_caja.php?'.http_build_query($query);}
function caja_date($date){return $date&&$date!=='0000-00-00'?date('d/m/Y',strtotime($date)):'Sin fecha';}
