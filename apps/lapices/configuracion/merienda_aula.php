<?php

if (!function_exists('app_fecha_iso_valida')) {
    function app_fecha_iso_valida($fecha)
    {
        if (!is_string($fecha) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $partes)) {
            return false;
        }

        return checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1]);
    }
}

if (!function_exists('app_aula_sin_merienda')) {
    function app_aula_sin_merienda($idCentro, $idCurso, $idAula, $fecha, $link = null)
    {
        static $cache = array();

        $idCentro = (int) $idCentro;
        $idCurso = (int) $idCurso;
        $idAula = (int) $idAula;
        if ($idCentro <= 0 || $idCurso <= 0 || $idAula <= 0 || !app_fecha_iso_valida($fecha)) {
            return false;
        }

        $clave = $idCentro . ':' . $idCurso . ':' . $idAula . ':' . $fecha;
        if (array_key_exists($clave, $cache)) {
            return $cache[$clave];
        }

        $resultado = db_query_params(
            "SELECT id_exclusion
             FROM aulas_exclusiones_merienda
             WHERE id_centro=? AND id_curso=? AND id_aula=? AND fecha=? AND activa=1
             LIMIT 1",
            'iiis',
            array($idCentro, $idCurso, $idAula, $fecha),
            $link
        );

        $cache[$clave] = $resultado && db_num_rows($resultado) > 0;
        return $cache[$clave];
    }
}

if (!function_exists('app_puede_gestionar_merienda_aula')) {
    function app_puede_gestionar_merienda_aula($idUsuario, $nivelAcceso, $idAula, $fecha, $link = null)
    {
        $idUsuario = (int) $idUsuario;
        $nivelAcceso = (int) $nivelAcceso;
        $idAula = (int) $idAula;

        if ($idUsuario <= 0 || $idAula <= 0 || !app_fecha_iso_valida($fecha)) {
            return false;
        }

        // La agenda antigua solo permite modificar otros dias al administrador.
        if ($fecha !== date('Y-m-d') && $nivelAcceso !== 4) {
            return false;
        }

        if ($nivelAcceso >= 3 && $nivelAcceso < 10) {
            return true;
        }

        if ($nivelAcceso !== 2) {
            return false;
        }

        $resultado = db_query_params(
            "SELECT id
             FROM personal_historial
             WHERE id_empleado=? AND id_aula=?
               AND (fecha_inicio IS NULL OR fecha_inicio='0000-00-00' OR fecha_inicio<=?)
               AND (fecha_fin IS NULL OR fecha_fin='0000-00-00' OR fecha_fin>=?)
             LIMIT 1",
            'iiss',
            array($idUsuario, $idAula, $fecha, $fecha),
            $link
        );

        return $resultado && db_num_rows($resultado) > 0;
    }
}

if (!function_exists('app_actualizar_merienda_aula')) {
    function app_actualizar_merienda_aula($idCentro, $idCurso, $idAula, $fecha, $quitar, $idUsuario, $link = null)
    {
        $idCentro = (int) $idCentro;
        $idCurso = (int) $idCurso;
        $idAula = (int) $idAula;
        $idUsuario = (int) $idUsuario;
        $quitar = (bool) $quitar;

        if ($idCentro <= 0 || $idCurso <= 0 || $idAula <= 0 || !app_fecha_iso_valida($fecha)) {
            return false;
        }

        $activa = $quitar ? 1 : 0;
        $motivo = $quitar
            ? 'Merienda retirada desde el control del aula'
            : 'Merienda incluida de nuevo desde el control del aula';

        $guardado = db_query_params(
            "INSERT INTO aulas_exclusiones_merienda
                (id_centro,id_curso,id_aula,fecha,motivo,activa,creado_por,fecha_actualizacion)
             VALUES (?,?,?,?,?,?,?,NOW())
             ON DUPLICATE KEY UPDATE
                motivo=VALUES(motivo), activa=VALUES(activa),
                creado_por=VALUES(creado_por), fecha_actualizacion=NOW()",
            'iiissii',
            array($idCentro, $idCurso, $idAula, $fecha, $motivo, $activa, $idUsuario),
            $link
        );
        if (!$guardado) {
            return false;
        }

        // La vista crea estos registros al abrirse. Este INSERT hace la accion
        // segura tambien cuando algun alumno todavia no tenia fila de agenda.
        $creados = db_query_params(
            "INSERT INTO agenda_alumnos_comidas (id_alumno,dia,id_aula)
             SELECT a.id_alumno, ?, a.aula
             FROM alumnos a
             WHERE a.id_centro=? AND a.aula=?
               AND a.fecha_matricula<=? AND a.fecha_baja>=?
               AND NOT EXISTS (
                    SELECT 1 FROM agenda_alumnos_comidas ac
                    WHERE ac.id_alumno=a.id_alumno AND ac.id_aula=a.aula AND ac.dia=?
               )",
            'siisss',
            array($fecha, $idCentro, $idAula, $fecha, $fecha, $fecha),
            $link
        );
        if (!$creados) {
            return false;
        }

        $cantidad = $quitar ? 3 : 0;
        $actualizados = db_query_params(
            "UPDATE agenda_alumnos_comidas
             SET cant_merienda1=?, cant_merienda2=?
             WHERE id_aula=? AND dia=?",
            'iiis',
            array($cantidad, $cantidad, $idAula, $fecha),
            $link
        );
        if (!$actualizados) {
            return false;
        }

        return array(
            'activa' => $quitar,
            'actualizados' => db_affected_rows($link)
        );
    }
}

