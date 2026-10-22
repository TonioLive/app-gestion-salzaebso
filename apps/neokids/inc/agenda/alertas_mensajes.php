<?php
require_once dirname(__DIR__,2).'/configuracion/family-messages.php';
$familyMessagePending=fm_unread_count();
if($familyMessagePending){
?><div style="padding:14px 18px;border:1px solid #a5d8ef;background:#e6f5fd;border-radius:12px;margin:12px 0"><a href="/gestion/mensajes/bandeja.php?filtro=pendientes" style="color:#075985;font-weight:bold"><?=(int)$familyMessagePending?> conversaciones con familias pendientes de leer</a></div><?php
}
unset($familyMessagePending);
