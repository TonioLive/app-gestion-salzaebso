<?php
/**
 * Capa de datos del centro de mensajes del personal.
 *
 * Mantiene el historial de las tablas antiguas, pero lo presenta agrupado por
 * alumno/familia y aplica los permisos de centro y aula antes de devolver datos.
 */

if (!defined('STAFF_MESSAGES_READ_RECEIPT_START')) {
    define('STAFF_MESSAGES_READ_RECEIPT_START', '2026-09-07');
}

if (!function_exists('staff_messages_user_can_access')) {
    function staff_messages_user_can_access()
    {
        $level = isset($GLOBALS['nivel_acceso']) ? (int) $GLOBALS['nivel_acceso'] : 0;
        $allowed = !empty($GLOBALS['permitido'])
            && !empty($GLOBALS['centro_actual'])
            && $level >= 2
            && $level < 10;

        if (!$allowed) {
            return false;
        }

        if ($level === 2 || $level === 3) {
            return true;
        }

        return !empty($GLOBALS['zona_mensajes']) || $level >= 4;
    }
}

if (!function_exists('staff_messages_is_educator')) {
    function staff_messages_is_educator()
    {
        return isset($GLOBALS['nivel_acceso'])
            && (int) $GLOBALS['nivel_acceso'] === 2;
    }
}

if (!function_exists('staff_messages_query_all')) {
    function staff_messages_query_all($database, $sql, $types, $parameters)
    {
        $rows = array();
        if (!($database instanceof mysqli)) {
            return $rows;
        }

        $statement = mysqli_prepare($database, $sql);
        if (!$statement) {
            return $rows;
        }

        if ($types !== '') {
            $bind = array($statement, $types);
            foreach ($parameters as $index => $value) {
                $parameters[$index] = $value;
                $bind[] = &$parameters[$index];
            }
            if (!call_user_func_array('mysqli_stmt_bind_param', $bind)) {
                mysqli_stmt_close($statement);
                return $rows;
            }
        }

        if (!mysqli_stmt_execute($statement)) {
            mysqli_stmt_close($statement);
            return $rows;
        }

        $result = mysqli_stmt_get_result($statement);
        if ($result instanceof mysqli_result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            mysqli_free_result($result);
        }
        mysqli_stmt_close($statement);

        return $rows;
    }
}

if (!function_exists('staff_messages_execute')) {
    function staff_messages_execute($database, $sql, $types, $parameters)
    {
        if (!($database instanceof mysqli)) {
            return false;
        }

        $statement = mysqli_prepare($database, $sql);
        if (!$statement) {
            return false;
        }

        if ($types !== '') {
            $bind = array($statement, $types);
            foreach ($parameters as $index => $value) {
                $parameters[$index] = $value;
                $bind[] = &$parameters[$index];
            }
            if (!call_user_func_array('mysqli_stmt_bind_param', $bind)) {
                mysqli_stmt_close($statement);
                return false;
            }
        }

        $executed = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
        return $executed;
    }
}

if (!function_exists('staff_messages_classrooms')) {
    function staff_messages_classrooms($database)
    {
        static $cache = array();
        $centreId = isset($GLOBALS['centro_actual']) ? (int) $GLOBALS['centro_actual'] : 0;
        $userId = isset($GLOBALS['idUsuario']) ? (int) $GLOBALS['idUsuario'] : 0;
        $today = date('Y-m-d');
        if ($centreId <= 0 || !($database instanceof mysqli)) {
            return array();
        }
        $level = isset($GLOBALS['nivel_acceso']) ? (int) $GLOBALS['nivel_acceso'] : 0;
        $cacheKey = spl_object_hash($database) . ':' . $centreId . ':' . $userId
            . ':' . $level . ':' . $today;
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $accessSql = '';
        $types = 'ssssii';
        $parameters = array($today, $today, $today, $today, $centreId, 0);
        if (staff_messages_is_educator()) {
            $accessSql = ' AND (ca.docente_titular=? OR EXISTS ('
                . 'SELECT 1 FROM personal_historial ph '
                . 'WHERE ph.id_empleado=? AND ph.id_aula=ca.id_aula '
                . 'AND (ph.id_escuela IS NULL OR ph.id_escuela=0 OR ph.id_escuela=ca.id_centro) '
                . 'AND (ph.fecha_inicio IS NULL OR ph.fecha_inicio<=?) '
                . "AND (ph.fecha_fin IS NULL OR ph.fecha_fin='0000-00-00' OR ph.fecha_fin>=?)"
                . '))';
            $types = 'ssssiiiiss';
            $parameters = array(
                $today,
                $today,
                $today,
                $today,
                $centreId,
                0,
                $userId,
                $userId,
                $today,
                $today,
            );
        }

        $sql = 'SELECT ca.id_aula, ca.nombre, ca.id_curso, '
            . 'COUNT(DISTINCT a.id_usuario) AS total_alumnos, '
            . 'MAX(CASE WHEN c.fecha_inicio<=? AND c.fecha_fin>=? THEN 1 ELSE 0 END) AS curso_activo '
            . 'FROM centro_aulas ca '
            . 'LEFT JOIN cursos_academicos c ON c.id_curso=ca.id_curso '
            . 'LEFT JOIN alumnos a ON a.aula=ca.id_aula AND a.id_centro=ca.id_centro '
            . 'AND a.fecha_matricula<=? '
            . "AND (a.fecha_baja IS NULL OR a.fecha_baja='0000-00-00' OR a.fecha_baja>=?) "
            . 'WHERE ca.id_centro=? AND ca.tipo=?' . $accessSql . ' '
            . 'GROUP BY ca.id_aula, ca.nombre, ca.id_curso '
            . 'HAVING total_alumnos>0 OR curso_activo=1 '
            . 'ORDER BY curso_activo DESC, ca.nombre';

        $rows = staff_messages_query_all($database, $sql, $types, $parameters);

        // Algunos cursos históricos no tienen fechas consistentes. Si ocurre,
        // ofrecemos las aulas accesibles para no dejar a la educadora sin chats.
        if (count($rows) === 0) {
            $fallbackAccess = '';
            $fallbackTypes = 'ii';
            $fallbackParameters = array($centreId, 0);
            if (staff_messages_is_educator()) {
                $fallbackAccess = ' AND (ca.docente_titular=? OR EXISTS ('
                    . 'SELECT 1 FROM personal_historial ph '
                    . 'WHERE ph.id_empleado=? AND ph.id_aula=ca.id_aula '
                    . 'AND (ph.id_escuela IS NULL OR ph.id_escuela=0 OR ph.id_escuela=ca.id_centro)'
                    . '))';
                $fallbackTypes = 'iiii';
                $fallbackParameters = array($centreId, 0, $userId, $userId);
            }
            $rows = staff_messages_query_all(
                $database,
                'SELECT ca.id_aula, ca.nombre, ca.id_curso, 0 AS total_alumnos, 0 AS curso_activo '
                    . 'FROM centro_aulas ca WHERE ca.id_centro=? AND ca.tipo=?'
                    . $fallbackAccess . ' ORDER BY ca.id_curso DESC, ca.nombre',
                $fallbackTypes,
                $fallbackParameters
            );
        }

        foreach ($rows as $index => $row) {
            $rows[$index]['id_aula'] = (int) $row['id_aula'];
            $rows[$index]['id_curso'] = (int) $row['id_curso'];
            $rows[$index]['total_alumnos'] = (int) $row['total_alumnos'];
            $rows[$index]['curso_activo'] = (int) $row['curso_activo'];
        }
        $cache[$cacheKey] = $rows;
        return $rows;
    }
}

if (!function_exists('staff_messages_classroom_ids')) {
    function staff_messages_classroom_ids($database)
    {
        $ids = array();
        foreach (staff_messages_classrooms($database) as $classroom) {
            $ids[] = (int) $classroom['id_aula'];
        }
        return array_values(array_unique($ids));
    }
}

if (!function_exists('staff_messages_students')) {
    function staff_messages_students($database, $classroomId)
    {
        $classroomIds = staff_messages_classroom_ids($database);
        $classroomId = (int) $classroomId;
        if ($classroomId > 0) {
            if (!in_array($classroomId, $classroomIds, true)) {
                return array();
            }
            $classroomIds = array($classroomId);
        }
        if (count($classroomIds) === 0) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($classroomIds), '?'));
        $today = date('Y-m-d');
        $centreId = (int) $GLOBALS['centro_actual'];
        $types = 'i' . str_repeat('i', count($classroomIds)) . 'ss';
        $parameters = array_merge(array($centreId), $classroomIds, array($today, $today));
        $rows = staff_messages_query_all(
            $database,
            'SELECT a.id_usuario AS id_alumno, ca.id_aula, ca.nombre AS nombre_aula, '
                . 'u.Nombre, u.Apellido1, u.Apellido2, '
                . 'p.id_usuario AS id_padre, pu.Nombre AS nombre_padre, '
                . 'pu.Apellido1 AS apellido1_padre, pu.Apellido2 AS apellido2_padre '
                . 'FROM alumnos a '
                . 'INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
                . 'INNER JOIN centro_aulas ca ON ca.id_aula=a.aula '
                . 'LEFT JOIN padres p ON p.padre_de=a.id_usuario '
                . 'LEFT JOIN usuarios pu ON pu.id_usuario=p.id_usuario '
                . 'WHERE a.id_centro=? AND a.aula IN (' . $placeholders . ') '
                . 'AND a.fecha_matricula<=? '
                . "AND (a.fecha_baja IS NULL OR a.fecha_baja='0000-00-00' OR a.fecha_baja>=?) "
                . 'ORDER BY u.Apellido1, u.Apellido2, u.Nombre, p.id_padre',
            $types,
            $parameters
        );

        $students = array();
        foreach ($rows as $row) {
            $studentId = (int) $row['id_alumno'];
            if (!isset($students[$studentId])) {
                $studentName = trim($row['Nombre'] . ' ' . $row['Apellido1'] . ' ' . $row['Apellido2']);
                $students[$studentId] = array(
                    'student_id' => $studentId,
                    'student_name' => $studentName,
                    'classroom_id' => (int) $row['id_aula'],
                    'classroom_name' => trim((string) $row['nombre_aula']),
                    'parents' => array(),
                );
            }
            $parentId = (int) $row['id_padre'];
            if ($parentId > 0) {
                $parentName = trim(
                    $row['nombre_padre'] . ' '
                    . $row['apellido1_padre'] . ' '
                    . $row['apellido2_padre']
                );
                if ($parentName === '') {
                    $parentName = 'Familiar';
                }
                $students[$studentId]['parents'][$parentId] = $parentName;
            }
        }

        foreach ($students as $studentId => $student) {
            $parentNames = array_values($student['parents']);
            $students[$studentId]['parent_count'] = count($parentNames);
            $students[$studentId]['family_name'] = count($parentNames) > 0
                ? implode(' · ', $parentNames)
                : 'Sin familiar vinculado';
            unset($students[$studentId]['parents']);
        }

        return array_values($students);
    }
}

if (!function_exists('staff_messages_find_student')) {
    function staff_messages_find_student($database, $studentId)
    {
        $studentId = (int) $studentId;
        $classroomIds = staff_messages_classroom_ids($database);
        if ($studentId <= 0 || count($classroomIds) === 0) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($classroomIds), '?'));
        $types = 'ii' . str_repeat('i', count($classroomIds));
        $parameters = array_merge(
            array($studentId, (int) $GLOBALS['centro_actual']),
            $classroomIds
        );
        $rows = staff_messages_query_all(
            $database,
            'SELECT a.id_usuario AS id_alumno, a.aula AS id_aula, ca.nombre AS nombre_aula, '
                . 'u.Nombre, u.Apellido1, u.Apellido2 '
                . 'FROM alumnos a INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
                . 'INNER JOIN centro_aulas ca ON ca.id_aula=a.aula '
                . 'WHERE a.id_usuario=? AND a.id_centro=? '
                . 'AND a.aula IN (' . $placeholders . ') '
                . 'ORDER BY (a.fecha_matricula<=CURRENT_DATE AND a.fecha_baja>=CURRENT_DATE) DESC, '
                . 'a.id_curso DESC LIMIT 1',
            $types,
            $parameters
        );
        if (count($rows) === 0) {
            return false;
        }

        $row = $rows[0];
        $parents = staff_messages_query_all(
            $database,
            'SELECT p.id_usuario, u.Nombre, u.Apellido1, u.Apellido2 '
                . 'FROM padres p LEFT JOIN usuarios u ON u.id_usuario=p.id_usuario '
                . 'WHERE p.padre_de=? ORDER BY p.id_padre',
            'i',
            array($studentId)
        );
        $parentNames = array();
        foreach ($parents as $parent) {
            $name = trim($parent['Nombre'] . ' ' . $parent['Apellido1'] . ' ' . $parent['Apellido2']);
            if ($name !== '') {
                $parentNames[] = $name;
            }
        }

        return array(
            'student_id' => $studentId,
            'student_name' => trim($row['Nombre'] . ' ' . $row['Apellido1'] . ' ' . $row['Apellido2']),
            'classroom_id' => (int) $row['id_aula'],
            'classroom_name' => trim((string) $row['nombre_aula']),
            'parent_count' => count($parentNames),
            'family_name' => count($parentNames) > 0
                ? implode(' · ', $parentNames)
                : 'Sin familiar vinculado',
        );
    }
}

if (!function_exists('staff_messages_event_rows')) {
    function staff_messages_event_rows($database, $studentIds, $ascending)
    {
        $studentIds = array_values(array_unique(array_map('intval', $studentIds)));
        if (count($studentIds) === 0) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        $centreId = (int) $GLOBALS['centro_actual'];
        $roleFilter = staff_messages_is_educator()
            ? ' AND (n.tipo_nota=2 OR COALESCE(n.destino, 0)=0)'
            : '';
        $initialSql = 'SELECT n.id_alumno, n.id_nota_padre AS thread_id, '
            . "CONCAT('n:', n.id_nota_padre) AS event_id, 0 AS event_kind, "
            . 'n.id_remitente AS author_id, '
            . "TRIM(CONCAT_WS(' ', au.Nombre, au.Apellido1, au.Apellido2)) AS author_name, "
            . 'COALESCE(n.asunto, \'\') AS subject, COALESCE(n.texto_nota, \'\') AS body, '
            . "CONCAT(n.fecha, ' ', n.hora) AS activity_at, "
            . 'CASE WHEN n.tipo_nota=2 THEN 0 ELSE 1 END AS family_side, '
            . 'n.tipo_nota AS message_type, n.destino, n.estado AS read_state, '
            . 'COALESCE(n.leido_por, \'\') AS read_by, n.estado AS thread_state, '
            . 'COALESCE(n.medicamento, \'\') AS medicine, COALESCE(n.cantidad, \'\') AS dose, '
            . 'COALESCE(n.fecha_medicacion, \'\') AS medicine_start, '
            . 'COALESCE(n.fecha_fin_medicacion, \'\') AS medicine_end, '
            . 'COALESCE(n.hora_medicacion, \'\') AS medicine_time '
            . 'FROM agenda_notas_padres n '
            . 'LEFT JOIN usuarios au ON au.id_usuario=n.id_remitente '
            . 'WHERE n.id_alumno IN (' . $placeholders . ') '
            . 'AND (n.id_centro_alumno=? OR n.id_centro_alumno=0)' . $roleFilter;
        $replySql = 'SELECT n.id_alumno, n.id_nota_padre AS thread_id, '
            . "CONCAT('r:', r.id_respuesta) AS event_id, 1 AS event_kind, "
            . 'r.respondido_por AS author_id, '
            . "TRIM(CONCAT_WS(' ', au.Nombre, au.Apellido1, au.Apellido2)) AS author_name, "
            . 'COALESCE(n.asunto, \'\') AS subject, COALESCE(r.texto_respuesta, \'\') AS body, '
            . "CONCAT(r.fecha_respuesta, ' ', r.hora_respuesta) AS activity_at, "
            . 'CASE WHEN COALESCE(r.direccion, 0)=0 THEN 1 ELSE 0 END AS family_side, '
            . 'n.tipo_nota AS message_type, n.destino, r.leida AS read_state, '
            . 'COALESCE(r.leido_por, \'\') AS read_by, n.estado AS thread_state, '
            . "'' AS medicine, '' AS dose, '' AS medicine_start, '' AS medicine_end, '' AS medicine_time "
            . 'FROM agenda_notas_padres_respuestas r '
            . 'INNER JOIN agenda_notas_padres n ON n.id_nota_padre=r.id_nota '
            . 'LEFT JOIN usuarios au ON au.id_usuario=r.respondido_por '
            . 'WHERE n.id_alumno IN (' . $placeholders . ') '
            . 'AND (n.id_centro_alumno=? OR n.id_centro_alumno=0)' . $roleFilter;
        $sql = '(' . $initialSql . ') UNION ALL (' . $replySql . ') '
            . 'ORDER BY activity_at ' . ($ascending ? 'ASC' : 'DESC')
            . ', event_kind ' . ($ascending ? 'ASC' : 'DESC') . ', event_id '
            . ($ascending ? 'ASC' : 'DESC');
        $parameters = array_merge(
            $studentIds,
            array($centreId),
            $studentIds,
            array($centreId)
        );
        $types = str_repeat('i', count($studentIds)) . 'i'
            . str_repeat('i', count($studentIds)) . 'i';

        return staff_messages_query_all($database, $sql, $types, $parameters);
    }
}

if (!function_exists('staff_messages_is_unread_event')) {
    function staff_messages_is_unread_event($event)
    {
        if ((int) $event['family_side'] !== 1) {
            return false;
        }

        $userId = (int) $GLOBALS['idUsuario'];
        $eventDate = substr((string) $event['activity_at'], 0, 10);
        if ($eventDate >= STAFF_MESSAGES_READ_RECEIPT_START) {
            return strpos((string) $event['read_by'], '[' . $userId . ']') === false;
        }

        return (int) $event['read_state'] === 0;
    }
}

if (!function_exists('staff_messages_unread_rows')) {
    function staff_messages_unread_rows($database, $studentIds)
    {
        $studentIds = array_values(array_unique(array_map('intval', $studentIds)));
        if (count($studentIds) === 0) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        $centreId = (int) $GLOBALS['centro_actual'];
        $readToken = '%[' . (int) $GLOBALS['idUsuario'] . ']%';
        $educatorInitialFilter = staff_messages_is_educator()
            ? ' AND COALESCE(n.destino, 0)=0'
            : '';
        $educatorReplyFilter = staff_messages_is_educator()
            ? ' AND (n.tipo_nota=2 OR COALESCE(n.destino, 0)=0)'
            : '';

        $initialSql = 'SELECT n.id_alumno, n.id_nota_padre AS thread_id, '
            . 'COALESCE(n.asunto, \'\') AS subject, COALESCE(n.texto_nota, \'\') AS body, '
            . "CONCAT(n.fecha, ' ', n.hora) AS activity_at "
            . 'FROM agenda_notas_padres n '
            . 'WHERE n.id_alumno IN (' . $placeholders . ') '
            . 'AND (n.id_centro_alumno=? OR n.id_centro_alumno=0) '
            . 'AND n.tipo_nota<>2' . $educatorInitialFilter . ' '
            . 'AND ((n.fecha>=? AND COALESCE(n.leido_por, \'\') NOT LIKE ?) '
            . 'OR (n.fecha<? AND n.estado=0))';
        $replySql = 'SELECT n.id_alumno, n.id_nota_padre AS thread_id, '
            . 'COALESCE(n.asunto, \'\') AS subject, COALESCE(r.texto_respuesta, \'\') AS body, '
            . "CONCAT(r.fecha_respuesta, ' ', r.hora_respuesta) AS activity_at "
            . 'FROM agenda_notas_padres_respuestas r '
            . 'INNER JOIN agenda_notas_padres n ON n.id_nota_padre=r.id_nota '
            . 'WHERE n.id_alumno IN (' . $placeholders . ') '
            . 'AND (n.id_centro_alumno=? OR n.id_centro_alumno=0) '
            . 'AND COALESCE(r.direccion, 0)=0' . $educatorReplyFilter . ' '
            . 'AND ((r.fecha_respuesta>=? AND COALESCE(r.leido_por, \'\') NOT LIKE ?) '
            . 'OR (r.fecha_respuesta<? AND r.leida=0))';
        $sql = '(' . $initialSql . ') UNION ALL (' . $replySql . ') '
            . 'ORDER BY activity_at DESC, thread_id DESC';
        $parameters = array_merge(
            $studentIds,
            array($centreId, STAFF_MESSAGES_READ_RECEIPT_START, $readToken, STAFF_MESSAGES_READ_RECEIPT_START),
            $studentIds,
            array($centreId, STAFF_MESSAGES_READ_RECEIPT_START, $readToken, STAFF_MESSAGES_READ_RECEIPT_START)
        );
        $types = str_repeat('i', count($studentIds)) . 'isss'
            . str_repeat('i', count($studentIds)) . 'isss';

        return staff_messages_query_all($database, $sql, $types, $parameters);
    }
}

if (!function_exists('staff_messages_body_text')) {
    function staff_messages_body_text($value)
    {
        $value = preg_replace('/<br\s*\/?>/i', "\n", (string) $value);
        $value = preg_replace('/<\/p\s*>/i', "\n", $value);
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
        return trim($value);
    }
}

if (!function_exists('staff_messages_clean_preview')) {
    function staff_messages_clean_preview($value)
    {
        $value = staff_messages_body_text($value);
        $value = preg_replace('/\s+/u', ' ', $value);
        $value = trim($value);
        if (function_exists('mb_strlen') && mb_strlen($value, 'UTF-8') > 110) {
            return mb_substr($value, 0, 107, 'UTF-8') . '…';
        }
        return strlen($value) > 110 ? substr($value, 0, 107) . '...' : $value;
    }
}

if (!function_exists('staff_messages_conversations')) {
    function staff_messages_conversations($database, $classroomId)
    {
        $students = staff_messages_students($database, $classroomId);
        $studentIds = array();
        $byStudent = array();
        foreach ($students as $student) {
            $studentId = (int) $student['student_id'];
            $studentIds[] = $studentId;
            $student['last_at'] = '';
            $student['last_preview'] = 'Todavía no hay mensajes';
            $student['last_author'] = '';
            $student['unread_count'] = 0;
            $student['has_messages'] = false;
            $student['latest_thread_id'] = 0;
            $byStudent[$studentId] = $student;
        }

        foreach (staff_messages_event_rows($database, $studentIds, false) as $event) {
            $studentId = (int) $event['id_alumno'];
            if (!isset($byStudent[$studentId])) {
                continue;
            }
            if (!$byStudent[$studentId]['has_messages']) {
                $preview = staff_messages_clean_preview($event['body']);
                $byStudent[$studentId]['last_at'] = (string) $event['activity_at'];
                $byStudent[$studentId]['last_preview'] = $preview !== '' ? $preview : (string) $event['subject'];
                $byStudent[$studentId]['last_author'] = (int) $event['family_side'] === 1
                    ? 'Familia'
                    : ((int) $event['author_id'] === (int) $GLOBALS['idUsuario'] ? 'Tú' : 'Centro');
                $byStudent[$studentId]['has_messages'] = true;
                $byStudent[$studentId]['latest_thread_id'] = (int) $event['thread_id'];
            }
            if (staff_messages_is_unread_event($event)) {
                $byStudent[$studentId]['unread_count']++;
            }
        }

        $conversations = array_values($byStudent);
        usort($conversations, function ($left, $right) {
            if ($left['last_at'] === $right['last_at']) {
                return strcasecmp($left['student_name'], $right['student_name']);
            }
            if ($left['last_at'] === '') {
                return 1;
            }
            if ($right['last_at'] === '') {
                return -1;
            }
            return strcmp($right['last_at'], $left['last_at']);
        });

        return $conversations;
    }
}

if (!function_exists('staff_messages_chat')) {
    function staff_messages_chat($database, $studentId)
    {
        $student = staff_messages_find_student($database, $studentId);
        if ($student === false) {
            return false;
        }

        $events = array();
        $latestThreadId = 0;
        foreach (staff_messages_event_rows($database, array((int) $studentId), true) as $row) {
            if ((int) $row['event_kind'] === 0 && (int) $row['thread_state'] !== 2) {
                $latestThreadId = (int) $row['thread_id'];
            }
            $body = staff_messages_body_text($row['body']);
            $events[] = array(
                'id' => (string) $row['event_id'],
                'thread_id' => (int) $row['thread_id'],
                'side' => (int) $row['family_side'] === 1 ? 'family' : 'staff',
                'author' => trim((string) $row['author_name']) !== ''
                    ? trim((string) $row['author_name'])
                    : ((int) $row['family_side'] === 1 ? 'Familia' : 'Centro'),
                'subject' => (string) $row['subject'],
                'text' => $body,
                'at' => str_replace(' ', 'T', (string) $row['activity_at']),
                'is_initial' => (int) $row['event_kind'] === 0,
                'is_medicine' => (int) $row['message_type'] === 1,
                'medicine' => (string) $row['medicine'],
                'dose' => (string) $row['dose'],
                'medicine_start' => (string) $row['medicine_start'],
                'medicine_end' => (string) $row['medicine_end'],
                'medicine_time' => (string) $row['medicine_time'],
            );
        }

        return array(
            'student' => $student,
            'events' => $events,
            'latest_thread_id' => $latestThreadId,
        );
    }
}

if (!function_exists('staff_messages_mark_read')) {
    function staff_messages_mark_read($database, $studentId)
    {
        $student = staff_messages_find_student($database, $studentId);
        if ($student === false) {
            return false;
        }

        $userId = (int) $GLOBALS['idUsuario'];
        $centreId = (int) $GLOBALS['centro_actual'];
        $token = '[' . $userId . ']';
        $like = '%' . $token . '%';
        $roleFilter = staff_messages_is_educator()
            ? ' AND (tipo_nota=2 OR COALESCE(destino, 0)=0)'
            : '';
        $notesUpdated = staff_messages_execute(
            $database,
            'UPDATE agenda_notas_padres SET estado=1, '
                . 'leido_por=CASE WHEN COALESCE(leido_por, \'\') LIKE ? THEN leido_por '
                . 'ELSE CONCAT(COALESCE(leido_por, \'\'), ?) END '
                . 'WHERE id_alumno=? AND (id_centro_alumno=? OR id_centro_alumno=0) '
                . 'AND tipo_nota<>2' . $roleFilter,
            'ssii',
            array($like, $token, (int) $studentId, $centreId)
        );

        $replyRoleFilter = staff_messages_is_educator()
            ? ' AND (n.tipo_nota=2 OR COALESCE(n.destino, 0)=0)'
            : '';
        $repliesUpdated = staff_messages_execute(
            $database,
            'UPDATE agenda_notas_padres_respuestas r '
                . 'INNER JOIN agenda_notas_padres n ON n.id_nota_padre=r.id_nota '
                . 'SET r.leida=1, r.leido_por=CASE '
                . 'WHEN COALESCE(r.leido_por, \'\') LIKE ? THEN r.leido_por '
                . 'ELSE CONCAT(COALESCE(r.leido_por, \'\'), ?) END '
                . 'WHERE n.id_alumno=? AND (n.id_centro_alumno=? OR n.id_centro_alumno=0) '
                . 'AND COALESCE(r.direccion, 0)=0' . $replyRoleFilter,
            'ssii',
            array($like, $token, (int) $studentId, $centreId)
        );

        return $notesUpdated && $repliesUpdated;
    }
}

if (!function_exists('staff_messages_send')) {
    function staff_messages_send($database, $studentId, $threadId, $text, $subject)
    {
        $student = staff_messages_find_student($database, $studentId);
        $text = trim((string) $text);
        $subject = trim((string) $subject);
        $textLength = function_exists('mb_strlen')
            ? mb_strlen($text, 'UTF-8')
            : strlen($text);
        if ($student === false || $text === '' || $textLength > 5000) {
            return false;
        }

        $userId = (int) $GLOBALS['idUsuario'];
        $centreId = (int) $GLOBALS['centro_actual'];
        $threadId = (int) $threadId;
        if ($threadId > 0) {
            $roleFilter = staff_messages_is_educator()
                ? ' AND (tipo_nota=2 OR COALESCE(destino, 0)=0)'
                : '';
            $threads = staff_messages_query_all(
                $database,
                'SELECT id_nota_padre, estado FROM agenda_notas_padres '
                    . 'WHERE id_nota_padre=? AND id_alumno=? '
                    . 'AND (id_centro_alumno=? OR id_centro_alumno=0)'
                    . $roleFilter . ' LIMIT 1',
                'iii',
                array($threadId, (int) $studentId, $centreId)
            );
            if (count($threads) === 0 || (int) $threads[0]['estado'] === 2) {
                $threadId = 0;
            }
        }

        if ($threadId > 0) {
            $sent = staff_messages_execute(
                $database,
                'INSERT INTO agenda_notas_padres_respuestas '
                    . '(id_nota, respondido_por, fecha_respuesta, hora_respuesta, '
                    . 'texto_respuesta, leida, direccion, leido_por) '
                    . "VALUES (?, ?, ?, ?, ?, 0, 1, '')",
                'iisss',
                array($threadId, $userId, date('Y-m-d'), date('H:i:s'), $text)
            );
            return $sent ? $threadId : false;
        }

        if ($subject === '') {
            $subject = 'Conversación con la familia';
        }
        if (function_exists('mb_substr')) {
            $subject = mb_substr($subject, 0, 255, 'UTF-8');
        } else {
            $subject = substr($subject, 0, 255);
        }
        $parents = staff_messages_query_all(
            $database,
            'SELECT id_usuario FROM padres WHERE padre_de=? ORDER BY id_padre LIMIT 1',
            'i',
            array((int) $studentId)
        );
        $parentId = count($parents) > 0 ? (int) $parents[0]['id_usuario'] : 0;
        $sent = staff_messages_execute(
            $database,
            'INSERT INTO agenda_notas_padres '
                . '(id_padre, id_alumno, id_remitente, tipo_nota, asunto, texto_nota, '
                . 'fecha, hora, estado, destino, leido_por, id_centro_alumno) '
                . "VALUES (?, ?, ?, 2, ?, ?, ?, ?, 0, 0, '', ?)",
            'iiissssi',
            array(
                $parentId,
                (int) $studentId,
                $userId,
                $subject,
                $text,
                date('Y-m-d'),
                date('H:i:s'),
                $centreId,
            )
        );
        return $sent ? (int) mysqli_insert_id($database) : false;
    }
}

if (!function_exists('staff_messages_notifications')) {
    function staff_messages_notifications($database)
    {
        $students = staff_messages_students($database, 0);
        $byStudent = array();
        $studentIds = array();
        foreach ($students as $student) {
            $studentId = (int) $student['student_id'];
            $studentIds[] = $studentId;
            $student['unread_count'] = 0;
            $student['last_at'] = '';
            $student['last_preview'] = '';
            $byStudent[$studentId] = $student;
        }

        foreach (staff_messages_unread_rows($database, $studentIds) as $event) {
            $studentId = (int) $event['id_alumno'];
            if (!isset($byStudent[$studentId])) {
                continue;
            }
            $byStudent[$studentId]['unread_count']++;
            if ($byStudent[$studentId]['last_at'] === '') {
                $preview = staff_messages_clean_preview($event['body']);
                $byStudent[$studentId]['last_preview'] = $preview !== ''
                    ? $preview
                    : staff_messages_clean_preview($event['subject']);
                $byStudent[$studentId]['last_at'] = (string) $event['activity_at'];
            }
        }

        $conversations = array_values(array_filter($byStudent, function ($conversation) {
            return (int) $conversation['unread_count'] > 0;
        }));
        usort($conversations, function ($left, $right) {
            return strcmp($right['last_at'], $left['last_at']);
        });
        $notifications = array();
        $total = 0;
        foreach ($conversations as $conversation) {
            $unread = (int) $conversation['unread_count'];
            $total += $unread;
            if ($unread <= 0 || count($notifications) >= 6) {
                continue;
            }
            $notifications[] = array(
                'id' => $conversation['student_id'] . ':' . $conversation['last_at'] . ':' . $unread,
                'student_id' => (int) $conversation['student_id'],
                'student_name' => (string) $conversation['student_name'],
                'family_name' => (string) $conversation['family_name'],
                'classroom_id' => (int) $conversation['classroom_id'],
                'classroom_name' => (string) $conversation['classroom_name'],
                'preview' => (string) $conversation['last_preview'],
                'last_at' => (string) $conversation['last_at'],
                'unread_count' => $unread,
                'url' => '/gestion/mensajes/mensajeria.php?id_aula='
                    . (int) $conversation['classroom_id'] . '&id_alumno='
                    . (int) $conversation['student_id'],
            );
        }
        return array('total' => $total, 'items' => $notifications);
    }
}
