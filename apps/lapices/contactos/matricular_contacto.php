<?php
require_once dirname(__DIR__) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/gestion/panel/bootstrap.php';

if ($permitido !== true) {
    header('Location: /index.php');
    exit;
}
if (!$panel_has_selected_centre) {
    header('Location: /index.php');
    exit;
}

$database = isset($panel_database) && $panel_database instanceof mysqli
    ? $panel_database
    : false;
$contact_id = app_request_int(
    app_request_value($_SERVER, 'REQUEST_METHOD', '') === 'POST' ? $_POST : $_GET,
    'id_contacto',
    0
);
if (!($database instanceof mysqli) || $contact_id <= 0) {
    http_response_code(404);
    exit('Contacto no encontrado.');
}

$contact_statement = mysqli_prepare(
    $database,
    'SELECT * FROM contactos WHERE id_contacto=? LIMIT 1'
);
mysqli_stmt_bind_param($contact_statement, 'i', $contact_id);
mysqli_stmt_execute($contact_statement);
$contact_result = mysqli_stmt_get_result($contact_statement);
$contact = $contact_result ? mysqli_fetch_assoc($contact_result) : false;
if ($contact_result) {
    mysqli_free_result($contact_result);
}
mysqli_stmt_close($contact_statement);

if (!$contact) {
    http_response_code(404);
    exit('Contacto no encontrado.');
}

$can_use_other_centre = (int) $nivel_acceso >= 4
    || !empty($usuario_multicentro);
if (
    !$can_use_other_centre
    && (int) $contact['id_centro'] !== (int) $centro_actual
) {
    http_response_code(403);
    exit('No tienes acceso a este contacto.');
}

$errors = array();
$contact_name = (string) $contact['Nombre'];
$contact_surname1 = (string) $contact['Apellido1'];
$contact_surname2 = (string) $contact['Apellido2'];
if (app_request_value($_SERVER, 'REQUEST_METHOD', '') === 'POST') {
    if (!app_verify_csrf(app_request_value($_POST, '_csrf', ''))) {
        $errors[] = 'La sesión del formulario ha caducado.';
    }

    $selected_user = app_request_int($_POST, 'id_usuario_existente', 0);
    $centre_id = (int) $contact['id_centro'];
    $course_id = (int) $contact['id_curso'];
    if ($centre_id <= 0 || $course_id <= 0) {
        $errors[] = 'Completa primero el centro y el curso del contacto.';
    }

    if (!$errors && $selected_user > 0) {
        $candidate_statement = mysqli_prepare(
            $database,
            'SELECT id_usuario FROM usuarios WHERE id_usuario=? '
            . 'AND Nombre=? AND Apellido1=? AND Apellido2=? LIMIT 1'
        );
        mysqli_stmt_bind_param(
            $candidate_statement,
            'isss',
            $selected_user,
            $contact_name,
            $contact_surname1,
            $contact_surname2
        );
        mysqli_stmt_execute($candidate_statement);
        mysqli_stmt_store_result($candidate_statement);
        if (mysqli_stmt_num_rows($candidate_statement) === 0) {
            $errors[] = 'El alumno existente seleccionado no coincide con el contacto.';
        }
        mysqli_stmt_close($candidate_statement);
    }

    if (!$errors) {
        $user_id = $selected_user;
        if ($user_id <= 0) {
            $today = date('Y-m-d');
            $user_statement = mysqli_prepare(
                $database,
                'INSERT INTO usuarios (tipo_inscripcion, fechaalta, nivel_acceso) '
                . 'VALUES (1,?,0)'
            );
            mysqli_stmt_bind_param($user_statement, 's', $today);
            if (!mysqli_stmt_execute($user_statement)) {
                $errors[] = 'No se ha podido crear la ficha del alumno.';
            } else {
                $user_id = (int) mysqli_insert_id($database);
            }
            mysqli_stmt_close($user_statement);
        }

        if (!$errors) {
            $contact_sip = (string) $contact['SIP'];
            $contact_sex = (string) $contact['sexo'];
            $contact_birth_date = (string) $contact['fecha_nacimiento'];
            $contact_coordinates = (string) $contact['coordenadas'];
            $contact_address = (string) $contact['calle'];
            $contact_postcode = (string) $contact['codigo_postal'];
            $contact_town = (string) $contact['poblacion'];
            $contact_phone = (string) $contact['telefono'];
            $contact_email = (string) $contact['email'];
            $user_statement = mysqli_prepare(
                $database,
                'UPDATE usuarios SET SIP=?, Nombre=?, Apellido1=?, Apellido2=?, '
                . 'sexo=?, fecha_nacimiento=?, coordenadas=?, Direccion=?, cp=?, '
                . 'Poblacion=?, telefono1=?, email=? WHERE id_usuario=?'
            );
            mysqli_stmt_bind_param(
                $user_statement,
                'ssssssssssssi',
                $contact_sip,
                $contact_name,
                $contact_surname1,
                $contact_surname2,
                $contact_sex,
                $contact_birth_date,
                $contact_coordinates,
                $contact_address,
                $contact_postcode,
                $contact_town,
                $contact_phone,
                $contact_email,
                $user_id
            );
            if (!mysqli_stmt_execute($user_statement)) {
                $errors[] = 'No se han podido copiar los datos a la ficha del alumno.';
            }
            mysqli_stmt_close($user_statement);
        }

        if (!$errors) {
            $decision_date = date('Y-m-d');
            $contact_statement = mysqli_prepare(
                $database,
                "UPDATE contactos SET id_usuario=?, fecha_c=?, "
                . "proceso_decision='c' WHERE id_contacto=?"
            );
            mysqli_stmt_bind_param(
                $contact_statement,
                'isi',
                $user_id,
                $decision_date,
                $contact_id
            );
            mysqli_stmt_execute($contact_statement);
            mysqli_stmt_close($contact_statement);

            $existing_statement = mysqli_prepare(
                $database,
                'SELECT id_alumno FROM alumnos '
                . 'WHERE id_usuario=? AND id_centro=? AND id_curso=? LIMIT 1'
            );
            mysqli_stmt_bind_param(
                $existing_statement,
                'iii',
                $user_id,
                $centre_id,
                $course_id
            );
            mysqli_stmt_execute($existing_statement);
            mysqli_stmt_bind_result($existing_statement, $existing_student_id);
            $already_enrolled = mysqli_stmt_fetch($existing_statement);
            mysqli_stmt_close($existing_statement);

            if ($already_enrolled) {
                $_SESSION['contactos_flash'] = array(
                    'type' => 'success',
                    'message' => 'El contacto ya estaba matriculado en este curso.',
                );
                header(
                    'Location: /contactos/nuevo_contacto.php?id_contacto='
                    . $contact_id
                );
                exit;
            }

            header(
                'Location: /alumnos/nuevo_alumno4.php?u=' . $user_id
                . '&id_centro_pre=' . $centre_id
                . '&id_curso_pre=' . $course_id
            );
            exit;
        }
    }
}

$candidates = array();
$candidate_statement = mysqli_prepare(
    $database,
    'SELECT id_usuario, Nombre, Apellido1, Apellido2, fecha_nacimiento, '
    . 'Direccion, telefono1 FROM usuarios '
    . 'WHERE Nombre=? AND Apellido1=? AND Apellido2=? '
    . 'ORDER BY id_usuario DESC LIMIT 12'
);
mysqli_stmt_bind_param(
    $candidate_statement,
    'sss',
    $contact_name,
    $contact_surname1,
    $contact_surname2
);
mysqli_stmt_execute($candidate_statement);
$candidate_result = mysqli_stmt_get_result($candidate_statement);
while ($candidate_result && $candidate = mysqli_fetch_assoc($candidate_result)) {
    $candidates[] = $candidate;
}
if ($candidate_result) {
    mysqli_free_result($candidate_result);
}
mysqli_stmt_close($candidate_statement);

$course_name = 'Sin curso';
if ((int) $contact['id_curso'] > 0) {
    $course_query = mysqli_query(
        $database,
        'SELECT nombre FROM cursos_academicos WHERE id_curso='
        . (int) $contact['id_curso'] . ' LIMIT 1'
    );
    if ($course_query && $course_row = mysqli_fetch_assoc($course_query)) {
        $course_name = $course_row['nombre'];
    }
    if ($course_query) {
        mysqli_free_result($course_query);
    }
}

$centre_name = 'Sin centro';
if ((int) $contact['id_centro'] > 0) {
    $centre_query = mysqli_query(
        $database,
        'SELECT nombre_centro FROM centros WHERE id_centro='
        . (int) $contact['id_centro'] . ' LIMIT 1'
    );
    if ($centre_query && $centre_row = mysqli_fetch_assoc($centre_query)) {
        $centre_name = $centre_row['nombre_centro'];
    }
    if ($centre_query) {
        mysqli_free_result($centre_query);
    }
}

$panel_active_section = 'alumnos';
$panel_topbar_label = 'Contactos interesados';
$panel_topbar_title = 'Preparar matrícula';
$panel_extra_styles = array('/contactos/assets/workflow.css?v=20260730.1');
require dirname(__DIR__) . '/gestion/panel/views/header.php';
?>
<section class="contact-workflow">
    <header class="contact-workflow__hero">
        <div>
            <span class="eyebrow">Contactos interesados</span>
            <h1>Inscribir alumno</h1>
            <p>
                Reutilizaremos los datos del seguimiento y abriremos la matrícula
                con el centro y el curso ya seleccionados.
            </p>
        </div>
        <a class="button button--outline" href="/contactos/nuevo_contacto.php?id_contacto=<?php echo $contact_id; ?>">
            Editar datos
        </a>
    </header>

    <?php foreach ($errors as $error) { ?>
        <div class="alert alert--error"><?php echo app_escape($error); ?></div>
    <?php } ?>

    <form class="card contact-workflow__card" method="post">
        <input type="hidden" name="_csrf" value="<?php echo app_escape(app_csrf_token()); ?>">
        <input type="hidden" name="id_contacto" value="<?php echo $contact_id; ?>">

        <div class="contact-workflow__summary">
            <div>
                <span>Alumno</span>
                <strong><?php echo app_escape(trim(
                    $contact['Nombre'] . ' ' . $contact['Apellido1'] . ' '
                    . $contact['Apellido2']
                )); ?></strong>
            </div>
            <div>
                <span>Centro</span>
                <strong><?php echo app_escape($centre_name); ?></strong>
            </div>
            <div>
                <span>Curso</span>
                <strong><?php echo app_escape($course_name); ?></strong>
            </div>
        </div>

        <?php if ((int) $contact['id_centro'] <= 0 || (int) $contact['id_curso'] <= 0) { ?>
            <div class="alert alert--error">
                El contacto necesita tener centro y curso antes de iniciar la matrícula.
            </div>
        <?php } else { ?>
            <h2>¿Es un alumno nuevo?</h2>
            <p class="muted">
                Si existe una ficha con el mismo nombre puedes reutilizarla para
                evitar duplicados.
            </p>

            <div class="contact-workflow__choices">
                <label class="contact-workflow__choice">
                    <input type="radio" name="id_usuario_existente" value="0" checked>
                    <span>
                        <strong>Crear una ficha nueva</strong>
                        <small>Usará automáticamente los datos de este contacto.</small>
                    </span>
                </label>
                <?php foreach ($candidates as $candidate) { ?>
                    <label class="contact-workflow__choice">
                        <input
                            type="radio"
                            name="id_usuario_existente"
                            value="<?php echo (int) $candidate['id_usuario']; ?>"
                            <?php echo (int) $contact['id_usuario'] === (int) $candidate['id_usuario'] ? ' checked' : ''; ?>
                        >
                        <span>
                            <strong>
                                Ficha #<?php echo (int) $candidate['id_usuario']; ?>
                                · <?php echo app_escape(trim(
                                    $candidate['Nombre'] . ' '
                                    . $candidate['Apellido1'] . ' '
                                    . $candidate['Apellido2']
                                )); ?>
                            </strong>
                            <small>
                                <?php echo app_escape($candidate['fecha_nacimiento']); ?>
                                <?php echo $candidate['Direccion'] !== '' ? ' · ' . app_escape($candidate['Direccion']) : ''; ?>
                                <?php echo $candidate['telefono1'] !== '' ? ' · ' . app_escape($candidate['telefono1']) : ''; ?>
                            </small>
                        </span>
                    </label>
                <?php } ?>
            </div>

            <div class="contact-workflow__actions">
                <a class="button button--outline" href="/contactos/listado_contactos.php">
                    Cancelar
                </a>
                <button class="button" type="submit">
                    Continuar con la matrícula
                </button>
            </div>
        <?php } ?>
    </form>
</section>
<?php require dirname(__DIR__) . '/gestion/panel/views/footer.php'; ?>
