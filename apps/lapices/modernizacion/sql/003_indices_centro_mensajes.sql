-- Indices para que el centro de mensajes ordene y abra conversaciones con rapidez.
-- Ejecutar una sola vez durante el despliegue de esta version.

ALTER TABLE agenda_notas_padres
    ADD INDEX idx_mensajes_alumno_centro_fecha
    (id_alumno, id_centro_alumno, fecha, hora);

ALTER TABLE agenda_notas_padres_respuestas
    ADD INDEX idx_mensajes_respuestas_nota_fecha
    (id_nota, fecha_respuesta, hora_respuesta);
