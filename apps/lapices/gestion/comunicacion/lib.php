<?php
require_once dirname(__DIR__) . '/mensajes/centro_mensajes_lib.php';

function communication_db()
{
    $db = db_link();
    if (!($db instanceof mysqli) || (mysqli_character_set_name($db) !== 'utf8mb4' && !mysqli_set_charset($db, 'utf8mb4'))) {
        throw new RuntimeException('No se puede conectar con la comunicación del centro.');
    }
    return $db;
}

function communication_query($sql, $types = '', $values = array())
{
    $result = db_query_params($sql, $types, $values, communication_db());
    if ($result === false) {
        throw new RuntimeException('No se ha podido completar la operación.');
    }
    return $result;
}

function communication_rows($sql, $types = '', $values = array())
{
    $result = communication_query($sql, $types, $values);
    $rows = array();
    while ($row = db_fetch_assoc($result)) { $rows[] = $row; }
    db_free_result($result);
    return $rows;
}

function communication_staff_access()
{
    return staff_messages_user_can_access() && !empty($GLOBALS['idUsuario']);
}

function communication_can_publish()
{
    return communication_staff_access()
        && ((int) $GLOBALS['nivel_acceso'] >= 4 || !empty($GLOBALS['zona_mensajes']));
}

function communication_staff_scope()
{
    if (!communication_staff_access()) {
        throw new DomainException('No tienes acceso a la comunicación de este centro.');
    }
    return array((int) $GLOBALS['centro_actual'], (int) $GLOBALS['idUsuario']);
}

function communication_text($value, $maximum)
{
    $value = trim(str_replace(array("\r\n", "\r"), "\n", (string) $value));
    if ($value === '' || !preg_match('//u', $value) || mb_strlen($value, 'UTF-8') > $maximum) {
        throw new InvalidArgumentException('Completa el texto y respeta el límite de caracteres.');
    }
    return $value;
}

function communication_key($value)
{
    if (!preg_match('/^[a-zA-Z0-9_-]{16,64}$/', (string) $value)) {
        throw new InvalidArgumentException('Recarga la página antes de enviar.');
    }
    return (string) $value;
}

function communication_chat($after = 0, $before = 0)
{
    list($centre, $user) = communication_staff_scope();
    $filter = '';
    $values = array($centre);
    $types = 'i';
    if ($after > 0) { $filter = ' AND m.id>?'; $values[] = (int) $after; $types .= 'i'; }
    elseif ($before > 0) { $filter = ' AND m.id<?'; $values[] = (int) $before; $types .= 'i'; }
    $rows = communication_rows(
        'SELECT m.id, m.autor_id, m.texto, m.creado, COALESCE(NULLIF(u.Nombre,\'\'),\'Equipo\') AS autor '
        . 'FROM comunicacion_equipo m LEFT JOIN usuarios u ON u.id_usuario=m.autor_id '
        . 'WHERE m.centro_id=?' . $filter . ' ORDER BY m.id ' . ($after > 0 ? 'ASC' : 'DESC') . ' LIMIT 101',
        $types, $values
    );
    $more = count($rows) > 100;
    if ($more) { array_pop($rows); }
    if ($after <= 0) { $rows = array_reverse($rows); }
    foreach ($rows as &$row) {
        $row['id'] = (int) $row['id'];
        $row['own'] = (int) $row['autor_id'] === $user;
        unset($row['autor_id']);
    }
    unset($row);
    return array('messages' => $rows, 'more' => $more);
}

function communication_send($text, $key)
{
    list($centre, $user) = communication_staff_scope();
    $text = communication_text($text, 4000);
    $key = communication_key($key);
    communication_query(
        'INSERT INTO comunicacion_equipo (centro_id,autor_id,texto,clave_cliente,creado) VALUES (?,?,?,?,NOW()) '
        . 'ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)', 'iiss', array($centre, $user, $text, $key)
    );
    return (int) mysqli_insert_id(communication_db());
}

function communication_mark_chat_read($last)
{
    list($centre, $user) = communication_staff_scope();
    $rows = communication_rows('SELECT id FROM comunicacion_equipo WHERE centro_id=? AND id=?', 'ii', array($centre, (int) $last));
    if (!$rows) { throw new InvalidArgumentException('No se encuentra el mensaje.'); }
    communication_query(
        'INSERT INTO comunicacion_equipo_lecturas (centro_id,usuario_id,ultimo_id) VALUES (?,?,?) '
        . 'ON DUPLICATE KEY UPDATE ultimo_id=GREATEST(ultimo_id,VALUES(ultimo_id))', 'iii', array($centre, $user, (int) $last)
    );
}

function communication_notifications($after)
{
    list($centre, $user) = communication_staff_scope();
    $summary = communication_rows(
        'SELECT COUNT(*) AS unread, COALESCE(MAX(m.id),0) AS latest_unread FROM comunicacion_equipo m '
        . 'LEFT JOIN comunicacion_equipo_lecturas r ON r.centro_id=m.centro_id AND r.usuario_id=? '
        . 'WHERE m.centro_id=? AND m.autor_id<>? AND m.id>COALESCE(r.ultimo_id,0)', 'iii', array($user, $centre, $user)
    );
    $last = communication_rows('SELECT COALESCE(MAX(id),0) AS id FROM comunicacion_equipo WHERE centro_id=?', 'i', array($centre));
    $events = array();
    $more = false;
    if ($after >= 0) {
        $events = communication_rows(
            'SELECT m.id,m.texto,m.creado,COALESCE(NULLIF(u.Nombre,\'\'),\'Equipo\') AS autor '
            . 'FROM comunicacion_equipo m LEFT JOIN usuarios u ON u.id_usuario=m.autor_id '
            . 'WHERE m.centro_id=? AND m.autor_id<>? AND m.id>? ORDER BY m.id LIMIT 101',
            'iii', array($centre, $user, (int) $after)
        );
        $more = count($events) > 100;
        if ($more) { array_pop($events); }
    }
    return array('unread' => (int) $summary[0]['unread'], 'latest' => (int) $last[0]['id'], 'events' => $events, 'more' => $more);
}

function communication_notices($archived = false, $before = 0)
{
    list($centre, $user) = communication_staff_scope();
    return communication_rows(
        'SELECT n.id,n.titulo,n.texto,n.creado,n.archivado,COALESCE(u.Nombre,\'Centro\') AS autor '
        . 'FROM comunicacion_avisos n LEFT JOIN usuarios u ON u.id_usuario=n.autor_id '
        . 'WHERE n.centro_id=? AND n.archivado IS ' . ($archived ? 'NOT NULL' : 'NULL')
        . ' AND (?=0 OR n.id<?) ORDER BY n.id DESC LIMIT 51', 'iii', array($centre, (int) $before, (int) $before)
    );
}

function communication_publish($title, $text, $key)
{
    list($centre, $user) = communication_staff_scope();
    if (!communication_can_publish()) { throw new DomainException('No tienes permiso para publicar avisos.'); }
    communication_query(
        'INSERT INTO comunicacion_avisos (centro_id,autor_id,titulo,texto,clave_cliente,creado) VALUES (?,?,?,?,?,NOW()) '
        . 'ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)', 'iisss',
        array($centre, $user, communication_text($title, 160), communication_text($text, 8000), communication_key($key))
    );
    return (int) mysqli_insert_id(communication_db());
}

function communication_archive($id, $archive)
{
    list($centre, $user) = communication_staff_scope();
    if (!communication_can_publish()) { throw new DomainException('No tienes permiso para gestionar avisos.'); }
    communication_query('UPDATE comunicacion_avisos SET archivado=' . ($archive ? 'NOW()' : 'NULL') . ' WHERE id=? AND centro_id=?', 'ii', array((int) $id, $centre));
}

function communication_parent_id()
{
    if (empty($_SESSION['permitido_padre']) || empty($_SESSION['nombre_padre'])) { return 0; }
    $parents = communication_rows('SELECT id_usuario FROM padres WHERE usuario_webcam=? LIMIT 1', 's', array((string) $_SESSION['nombre_padre']));
    return $parents ? (int) $parents[0]['id_usuario'] : 0;
}

function communication_parent_scope_sql()
{
    return 'EXISTS (SELECT 1 FROM padres p INNER JOIN alumnos a ON a.id_usuario=p.padre_de '
        . 'WHERE p.id_usuario=? AND a.id_centro=n.centro_id AND a.fecha_matricula<=CURDATE() '
        . "AND (a.fecha_baja IS NULL OR a.fecha_baja='0000-00-00' OR a.fecha_baja>=CURDATE()) "
        . 'AND (a.fecha_baja IS NULL OR a.fecha_matricula<>a.fecha_baja))';
}

function communication_parent_notices($parent, $before = 0)
{
    if ($parent <= 0) { throw new DomainException('Inicia sesión como familiar.'); }
    $rows = communication_rows(
        'SELECT n.id,n.titulo,n.texto,n.creado,c.nombre_centro AS centro, (r.aviso_id IS NULL) AS unread '
        . 'FROM comunicacion_avisos n INNER JOIN centros c ON c.id_centro=n.centro_id '
        . 'LEFT JOIN comunicacion_avisos_lecturas r ON r.aviso_id=n.id AND r.familiar_id=? '
        . 'WHERE n.archivado IS NULL AND ' . communication_parent_scope_sql()
        . ' AND (?=0 OR n.id<?) ORDER BY n.id DESC LIMIT 51', 'iiii', array($parent, $parent, (int) $before, (int) $before)
    );
    $count = communication_rows(
        'SELECT COUNT(*) AS total FROM comunicacion_avisos n WHERE n.archivado IS NULL AND '
        . communication_parent_scope_sql() . ' AND NOT EXISTS (SELECT 1 FROM comunicacion_avisos_lecturas r WHERE r.aviso_id=n.id AND r.familiar_id=?)',
        'ii', array($parent, $parent)
    );
    return array('notices' => $rows, 'unread' => (int) $count[0]['total']);
}

function communication_parent_read($parent, $id)
{
    if ($parent <= 0) { throw new DomainException('Inicia sesión como familiar.'); }
    $allowed = communication_rows('SELECT n.id FROM comunicacion_avisos n WHERE n.id=? AND n.archivado IS NULL AND ' . communication_parent_scope_sql(), 'ii', array((int) $id, $parent));
    if (!$allowed) { throw new DomainException('Este aviso no está disponible.'); }
    communication_query('INSERT IGNORE INTO comunicacion_avisos_lecturas (aviso_id,familiar_id,leido) VALUES (?,?,NOW())', 'ii', array((int) $id, $parent));
}
