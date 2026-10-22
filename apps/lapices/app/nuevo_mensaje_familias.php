<?php
require dirname(__DIR__) . '/configuracion/config.php';

if (empty($_SESSION['permitido_padre'])) {
    header('Location: /app/login.php');
    exit;
}

require __DIR__ . '/parte_superior_padres.php';

function familias_compose_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$familias_destinatarios_educadoras = array();
$familias_destinatarios_centros = array();
$familias_centros_agregados = array();
$familias_permite_coordinacion =
    isset($mostrar_mensajes_administracion) &&
    $mostrar_mensajes_administracion == true;

$familias_busca_alumnos = db_query(
    'SELECT alumnos.id_usuario AS id_alumno_usuario, alumnos.id_centro, ' .
    'usuarios.Nombre AS nombre_hijo, usuarios.Apellido1, usuarios.Apellido2, ' .
    'centros.nombre_centro ' .
    'FROM alumnos ' .
    'INNER JOIN usuarios ON usuarios.id_usuario=alumnos.id_usuario ' .
    'INNER JOIN padres ON padres.padre_de=alumnos.id_usuario ' .
    'LEFT JOIN centros ON centros.id_centro=alumnos.id_centro ' .
    'WHERE padres.id_usuario=' . (int) $id_usuario . ' ' .
    "AND alumnos.fecha_matricula<='" . date('Y-m-d') . "' " .
    "AND alumnos.fecha_baja>='" . date('Y-m-d') . "' " .
    'AND alumnos.id_curso=' . (int) $id_curso_actual . ' ' .
    'ORDER BY usuarios.Nombre, usuarios.Apellido1, usuarios.Apellido2'
);

while (
    $familias_busca_alumnos &&
    ($familias_alumno = db_fetch_array($familias_busca_alumnos))
) {
    $familias_nombre_hijo = trim(
        $familias_alumno['nombre_hijo'] . ' ' .
        $familias_alumno['Apellido1'] . ' ' .
        $familias_alumno['Apellido2']
    );
    $familias_id_alumno = (int) $familias_alumno['id_alumno_usuario'];
    $familias_id_centro = (int) $familias_alumno['id_centro'];
    $familias_nombre_centro = trim((string) $familias_alumno['nombre_centro']);

    $familias_destinatarios_educadoras[] = array(
        'valor' => 'A' . $familias_id_alumno,
        'inicial' => strtoupper(substr($familias_nombre_hijo, 0, 1)),
        'titulo' => 'Educadora del aula',
        'detalle' => 'Sobre ' . $familias_nombre_hijo,
        'centro' => $familias_nombre_centro,
    );

    if (
        $familias_permite_coordinacion &&
        !isset($familias_centros_agregados[$familias_id_centro])
    ) {
        $familias_destinatarios_centros[] = array(
            'valor' => 'C' . $familias_id_alumno,
            'inicial' => 'C',
            'titulo' => 'Coordinacion del centro',
            'detalle' => $familias_nombre_centro,
            'centro' => 'Consultas generales y administrativas',
        );
        $familias_centros_agregados[$familias_id_centro] = true;
    }
}

$familias_numero_destinatarios =
    count($familias_destinatarios_educadoras) +
    count($familias_destinatarios_centros);
$familias_error = isset($_GET['estado']) && $_GET['estado'] === 'error';
?>

<div class="content-wrapper familias-compose-page">
    <section class="content-header">
        <a class="familias-chat-back" href="/app/mensajeria.php">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
            Volver a mensajes
        </a>
        <h1>Nuevo mensaje</h1>
        <p>Habla con el equipo que atiende a tus hijos.</p>
    </section>

    <section class="content">
        <?php if ($familias_numero_destinatarios > 0) { ?>
            <form
                class="familias-compose-card"
                action="/app/nuevo_mensaje1.php"
                method="post"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?php echo familias_compose_escape(app_csrf_token()); ?>"
                >

                <?php if ($familias_error) { ?>
                    <div class="familias-compose-error" role="alert">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                        No se ha podido enviar el mensaje. Revisa todos los campos e int&eacute;ntalo de nuevo.
                    </div>
                <?php } ?>

                <fieldset class="familias-recipient-picker">
                    <legend>
                        <span>1</span>
                        &iquest;Con qui&eacute;n quieres hablar?
                    </legend>
                    <p class="familias-compose-help">
                        Solo aparecen los contactos disponibles para tus hijos y sus centros actuales.
                    </p>

                    <div class="familias-recipient-list">
                        <?php
                        $familias_indice_destinatario = 0;
                        foreach (
                            array_merge(
                                $familias_destinatarios_educadoras,
                                $familias_destinatarios_centros
                            ) as $familias_destinatario
                        ) {
                            $familias_indice_destinatario++;
                            $familias_destinatario_id =
                                'familias-destinatario-' .
                                $familias_indice_destinatario;
                        ?>
                            <label
                                class="familias-recipient-option"
                                for="<?php echo $familias_destinatario_id; ?>"
                            >
                                <input
                                    id="<?php echo $familias_destinatario_id; ?>"
                                    type="radio"
                                    name="destinatario"
                                    value="<?php echo familias_compose_escape($familias_destinatario['valor']); ?>"
                                    <?php echo $familias_indice_destinatario === 1 ? 'checked' : ''; ?>
                                    required
                                >
                                <span class="familias-recipient-option__avatar" aria-hidden="true">
                                    <?php echo familias_compose_escape($familias_destinatario['inicial']); ?>
                                </span>
                                <span class="familias-recipient-option__copy">
                                    <strong><?php echo familias_compose_escape($familias_destinatario['titulo']); ?></strong>
                                    <span><?php echo familias_compose_escape($familias_destinatario['detalle']); ?></span>
                                    <?php if ($familias_destinatario['centro'] !== '') { ?>
                                        <small><?php echo familias_compose_escape($familias_destinatario['centro']); ?></small>
                                    <?php } ?>
                                </span>
                                <span class="familias-recipient-option__check" aria-hidden="true">
                                    <i class="fa fa-check"></i>
                                </span>
                            </label>
                        <?php } ?>
                    </div>
                </fieldset>

                <div class="familias-compose-fields">
                    <h2>
                        <span>2</span>
                        Escribe tu mensaje
                    </h2>

                    <label for="familias-compose-subject">Asunto</label>
                    <input
                        id="familias-compose-subject"
                        class="form-control"
                        type="text"
                        name="asunto"
                        maxlength="255"
                        placeholder="Por ejemplo: Consulta sobre la agenda"
                        autocomplete="off"
                        required
                    >

                    <label for="familias-compose-message">Mensaje</label>
                    <textarea
                        id="familias-compose-message"
                        class="form-control"
                        name="texto_mensaje"
                        rows="7"
                        maxlength="5000"
                        placeholder="Escribe aqu&iacute; tu mensaje..."
                        required
                    ></textarea>
                    <small class="familias-compose-hint">
                        El mensaje quedar&aacute; guardado en tus conversaciones.
                    </small>
                </div>

                <footer class="familias-compose-actions">
                    <a class="btn btn-default" href="/app/mensajeria.php">
                        Cancelar
                    </a>
                    <button class="btn btn-primary" type="submit">
                        <i class="fa fa-paper-plane" aria-hidden="true"></i>
                        Enviar mensaje
                    </button>
                </footer>
            </form>
        <?php } else { ?>
            <div class="familias-chat-empty">
                <i class="fa fa-user-times" aria-hidden="true"></i>
                <h2>No hay destinatarios disponibles</h2>
                <p>
                    En este momento no encontramos hijos matriculados en el curso activo
                    con los que iniciar una conversaci&oacute;n.
                </p>
                <a class="btn btn-primary" href="/app/mensajeria.php">
                    Volver a mensajes
                </a>
            </div>
        <?php } ?>
    </section>
</div>

<?php require __DIR__ . '/parte_inferior_padres.php'; ?>
