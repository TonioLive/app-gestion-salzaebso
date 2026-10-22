<?php
// Revalidate administrative access even when a previous login cookie is still valid.
// Preserve the contract rule used by the existing staff login, including open-ended contracts.
function pool_admin_account_valid($id){return (bool)app_rows("SELECT u.id_usuario FROM usuarios u WHERE u.id_usuario=? AND u.nivel_acceso=4 AND u.usuario<>'' AND u.pass<>'' AND EXISTS(SELECT 1 FROM personal_contratos c WHERE c.id_empleado=u.id_usuario AND (c.fecha_fin>=CURDATE() OR c.fecha_fin='0000-00-00' OR c.fecha_fin IS NULL OR c.fecha_inicio>CURDATE()))",'i',array((int)$id));}
function pool_require_administration(){if((int)$GLOBALS['nivel_acceso']!==4||!pool_admin_account_valid($GLOBALS['idUsuario'])){http_response_code(403);exit('Acceso reservado a una cuenta de administración autorizada.');}}
