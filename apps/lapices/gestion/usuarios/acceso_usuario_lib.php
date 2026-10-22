<?php
function gestion_acceso_permitido($usuario)
{
    $nivel = isset($GLOBALS['nivel_acceso']) ? (int) $GLOBALS['nivel_acceso'] : 0;
    if (empty($GLOBALS['permitido']) || empty($GLOBALS['centro_actual']) || !in_array($nivel, array(3,4), true)) { return false; }
    return $nivel === 4 || ((int) $usuario['nivel_acceso'] < 4 && (int) $usuario['id_centro'] === (int) $GLOBALS['centro_actual']);
}

function gestion_acceso_usuario($id)
{
    $result = db_query_params('SELECT * FROM usuarios WHERE id_usuario=? AND tipo_inscripcion>=3', 'i', array((int) $id));
    $usuario = $result ? db_fetch_assoc($result) : false;
    if (!$usuario || !gestion_acceso_permitido($usuario)) { throw new DomainException('No tienes permiso para gestionar este acceso.'); }
    return $usuario;
}

function gestion_acceso_password($nueva, $actual)
{
    $nueva = (string) $nueva;
    if ($nueva === '' || $nueva === '******') { return (string) $actual; }
    if (strlen($nueva) > 72 || strpos($nueva, "\0") !== false || !preg_match('//u', $nueva)) {
        throw new InvalidArgumentException('La contraseña admite hasta 72 bytes y debe contener texto válido.');
    }
    $hash = password_hash($nueva, PASSWORD_BCRYPT, array('cost'=>10));
    if ($hash === false) { throw new RuntimeException('No se ha podido guardar la contraseña.'); }
    return $hash;
}
