<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/config.php';
require_once dirname(__DIR__) . '/panel/bootstrap.php';

$panel_active_section = 'gabinete';
$panel_topbar_label = 'Administración';
$panel_topbar_title = 'Gabinete psicopedagógico';

$panel_gabinete_groups = array(
    array(
        'key' => 'alumnado',
        'title' => 'Aulas y alumnado',
        'summary' => 'Selecciona el aula y accede a la ficha de trabajo de cada alumno.',
        'items' => array(
            array(
                'mark' => 'AU',
                'title' => 'Listado de aulas',
                'description' => 'Consulta los centros, cursos y aulas disponibles.',
                'meta' => 'Punto de entrada principal',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'AL',
                'title' => 'Alumnado del aula',
                'description' => 'Abre el listado completo y entra en el seguimiento individual.',
                'meta' => 'Después de seleccionar aula',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'OR',
                'title' => 'Orlas y fotografías',
                'description' => 'Visualiza e imprime las fotografías del alumnado por aula.',
                'meta' => 'Disponible en cada aula',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'CA',
                'title' => 'Calendario educativo',
                'description' => 'Accede al calendario y a la planificación vinculada al aula.',
                'meta' => 'Disponible en cada aula',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
        ),
    ),
    array(
        'key' => 'seguimiento',
        'title' => 'Evaluación y seguimiento',
        'summary' => 'Pruebas de desarrollo, historial educativo y evolución individual.',
        'items' => array(
            array(
                'mark' => 'SE',
                'title' => 'Seguimiento individual',
                'description' => 'Consulta datos personales, observaciones y pruebas realizadas.',
                'meta' => 'Selecciona primero al alumno',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'BR',
                'title' => 'Prueba Brunet-Lézine',
                'description' => 'Crea, modifica y consulta resultados de la escala Brunet.',
                'meta' => 'Prueba e informe',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'BA',
                'title' => 'Brunet ampliado',
                'description' => 'Gestiona la evaluación ampliada y genera su informe.',
                'meta' => 'Prueba e informe',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'MC',
                'title' => 'Escala McCarthy',
                'description' => 'Registra la prueba, revisa los resultados y descarga el informe.',
                'meta' => 'Prueba e informe',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'CO',
                'title' => 'Informe comparativo',
                'description' => 'Compara evaluaciones y documenta la evolución del alumno.',
                'meta' => 'Seguimiento longitudinal',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'HE',
                'title' => 'Historial educativo',
                'description' => 'Cumplimenta los anexos I y II del historial educativo.',
                'meta' => 'Cuestionarios e impresión',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
        ),
    ),
    array(
        'key' => 'informes',
        'title' => 'Informes y documentación',
        'summary' => 'Generación, impresión y archivo de la documentación psicopedagógica.',
        'items' => array(
            array(
                'mark' => 'IN',
                'title' => 'Resultados e informes',
                'description' => 'Consulta resultados, edita pruebas y genera informes imprimibles.',
                'meta' => 'Brunet, McCarthy y comparativas',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'EV',
                'title' => 'Evaluaciones trimestrales',
                'description' => 'Accede a los informes del primer, segundo y tercer trimestre.',
                'meta' => 'Por alumno y curso',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'PO',
                'title' => 'Portadas y listados',
                'description' => 'Imprime portadas psicopedagógicas y listados del aula.',
                'meta' => 'Impresión individual o conjunta',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
            array(
                'mark' => 'DO',
                'title' => 'Carpeta documental',
                'description' => 'Examina y guarda documentos asociados al alumno y al curso.',
                'meta' => 'PDF, Word, Excel y presentaciones',
                'href' => '/gestion/gabinete/gestion_aulas.php',
            ),
        ),
    ),
);

if (
    $permitido === true &&
    $panel_has_selected_centre &&
    !$panel_can_access_gabinete
) {
    http_response_code(403);
}

require dirname(__DIR__) . '/panel/views/header.php';

if ($permitido !== true) {
    require dirname(__DIR__) . '/panel/views/login.php';
} elseif (!$panel_has_selected_centre) {
    require dirname(__DIR__) . '/panel/views/select-centre.php';
} elseif ($panel_can_access_gabinete) {
    require dirname(__DIR__) . '/panel/views/gabinete.php';
} else {
    ?>
    <section class="module-landing module-landing--missing">
        <span class="eyebrow">Acceso restringido</span>
        <h1>No tienes acceso al gabinete psicopedagógico</h1>
        <p>Esta herramienta solo se muestra a los perfiles autorizados.</p>
        <a class="button" href="/index.php">Volver al inicio</a>
    </section>
    <?php
}

require dirname(__DIR__) . '/panel/views/footer.php';
