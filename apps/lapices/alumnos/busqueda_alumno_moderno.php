<?php
require('../configuracion/parte_superior_privado.php');

if (empty($permitido)) {
    require('../configuracion/parte_inferior.php');
    return;
}

require_once dirname(__DIR__) . '/gestion/panel/paginacion.php';

$busqueda = trim(app_request_value($_GET, 'b', ''));
$tipo_busqueda = strtoupper(app_request_value($_GET, 'Tipo_b', 'AND'));
$tipo_busqueda = $tipo_busqueda === 'OR' ? 'OR' : 'AND';
$inicial = strtoupper(substr(app_request_value($_GET, 'i', 'A'), 0, 1));
if (!preg_match('/^[A-Z]$/', $inicial)) {
    $inicial = 'A';
}
$por_pagina = app_pagination_per_page($_GET, 'por_pagina');
$pagina = app_pagination_page($_GET, 'pagina');

$filtro_centro_sql = '';
$filtro_centro_tipos = '';
$filtro_centro_parametros = array();
if ((int) $tipo_centro_actual === 1) {
    $filtro_centro_sql = 'c.centro_propio=1';
} else {
    $filtro_centro_sql = 'a.id_centro=?';
    $filtro_centro_tipos = 'i';
    $filtro_centro_parametros[] = (int) $centro_actual;
}

$consulta_iniciales = db_query_params(
    'SELECT DISTINCT UPPER(LEFT(u.Apellido1, 1)) AS inicial '
    . 'FROM usuarios u '
    . 'INNER JOIN alumnos a ON a.id_usuario=u.id_usuario '
    . 'INNER JOIN centros c ON c.id_centro=a.id_centro '
    . 'WHERE ' . $filtro_centro_sql . ' AND u.tipo_inscripcion=1 '
    . 'ORDER BY inicial',
    $filtro_centro_tipos,
    $filtro_centro_parametros
);

$iniciales_disponibles = array();
if ($consulta_iniciales) {
    while ($fila_inicial = db_fetch_assoc($consulta_iniciales)) {
        $iniciales_disponibles[$fila_inicial['inicial']] = true;
    }
}

$condicion_busqueda = '';
$tipos = $filtro_centro_tipos;
$parametros = $filtro_centro_parametros;

if ($busqueda === '') {
    $condicion_busqueda = 'u.Apellido1 LIKE ?';
    $tipos .= 's';
    $parametros[] = $inicial . '%';
    $descripcion_resultado = 'Apellidos que comienzan por ' . $inicial;
} else {
    $palabras = preg_split('/\s+/', $busqueda);
    $palabras = array_slice(array_filter($palabras, 'strlen'), 0, 5);
    $grupos = array();

    foreach ($palabras as $palabra) {
        $campos = array(
            'u.Nombre LIKE ?',
            'u.Apellido1 LIKE ?',
            'u.Apellido2 LIKE ?',
            'u.Poblacion LIKE ?',
            'u.DNI LIKE ?',
            'u.Direccion LIKE ?',
            'u.telefono1 LIKE ?',
            'u.telefono2 LIKE ?',
            'u.movil LIKE ?',
            'u.Email LIKE ?',
            'u.observaciones LIKE ?',
            'u.SIP LIKE ?',
        );
        $valor_like = '%' . $palabra . '%';
        foreach ($campos as $campo) {
            $tipos .= 's';
            $parametros[] = $valor_like;
        }

        if (ctype_digit($palabra)) {
            $campos[] = 'u.id_usuario=?';
            $tipos .= 'i';
            $parametros[] = (int) $palabra;
        }

        $grupos[] = '(' . implode(' OR ', $campos) . ')';
    }

    if (empty($grupos)) {
        $condicion_busqueda = '1=0';
    } else {
        $condicion_busqueda = implode(' ' . $tipo_busqueda . ' ', $grupos);
    }
    $descripcion_resultado = 'Resultado de la busqueda';
}

$sql_total_alumnos = 'SELECT COUNT(DISTINCT u.id_usuario) AS total '
    . 'FROM usuarios u '
    . 'INNER JOIN alumnos a ON a.id_usuario=u.id_usuario '
    . 'INNER JOIN centros c ON c.id_centro=a.id_centro '
    . 'WHERE ' . $filtro_centro_sql . ' AND u.tipo_inscripcion=1 '
    . 'AND (' . $condicion_busqueda . ')';
$consulta_total_alumnos = db_query_params(
    $sql_total_alumnos,
    $tipos,
    $parametros
);
$total_alumnos = 0;
if ($consulta_total_alumnos) {
    $fila_total_alumnos = db_fetch_assoc($consulta_total_alumnos);
    $total_alumnos = (int) $fila_total_alumnos['total'];
}
$total_paginas = max(1, (int) ceil($total_alumnos / $por_pagina));
$pagina = min($pagina, $total_paginas);
$desplazamiento = ($pagina - 1) * $por_pagina;

$sql_alumnos = 'SELECT '
    . 'u.id_usuario, u.Apellido1, u.Apellido2, u.Nombre, u.SIP, u.Poblacion, '
    . 'MAX(u.fecha_nacimiento) AS fecha_nacimiento, '
    . 'MAX(u.nonato) AS nonato, MAX(a.id_curso) AS id_curso_alumno, '
    . "GROUP_CONCAT(DISTINCT CONCAT(ca.nombre, ' - ', ac.nombre) "
    . "ORDER BY ac.id_curso SEPARATOR '||') AS aulas "
    . 'FROM usuarios u '
    . 'INNER JOIN alumnos a ON a.id_usuario=u.id_usuario '
    . 'INNER JOIN centros c ON c.id_centro=a.id_centro '
    . 'LEFT JOIN centro_aulas ca ON ca.id_aula=a.aula '
    . 'LEFT JOIN cursos_academicos ac ON ac.id_curso=a.id_curso '
    . 'WHERE ' . $filtro_centro_sql . ' AND u.tipo_inscripcion=1 '
    . 'AND (' . $condicion_busqueda . ') '
    . 'GROUP BY u.id_usuario, u.Apellido1, u.Apellido2, u.Nombre, '
    . 'u.SIP, u.Poblacion '
    . 'ORDER BY u.Apellido1, u.Apellido2, u.Nombre LIMIT ? OFFSET ?';

$tipos_listado = $tipos . 'ii';
$parametros_listado = array_merge($parametros, array($por_pagina, $desplazamiento));

$consulta_alumnos = db_query_params(
    $sql_alumnos,
    $tipos_listado,
    $parametros_listado
);

$alumnos_encontrados = array();
if ($consulta_alumnos) {
    while ($alumno = db_fetch_assoc($consulta_alumnos)) {
        $alumnos_encontrados[] = $alumno;
    }
}

function alumno_edad_actual($fecha_nacimiento)
{
    if (!$fecha_nacimiento || $fecha_nacimiento === '0000-00-00') {
        return '';
    }

    $nacimiento = new DateTime($fecha_nacimiento);
    $hoy = new DateTime('today');
    return $nacimiento->diff($hoy)->y;
}
?>
<section class="legacy-module-page" aria-labelledby="alumnos-search-title">
    <header class="legacy-module-page__heading">
        <div>
            <span class="legacy-module-page__eyebrow">Alumnos</span>
            <h1 id="alumnos-search-title">Buscar alumno</h1>
            <p>Busca por nombre, apellidos, documento, contacto, SIP o identificador.</p>
        </div>
        <a class="legacy-card-action legacy-card-action--primary legacy-heading-action" href="/alumnos/nuevo_alumno.php">
            Inscribir alumno
        </a>
    </header>

    <form class="legacy-search-panel" method="get" action="/alumnos/busqueda_alumno.php">
        <input type="hidden" name="por_pagina" value="<?= (int) $por_pagina; ?>">
        <label class="legacy-search-panel__field">
            <span>Texto de b&uacute;squeda</span>
            <input type="search" name="b" value="<?= app_escape($busqueda); ?>" placeholder="Nombre, tel&eacute;fono, SIP...">
        </label>
        <fieldset>
            <legend>Combinar palabras</legend>
            <label><input type="radio" name="Tipo_b" value="AND"<?= $tipo_busqueda === 'AND' ? ' checked' : ''; ?>> Todas</label>
            <label><input type="radio" name="Tipo_b" value="OR"<?= $tipo_busqueda === 'OR' ? ' checked' : ''; ?>> Cualquiera</label>
        </fieldset>
        <button type="submit">Buscar</button>
    </form>

    <?php if ($busqueda === '') { ?>
        <nav class="legacy-alphabet" aria-label="Filtrar por inicial">
            <?php foreach (range('A', 'Z') as $letra) {
                $disponible = isset($iniciales_disponibles[$letra]);
                ?>
                <?php if ($disponible) { ?>
                    <a class="<?= $letra === $inicial ? 'is-active' : ''; ?>" href="?i=<?= $letra; ?>&amp;por_pagina=<?= (int) $por_pagina; ?>"><?= $letra; ?></a>
                <?php } else { ?>
                    <span><?= $letra; ?></span>
                <?php } ?>
            <?php } ?>
        </nav>
    <?php } ?>

    <div class="legacy-results-heading">
        <div>
            <strong><?= app_escape($descripcion_resultado); ?></strong>
            <span><?= (int) $total_alumnos; ?> alumno(s)</span>
        </div>
    </div>

    <?php if (empty($alumnos_encontrados)) { ?>
        <div class="legacy-empty-state">
            <strong>No se han encontrado alumnos</strong>
            <p>Prueba con otro texto o selecciona una inicial diferente.</p>
        </div>
    <?php } else { ?>
        <div class="legacy-data-table-wrap">
            <table class="legacy-data-table" data-pagination="server">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>SIP</th>
                        <th>Nacimiento</th>
                        <th>Poblaci&oacute;n</th>
                        <th>Aulas</th>
                        <th><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos_encontrados as $alumno) {
                        $id_alumno = (int) $alumno['id_usuario'];
                        $fecha_nacimiento = '';
                        $edad = '';
                        if ((int) $alumno['nonato'] === 1) {
                            $fecha_nacimiento = 'No ha nacido';
                        } elseif (!empty($alumno['fecha_nacimiento'])
                            && $alumno['fecha_nacimiento'] !== '0000-00-00'
                        ) {
                            $fecha_nacimiento = date('d/m/Y', strtotime($alumno['fecha_nacimiento']));
                            $edad = alumno_edad_actual($alumno['fecha_nacimiento']);
                        }
                        $aulas = !empty($alumno['aulas'])
                            ? explode('||', $alumno['aulas'])
                            : array();
                        ?>
                        <tr>
                            <td>
                                <a class="legacy-student-name" href="/alumnos/nuevo_alumno1.php?u=<?= $id_alumno; ?>">
                                    <?= app_escape(trim($alumno['Apellido1'] . ' ' . $alumno['Apellido2'] . ', ' . $alumno['Nombre'])); ?>
                                </a>
                            </td>
                            <td><?= app_escape($alumno['SIP']); ?></td>
                            <td>
                                <?= app_escape($fecha_nacimiento); ?>
                                <?= $edad !== '' ? '<small>' . (int) $edad . ' a&ntilde;os</small>' : ''; ?>
                            </td>
                            <td><?= app_escape($alumno['Poblacion']); ?></td>
                            <td>
                                <?php foreach ($aulas as $aula) { ?>
                                    <span class="legacy-table-tag"><?= app_escape($aula); ?></span>
                                <?php } ?>
                            </td>
                            <td class="legacy-data-table__actions">
                                <a href="/alumnos/nuevo_alumno1.php?u=<?= $id_alumno; ?>">Abrir ficha</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php app_render_pagination(
            $total_alumnos,
            $pagina,
            $por_pagina,
            array('label' => 'alumnos')
        ); ?>
    <?php } ?>
</section>
<?php require('../configuracion/parte_inferior.php'); ?>
