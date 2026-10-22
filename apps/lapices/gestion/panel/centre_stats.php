<?php

if (!function_exists('panel_empty_centre_stats')) {
    function panel_empty_centre_stats()
    {
        return array(
            'alumnos' => 0,
            'asistencia' => 0,
            'comedor' => 0,
            'aulas' => 0,
            'porcentaje_asistencia' => 0,
            'actualizado' => date('H:i:s'),
        );
    }
}

if (!function_exists('panel_fetch_centre_stats')) {
    function panel_fetch_centre_stats($database, $centre_id, $date)
    {
        $stats = panel_empty_centre_stats();
        $centre_id = (int) $centre_id;
        $date = (string) $date;

        if (!($database instanceof mysqli) || $centre_id <= 0) {
            return $stats;
        }

        $statement = mysqli_prepare(
            $database,
            'SELECT '
            . 'COUNT(DISTINCT a.id_usuario) AS alumnos, '
            . 'COUNT(DISTINCT CASE WHEN sc.id_alumno IS NOT NULL THEN a.id_usuario END) AS asistencia, '
            . 'COUNT(DISTINCT CASE WHEN a.comedor<>2 '
            . 'AND COALESCE(had.no_comedor, 0)=0 THEN a.id_usuario END) AS comedor, '
            . 'COUNT(DISTINCT CASE WHEN ca.tipo=0 THEN a.aula END) AS aulas '
            . 'FROM alumnos a '
            . 'LEFT JOIN ('
            . 'SELECT DISTINCT id_alumno FROM servicios_consumidos WHERE fecha=?'
            . ') sc ON sc.id_alumno=a.id_usuario '
            . 'LEFT JOIN horario_alumnos_dias had '
            . 'ON had.id_usuario=a.id_usuario AND had.fecha=? '
            . 'LEFT JOIN centro_aulas ca ON ca.id_aula=a.aula '
            . 'WHERE a.id_centro=? '
            . 'AND a.fecha_matricula<=? '
            . 'AND a.fecha_baja>=?'
        );

        if (!$statement) {
            return $stats;
        }

        mysqli_stmt_bind_param(
            $statement,
            'ssiss',
            $date,
            $date,
            $centre_id,
            $date,
            $date
        );
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : false;

        if ($result) {
            mysqli_free_result($result);
        }
        mysqli_stmt_close($statement);

        if (!$row) {
            return $stats;
        }

        $stats['alumnos'] = (int) $row['alumnos'];
        $stats['asistencia'] = (int) $row['asistencia'];
        $stats['comedor'] = (int) $row['comedor'];
        $stats['aulas'] = (int) $row['aulas'];
        $stats['porcentaje_asistencia'] = $stats['alumnos'] > 0
            ? (int) round(($stats['asistencia'] / $stats['alumnos']) * 100)
            : 0;
        $stats['actualizado'] = date('H:i:s');

        return $stats;
    }
}
