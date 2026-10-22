<?php

function contactos_workflow_database()
{
    global $sql_host, $sql_usuario, $sql_pass, $sql_db;

    return app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
}

function contactos_workflow_source_table($table)
{
    $allowed = array('contactos_web', 'contactos_web_sp');
    return in_array($table, $allowed, true) ? $table : '';
}

function contactos_workflow_fetch_source($database, $table, $contact_id)
{
    $table = contactos_workflow_source_table($table);
    $contact_id = (int) $contact_id;
    if ($table === '' || $contact_id <= 0) {
        return false;
    }

    $statement = mysqli_prepare(
        $database,
        "SELECT * FROM `" . $table . "` WHERE id_contacto=? LIMIT 1"
    );
    if (!$statement) {
        return false;
    }

    mysqli_stmt_bind_param($statement, 'i', $contact_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);

    return $row;
}

function contactos_workflow_existing_link($database, $table, $contact_id)
{
    $table = contactos_workflow_source_table($table);
    $contact_id = (int) $contact_id;
    if ($table === '' || $contact_id <= 0) {
        return 0;
    }

    $statement = mysqli_prepare(
        $database,
        'SELECT id_contacto_interesado FROM contactos_web_interesados '
        . 'WHERE tabla_origen=? AND id_contacto_web=? LIMIT 1'
    );
    if (!$statement) {
        return 0;
    }

    mysqli_stmt_bind_param($statement, 'si', $table, $contact_id);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $interested_id);
    $found = mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);

    return $found ? (int) $interested_id : 0;
}

function contactos_workflow_clean_date($value)
{
    $value = trim((string) $value);
    if (
        $value === ''
        || $value === '0000-00-00'
        || strtotime($value) === false
    ) {
        return '0000-00-00';
    }

    return date('Y-m-d', strtotime($value));
}

function contactos_workflow_limit($value, $length)
{
    $value = trim((string) $value);
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $length, 'UTF-8')
        : substr($value, 0, $length);
}

function contactos_workflow_person_name($source, $suffix)
{
    $parts = array(
        isset($source['nombre_' . $suffix]) ? $source['nombre_' . $suffix] : '',
        isset($source['apellido1_' . $suffix]) ? $source['apellido1_' . $suffix] : '',
        isset($source['apellido2_' . $suffix]) ? $source['apellido2_' . $suffix] : '',
    );
    $parts = array_filter(array_map('trim', $parts), 'strlen');
    return implode(' ', $parts);
}

function contactos_workflow_sex($value)
{
    $value = function_exists('mb_strtolower')
        ? mb_strtolower(trim((string) $value), 'UTF-8')
        : strtolower(trim((string) $value));

    if (
        strpos($value, 'niña') === 0
        || strpos($value, 'nina') === 0
        || $value === 'h'
        || $value === 'm'
        || strpos($value, 'hemb') === 0
        || strpos($value, 'fem') === 0
    ) {
        return 'H';
    }
    if (
        $value === 'v'
        || strpos($value, 'var') === 0
        || strpos($value, 'masc') === 0
        || strpos($value, 'niño') === 0
        || strpos($value, 'nino') === 0
    ) {
        return 'V';
    }

    return strtoupper(substr($value, 0, 1));
}

function contactos_workflow_summary($source)
{
    $parts = array();
    $pairs = array(
        'Horario solicitado' => 'horario',
        'Mensualidad' => 'mensualidad',
        'Desayuno' => 'desayuno',
        'Almuerzo' => 'almuerzo',
        'Comida' => 'comida',
        'Merienda' => 'merienda',
        'Horas extra' => 'horas_extra',
        'N.º horas extra' => 'numero_horas_extra',
        'Cómo nos conoció' => 'como_conocio_texto',
        'Referido por centro' => 'referido_centro',
        'Referido por alumno' => 'referido_alumno',
    );
    foreach ($pairs as $label => $field) {
        $value = isset($source[$field]) ? trim((string) $source[$field]) : '';
        if ($value !== '') {
            $parts[] = $label . ': ' . $value;
        }
    }

    $second_contact = contactos_workflow_person_name($source, 'c2');
    if ($second_contact !== '') {
        $second_phone = isset($source['telefono_padre2'])
            ? trim((string) $source['telefono_padre2'])
            : '';
        $second_email = isset($source['email_padre2'])
            ? trim((string) $source['email_padre2'])
            : '';
        $parts[] = 'Segundo familiar: ' . $second_contact
            . ($second_phone !== '' ? ' · ' . $second_phone : '')
            . ($second_email !== '' ? ' · ' . $second_email : '');
    }

    return implode("\n", $parts);
}

function contactos_workflow_transfer($database, $table, $contact_id, $manager_id)
{
    $table = contactos_workflow_source_table($table);
    $contact_id = (int) $contact_id;
    $manager_id = (int) $manager_id;

    $existing = contactos_workflow_existing_link(
        $database,
        $table,
        $contact_id
    );
    if ($existing > 0) {
        return array('ok' => true, 'id_contacto' => $existing, 'existing' => true);
    }

    $source = contactos_workflow_fetch_source($database, $table, $contact_id);
    if (!$source) {
        return array('ok' => false, 'error' => 'No se encuentra la inscripción web.');
    }

    $primary_name = contactos_workflow_person_name($source, 'c1');
    $primary_phone = contactos_workflow_limit($source['telefono_padre1'], 100);
    if ($primary_phone === '') {
        $primary_phone = contactos_workflow_limit($source['telefono_t1'], 100);
    }
    $primary_email = contactos_workflow_limit($source['email_padre1'], 100);
    if ($primary_name === '') {
        $primary_name = trim(
            (string) $source['Nombre'] . ' '
            . (string) $source['Apellido1']
        );
    }

    $source_comments = trim((string) $source['comentarios']);
    $web_summary = contactos_workflow_summary($source);
    $comments = $source_comments;
    if ($web_summary !== '') {
        $comments .= ($comments !== '' ? "\n\n" : '')
            . "Datos importados de la inscripción web:\n"
            . $web_summary;
    }

    $id_centro = (int) $source['id_centro'];
    $nombre = contactos_workflow_limit($source['Nombre'], 255);
    $fecha_contacto = contactos_workflow_clean_date($source['fecha_contacto']);
    $proceso = 'b1';
    $persona = contactos_workflow_limit($primary_name, 255);
    $telefono = $primary_phone;
    $email = $primary_email;
    $apellido1 = contactos_workflow_limit($source['Apellido1'], 255);
    $apellido2 = contactos_workflow_limit($source['Apellido2'], 255);
    $calle = contactos_workflow_limit($source['calle'], 250);
    $codigo_postal = contactos_workflow_limit($source['codigo_postal'], 10);
    $poblacion = contactos_workflow_limit($source['poblacion'], 255);
    $coordenadas = contactos_workflow_limit($source['coordenadas'], 50);
    $fecha_nacimiento = contactos_workflow_clean_date($source['fecha_nacimiento']);
    $sexo = contactos_workflow_limit(
        contactos_workflow_sex($source['sexo']),
        50
    );
    $fecha_informacion = contactos_workflow_clean_date($source['fecha_informacion']);
    $id_curso = (int) $source['id_curso'];
    $nuevo_alumno = 1;
    $id_usuario = (int) $source['id_usuario_alumno'];
    $nonato = (int) $source['nonato'];
    $prematuro = (int) $source['prematuro'];
    $prematuro1 = (int) $source['prematuro1'];
    $como_conocio = (int) $source['como_conocio'];
    $no_publicidad = (int) $source['no_publicidad'];
    $origen = 1;
    $sip = contactos_workflow_limit($source['SIP'], 50);
    $observaciones = contactos_workflow_limit(
        $source['observaciones_informacion'],
        50
    );

    $statement = mysqli_prepare(
        $database,
        'INSERT INTO contactos ('
        . 'id_centro, Nombre, fecha_contacto, proceso_decision, persona_contacto, '
        . 'comentarios, telefono, email, Apellido1, Apellido2, calle, codigo_postal, '
        . 'poblacion, coordenadas, fecha_nacimiento, sexo, fecha_informacion, '
        . 'id_curso, nuevo_alumno, id_usuario, nonato, prematuro, prematuro1, '
        . 'como_conocio, no_publicidad, origen, SIP, observaciones_informacion'
        . ') VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    if (!$statement) {
        return array('ok' => false, 'error' => 'No se puede preparar el contacto interesado.');
    }

    mysqli_stmt_bind_param(
        $statement,
        'issssssssssssssssiiiiiiiiiss',
        $id_centro,
        $nombre,
        $fecha_contacto,
        $proceso,
        $persona,
        $comments,
        $telefono,
        $email,
        $apellido1,
        $apellido2,
        $calle,
        $codigo_postal,
        $poblacion,
        $coordenadas,
        $fecha_nacimiento,
        $sexo,
        $fecha_informacion,
        $id_curso,
        $nuevo_alumno,
        $id_usuario,
        $nonato,
        $prematuro,
        $prematuro1,
        $como_conocio,
        $no_publicidad,
        $origen,
        $sip,
        $observaciones
    );
    $inserted = mysqli_stmt_execute($statement);
    $interested_id = $inserted ? (int) mysqli_insert_id($database) : 0;
    $insert_error = mysqli_stmt_error($statement);
    mysqli_stmt_close($statement);
    if (!$inserted || $interested_id <= 0) {
        return array(
            'ok' => false,
            'error' => 'No se ha podido crear el interesado. ' . $insert_error,
        );
    }

    $snapshot = json_encode(
        $source,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            | JSON_PARTIAL_OUTPUT_ON_ERROR
    );
    $linked_at = date('Y-m-d H:i:s');
    $link_statement = mysqli_prepare(
        $database,
        'INSERT INTO contactos_web_interesados '
        . '(tabla_origen, id_contacto_web, id_contacto_interesado, '
        . 'datos_origen, creado_por, fecha_creacion) VALUES (?,?,?,?,?,?)'
    );
    if (!$link_statement) {
        mysqli_query($database, 'DELETE FROM contactos WHERE id_contacto=' . $interested_id);
        return array('ok' => false, 'error' => 'No se puede relacionar la inscripción con el interesado.');
    }
    mysqli_stmt_bind_param(
        $link_statement,
        'siisis',
        $table,
        $contact_id,
        $interested_id,
        $snapshot,
        $manager_id,
        $linked_at
    );
    $linked = mysqli_stmt_execute($link_statement);
    mysqli_stmt_close($link_statement);
    if (!$linked) {
        $existing = contactos_workflow_existing_link(
            $database,
            $table,
            $contact_id
        );
        if ($existing > 0) {
            mysqli_query($database, 'DELETE FROM contactos WHERE id_contacto=' . $interested_id);
            return array('ok' => true, 'id_contacto' => $existing, 'existing' => true);
        }
        mysqli_query($database, 'DELETE FROM contactos WHERE id_contacto=' . $interested_id);
        return array('ok' => false, 'error' => 'No se ha podido guardar la relación con la inscripción web.');
    }

    $note = 'Contacto creado automáticamente desde Inscripciones web.';
    $note_date = date('Y-m-d');
    $note_statement = mysqli_prepare(
        $database,
        'INSERT INTO contactos_seguimiento '
        . '(fecha, comentarios, id_contacto, id_usuario, fecha_modificacion) '
        . 'VALUES (?,?,?,?,?)'
    );
    if ($note_statement) {
        mysqli_stmt_bind_param(
            $note_statement,
            'ssiis',
            $note_date,
            $note,
            $interested_id,
            $manager_id,
            $note_date
        );
        mysqli_stmt_execute($note_statement);
        mysqli_stmt_close($note_statement);
    }

    return array('ok' => true, 'id_contacto' => $interested_id, 'existing' => false);
}

function contactos_workflow_imported_data($database, $interested_id)
{
    $interested_id = (int) $interested_id;
    if ($interested_id <= 0) {
        return false;
    }

    $statement = mysqli_prepare(
        $database,
        'SELECT tabla_origen, id_contacto_web, datos_origen, fecha_creacion '
        . 'FROM contactos_web_interesados '
        . 'WHERE id_contacto_interesado=? LIMIT 1'
    );
    if (!$statement) {
        return false;
    }
    mysqli_stmt_bind_param($statement, 'i', $interested_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);

    if (!$row) {
        return false;
    }
    $row['data'] = json_decode($row['datos_origen'], true);
    return $row;
}
