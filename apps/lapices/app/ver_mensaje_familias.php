<?php
require dirname(__DIR__) . '/configuracion/config.php';

if (empty($_SESSION['permitido_padre'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/parte_superior_padres.php';

function familias_chat_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function familias_chat_message_text($value)
{
    $value = preg_replace('#<br\s*/?>#i', "\n", (string) $value);
    $value = preg_replace('#</p\s*>#i', "\n", $value);
    $value = trim(strip_tags($value));

    return nl2br(familias_chat_escape($value));
}

$familias_id_mensaje = app_request_int($_GET, 'id_mensaje', 0);
$familias_error_respuesta =
    isset($_GET['respuesta']) && $_GET['respuesta'] === 'error';
$familias_alumnos = array();
$familias_padres = db_query(
    'SELECT padre_de FROM padres WHERE id_usuario=' . (int) $id_usuario
);

while ($familias_padre = db_fetch_array($familias_padres)) {
    $familias_alumnos[] = (int) $familias_padre['padre_de'];
}

$familias_mensaje = false;
if ($familias_id_mensaje > 0 && count($familias_alumnos) > 0) {
    $familias_busca_mensaje = db_query(
        'SELECT * FROM agenda_notas_padres WHERE id_nota_padre=' .
        $familias_id_mensaje . ' AND id_alumno IN (' .
        implode(',', $familias_alumnos) . ') LIMIT 1'
    );

    if ($familias_busca_mensaje && db_num_rows($familias_busca_mensaje) > 0) {
        $familias_mensaje = db_fetch_array($familias_busca_mensaje);
    }
}

$familias_respuestas = false;
$familias_numero_respuestas = 0;
$familias_puede_responder = false;
$familias_nombre_interlocutor = 'Centro educativo';
$familias_estado = '';
$familias_mensaje_propio = false;

if ($familias_mensaje) {
    $familias_tipo = (int) $familias_mensaje['tipo_nota'];
    $familias_mensaje_propio = $familias_tipo !== 2;

    if (!$familias_mensaje_propio) {
        $familias_busca_autor = db_query(
            'SELECT Nombre FROM usuarios WHERE id_usuario=' .
            (int) $familias_mensaje['id_remitente'] . ' LIMIT 1'
        );
        if ($familias_busca_autor && db_num_rows($familias_busca_autor) > 0) {
            $familias_autor = db_fetch_array($familias_busca_autor);
            $familias_nombre_interlocutor = $familias_autor['Nombre'];
        }

        if ((int) $familias_mensaje['estado'] === 0) {
            db_query(
                'UPDATE agenda_notas_padres SET estado=1 WHERE id_nota_padre=' .
                $familias_id_mensaje
            );
            $familias_mensaje['estado'] = 1;
        }
    } else {
        $familias_nombre_interlocutor =
            (int) $familias_mensaje['destino'] === 0
                ? 'Educadora'
                : 'Coordinacion';
    }

    $familias_estados = array('No le&iacute;do', 'Le&iacute;do', 'Cerrado');
    $familias_estado_indice = (int) $familias_mensaje['estado'];
    $familias_estado = isset($familias_estados[$familias_estado_indice])
        ? $familias_estados[$familias_estado_indice]
        : '';

    $familias_respuestas = db_query(
        'SELECT * FROM agenda_notas_padres_respuestas WHERE id_nota=' .
        $familias_id_mensaje .
        ' ORDER BY fecha_respuesta ASC, hora_respuesta ASC'
    );
    $familias_numero_respuestas = $familias_respuestas
        ? db_num_rows($familias_respuestas)
        : 0;
    $familias_puede_responder = true;
}
?>

<div class="content-wrapper familias-chat-page">
    <?php if ($familias_mensaje) { ?>
        <section class="content-header">
            <a class="familias-chat-back" href="/app/mensajeria.php">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Volver a mensajes
            </a>
            <h1>Conversaci&oacute;n</h1>
        </section>

        <section class="content">
            <article class="familias-chat-card">
                <header class="familias-chat-card__header">
                    <span class="familias-chat-avatar" aria-hidden="true">
                        <?php echo familias_chat_escape(
                            strtoupper(substr(strip_tags($familias_nombre_interlocutor), 0, 1))
                        ); ?>
                    </span>
                    <span class="familias-chat-card__identity">
                        <strong><?php echo familias_chat_escape($familias_nombre_interlocutor); ?></strong>
                        <small><?php echo familias_chat_escape($familias_mensaje['asunto']); ?></small>
                    </span>
                    <span class="familias-chat-status"><?php echo $familias_estado; ?></span>
                </header>

                <div class="familias-chat-thread" aria-label="Mensajes de la conversaci&oacute;n">
                    <span class="familias-chat-day">
                        <?php echo date(
                            'd/m/Y',
                            strtotime($familias_mensaje['fecha'])
                        ); ?>
                    </span>

                    <article class="familias-chat-bubble <?php
                        echo $familias_mensaje_propio
                            ? 'familias-chat-bubble--outgoing'
                            : 'familias-chat-bubble--incoming';
                    ?>">
                        <strong class="familias-chat-bubble__author">
                            <?php echo $familias_mensaje_propio
                                ? 'T&uacute;'
                                : familias_chat_escape($familias_nombre_interlocutor); ?>
                        </strong>
                        <div class="familias-chat-bubble__text">
                            <?php echo familias_chat_message_text(
                                $familias_mensaje['texto_nota']
                            ); ?>
                        </div>

                        <?php if ((int) $familias_mensaje['tipo_nota'] === 1) { ?>
                            <dl class="familias-chat-medication">
                                <div><dt>Medicamento</dt><dd><?php echo familias_chat_escape($familias_mensaje['medicamento']); ?></dd></div>
                                <div><dt>Cantidad</dt><dd><?php echo familias_chat_escape($familias_mensaje['cantidad']); ?></dd></div>
                                <div><dt>Hora</dt><dd><?php echo date('H:i', strtotime($familias_mensaje['hora_medicacion'])); ?></dd></div>
                                <div><dt>Desde</dt><dd><?php echo date('d/m/Y', strtotime($familias_mensaje['fecha_medicacion'])); ?></dd></div>
                                <div><dt>Hasta</dt><dd><?php echo date('d/m/Y', strtotime($familias_mensaje['fecha_fin_medicacion'])); ?></dd></div>
                            </dl>
                        <?php } ?>

                        <span class="familias-chat-bubble__meta">
                            <?php echo date(
                                'H:i',
                                strtotime($familias_mensaje['hora'])
                            ); ?>
                            <?php if ($familias_mensaje_propio) { ?>
                                <i class="fa fa-check" aria-hidden="true"></i>
                            <?php } ?>
                        </span>
                    </article>

                    <?php while (
                        $familias_respuestas &&
                        ($familias_respuesta = db_fetch_array($familias_respuestas))
                    ) {
                        $familias_respuesta_propia =
                            (int) $familias_respuesta['respondido_por'] ===
                            (int) $id_usuario;
                        $familias_nombre_autor = 'Centro educativo';

                        if (!$familias_respuesta_propia) {
                            $familias_busca_autor_respuesta = db_query(
                                'SELECT Nombre FROM usuarios WHERE id_usuario=' .
                                (int) $familias_respuesta['respondido_por'] .
                                ' LIMIT 1'
                            );
                            if (
                                $familias_busca_autor_respuesta &&
                                db_num_rows($familias_busca_autor_respuesta) > 0
                            ) {
                                $familias_autor_respuesta = db_fetch_array(
                                    $familias_busca_autor_respuesta
                                );
                                $familias_nombre_autor =
                                    $familias_autor_respuesta['Nombre'];
                            }

                            db_query(
                                'UPDATE agenda_notas_padres_respuestas SET leida=1 ' .
                                'WHERE id_respuesta=' .
                                (int) $familias_respuesta['id_respuesta']
                            );
                        }
                    ?>
                        <article class="familias-chat-bubble <?php
                            echo $familias_respuesta_propia
                                ? 'familias-chat-bubble--outgoing'
                                : 'familias-chat-bubble--incoming';
                        ?>">
                            <strong class="familias-chat-bubble__author">
                                <?php echo $familias_respuesta_propia
                                    ? 'T&uacute;'
                                    : familias_chat_escape($familias_nombre_autor); ?>
                            </strong>
                            <div class="familias-chat-bubble__text">
                                <?php echo familias_chat_message_text(
                                    $familias_respuesta['texto_respuesta']
                                ); ?>
                            </div>
                            <span class="familias-chat-bubble__meta">
                                <?php echo date(
                                    'd/m/Y H:i',
                                    strtotime(
                                        $familias_respuesta['fecha_respuesta'] .
                                        ' ' .
                                        $familias_respuesta['hora_respuesta']
                                    )
                                ); ?>
                                <?php if ($familias_respuesta_propia) { ?>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                <?php } ?>
                            </span>
                        </article>
                    <?php } ?>
                </div>

                <?php if ($familias_puede_responder) { ?>
                    <form
                        id="responder"
                        class="familias-chat-composer"
                        action="/app/responder_mensaje1.php"
                        method="post"
                    >
                        <?php if ($familias_error_respuesta) { ?>
                            <span class="familias-chat-composer__error">
                                No se ha podido enviar. Revisa el mensaje e int&eacute;ntalo de nuevo.
                            </span>
                        <?php } ?>
                        <input type="hidden" name="_csrf" value="<?php echo familias_chat_escape(app_csrf_token()); ?>">
                        <input type="hidden" name="id_mensaje" value="<?php echo $familias_id_mensaje; ?>">
                        <input type="hidden" name="id_remitente" value="<?php echo (int) $id_usuario; ?>">
                        <label class="sr-only" for="familias-chat-response">Escribe tu respuesta</label>
                        <textarea
                            id="familias-chat-response"
                            name="respuesta"
                            rows="1"
                            maxlength="5000"
                            placeholder="Escribe un mensaje..."
                            required
                        ></textarea>
                        <button type="submit" aria-label="Enviar respuesta">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </form>
                <?php } ?>

                <footer class="familias-chat-card__footer">
                    <a class="btn btn-default" href="/app/mensajeria.php">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        Volver
                    </a>
                    <?php if (
                        $familias_mensaje_propio &&
                        (int) $familias_mensaje['estado'] === 0
                    ) { ?>
                        <a
                            class="btn btn-default familias-chat-delete"
                            href="/app/eliminar_mensaje.php?id_mensaje=<?php echo $familias_id_mensaje; ?>"
                            onclick="return window.confirm('Esta operaci\u00f3n no se puede deshacer. \u00bfEliminar el mensaje?');"
                        >
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                            Eliminar
                        </a>
                    <?php } ?>
                </footer>
            </article>
        </section>
    <?php } else { ?>
        <section class="content-header"><h1>Mensaje no encontrado</h1></section>
        <section class="content">
            <div class="familias-chat-empty">
                <i class="fa fa-comment-o" aria-hidden="true"></i>
                <p>No hemos podido abrir esta conversaci&oacute;n.</p>
                <a class="btn btn-primary" href="/app/mensajeria.php">Volver a mensajes</a>
            </div>
        </section>
    <?php } ?>
</div>

<?php require __DIR__ . '/parte_inferior_padres.php'; ?>
