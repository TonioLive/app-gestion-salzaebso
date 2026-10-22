<?php

if (!function_exists('lapices_help_catalog')) {
    function lapices_help_catalog()
    {
        return array(
            array(
                'id' => 'inicio',
                'mark' => 'IN',
                'title' => 'Inicio y selección de centro',
                'summary' => 'Empieza la jornada, selecciona el centro y entra en cada módulo.',
                'audience' => 'Todo el personal',
                'patterns' => array('/index.php', '/gestion/panel/modulo.php'),
                'keywords' => 'inicio centro resumen módulos fichar asistencia cambiar centro buscador',
                'steps' => array(
                    'Después de iniciar sesión, selecciona el centro en el que vas a trabajar.',
                    'Comprueba que el nombre del centro aparece en la parte superior antes de introducir datos.',
                    'Abre el módulo que necesites desde el menú lateral o utiliza el buscador superior.',
                    'Si tienes activa la vista con análisis, los datos de alumnado, asistencia y comedor se actualizan automáticamente.',
                ),
                'tips' => array(
                    'Si cambias de escuela durante el día, utiliza “Cambiar centro” antes de continuar.',
                    'En móvil, pulsa el botón de menú de la esquina superior para ver todos los módulos.',
                ),
                'links' => array(
                    array('label' => 'Ir al inicio', 'href' => '/index.php'),
                    array('label' => 'Personalizar mi pantalla', 'href' => '/gestion/panel/personalizacion.php'),
                ),
            ),
            array(
                'id' => 'alumnos',
                'mark' => 'AL',
                'title' => 'Alumnos y matrículas',
                'summary' => 'Altas, búsqueda, familiares, contactos y listados de alumnos.',
                'audience' => 'Administración y coordinación',
                'patterns' => array('/alumnos/', '/gestion/centros/listado_alumnos.php', '/gestion/centros/listado_alergias.php'),
                'keywords' => 'alumno matrícula nuevo editar familiar contacto teléfonos estado civil listado aula alergias piscina comedor',
                'steps' => array(
                    'Entra en Alumnos y elige “Inscribir alumno” para crear una matrícula o “Buscar alumno” para abrir una ficha existente.',
                    'Completa los cuatro pasos en orden. Guarda cada pantalla antes de pasar a la siguiente.',
                    'En el paso 2 crea o selecciona los familiares; sus teléfonos se reutilizan después como contactos del alumno.',
                    'En el paso 3 revisa el primer y segundo contacto propuestos automáticamente y corrígelos si fuera necesario.',
                    'Usa el listado simplificado cuando solo necesites nombre, aula, nacimiento, piscina, comedor y fechas del curso.',
                    'En los listados largos elige 20, 50 u 80 filas por página y utiliza los números inferiores para avanzar.',
                ),
                'tips' => array(
                    'Los campos obligatorios están marcados; no cierres la ventana antes de guardar.',
                    'Desde un listado de aula puedes pulsar el nombre del alumno para abrir su ficha.',
                    'Mostrar menos filas hace que las búsquedas y listados grandes respondan con mayor rapidez.',
                ),
                'links' => array(
                    array('label' => 'Gestión de alumnos', 'href' => '/alumnos/gestion_alumnos.php'),
                    array('label' => 'Listado simplificado', 'href' => '/alumnos/listado_simplificado.php'),
                ),
            ),
            array(
                'id' => 'aulas',
                'mark' => 'AU',
                'title' => 'Aulas, asistencia y agenda diaria',
                'summary' => 'Consulta tus aulas y registra la información diaria del alumnado.',
                'audience' => 'Educadoras, coordinación y administración',
                'patterns' => array('/aulas/', '/aulas_docentes/', '/gestion/agenda/', '/gestion/agenda_dia/', '/gestion/centros/gestion_aulas.php'),
                'keywords' => 'aula asistencia alumnos comida salud cambios anotaciones actividades programación merienda',
                'steps' => array(
                    'Selecciona el curso y el aula que vas a gestionar.',
                    'Comprueba la fecha antes de registrar asistencia o información diaria.',
                    'Abre la pestaña correspondiente: alumnos, comida, salud, cambios, anotaciones o programación.',
                    'Guarda los cambios y revisa que el estado quede actualizado antes de salir.',
                    'En Comida puedes quitar la merienda a toda el aula para un día concreto; cocina dejará de contar ese grupo.',
                ),
                'tips' => array(
                    'Las educadoras solo ven las aulas y funciones asignadas a su perfil.',
                    'En una tabla ancha puedes desplazar el contenido horizontalmente sin hacer zoom.',
                ),
                'links' => array(
                    array('label' => 'Gestión de aulas', 'href' => '/aulas/gestion_aulas.php'),
                    array('label' => 'Mis aulas', 'href' => '/aulas_docentes/mis_aulas.php'),
                ),
            ),
            array(
                'id' => 'comedor',
                'mark' => 'CO',
                'title' => 'Comedor, cocina y menús',
                'summary' => 'Menús mensuales, asistencia, previsiones de cocina y preparación de platos.',
                'audience' => 'Cocina, educadoras, coordinación y administración',
                'patterns' => array('/cocina/', '/menu/'),
                'keywords' => 'comedor cocina menú menus mes imprimir platos previsión compras albarán temperatura merienda',
                'steps' => array(
                    'Entra en Comedor para ver todas las opciones relacionadas con cocina en una sola pantalla.',
                    'Revisa la asistencia al comedor antes de preparar previsiones o compras.',
                    'Consulta preparación de platos, albaranes y temperaturas desde sus tarjetas correspondientes.',
                    'Para entregar el menú a las familias, abre “Imprimir menús”, elige el mes y utiliza la impresión horizontal.',
                ),
                'tips' => array(
                    'Una merienda quitada desde el aula se descuenta de las previsiones de cocina de ese día.',
                    'Antes de imprimir, revisa el mes y usa orientación horizontal para que el calendario quepa en una hoja.',
                ),
                'links' => array(
                    array('label' => 'Gestión de comedor', 'href' => '/cocina/gestion_comedor.php'),
                    array('label' => 'Imprimir menús', 'href' => '/menu/menus_mes.php'),
                ),
            ),
            array(
                'id' => 'facturacion',
                'mark' => 'FA',
                'title' => 'Facturación, caja y servicios',
                'summary' => 'Gestiona servicios, recibos, facturas, cobros e informes económicos.',
                'audience' => 'Administración y coordinación autorizada',
                'patterns' => array('/facturacion/', '/caja/'),
                'keywords' => 'factura facturación caja recibo servicio tarifa cobrar devolución abono banco sepa',
                'steps' => array(
                    'Selecciona primero el centro y el curso para evitar trabajar con datos de otra escuela.',
                    'Busca al alumno, familia o servicio que necesites modificar.',
                    'Revisa importes, fechas y concepto antes de guardar o generar un cobro.',
                    'Comprueba el resultado en el historial de facturación del alumno.',
                ),
                'tips' => array(
                    'Los cambios de material cobrados generan el ajuste negativo del artículo devuelto y el cobro del nuevo.',
                    'No repitas una operación si el navegador tarda: comprueba antes el historial.',
                ),
                'links' => array(
                    array('label' => 'Gestión de facturación', 'href' => '/facturacion/gestion_facturacion.php'),
                    array('label' => 'Caja', 'href' => '/caja/listado_caja.php'),
                ),
            ),
            array(
                'id' => 'material',
                'mark' => 'MA',
                'title' => 'Almacén, uniformes y material',
                'summary' => 'Prepara pedidos, controla el stock y registra entregas o cambios.',
                'audience' => 'Administración, coordinación y almacén',
                'patterns' => array('/almacen/', '/gestion/material/'),
                'keywords' => 'almacén material uniforme talla niño niña stock pedido entrega cambio cobrar pdf estimación macuto babero chándal',
                'steps' => array(
                    'Selecciona el curso para calcular una estimación según el alumnado matriculado.',
                    'Revisa la recomendación de cada talla y escribe la cantidad que realmente quieres pedir.',
                    'Marca los artículos y genera el PDF desglosado para enviarlo al proveedor.',
                    'Confirma el pedido para registrar la entrada y sumar las unidades al stock.',
                    'En Control de entregas selecciona alumno, artículo y cantidad; después registra el cobro o el cambio de talla.',
                ),
                'tips' => array(
                    'La cantidad recomendada es una ayuda: puedes sustituirla por cualquier cantidad.',
                    'En un cambio, selecciona primero el artículo entregado originalmente y después el nuevo.',
                ),
                'links' => array(
                    array('label' => 'Gestión de material', 'href' => '/gestion/material/gestion_material.php'),
                    array('label' => 'Control de entregas', 'href' => '/gestion/material/control_entregas.php'),
                ),
            ),
            array(
                'id' => 'incidencias',
                'mark' => 'IC',
                'title' => 'Incidencias',
                'summary' => 'Registra, consulta y realiza el seguimiento de avisos e incidencias.',
                'audience' => 'Personal autorizado',
                'patterns' => array('/gestion/incidencias/'),
                'keywords' => 'incidencia aviso accidente seguimiento histórico estado',
                'steps' => array(
                    'Abre Gestión de incidencias y elige el tipo de incidencia.',
                    'Identifica correctamente el centro, aula, alumno o trabajador relacionado.',
                    'Describe lo ocurrido con fecha, hora y actuaciones realizadas.',
                    'Guarda y utiliza el listado para revisar el estado o completar el seguimiento.',
                ),
                'tips' => array(
                    'Escribe hechos concretos y evita información que no sea necesaria para gestionar la incidencia.',
                ),
                'links' => array(
                    array('label' => 'Gestión de incidencias', 'href' => '/gestion/incidencias/menu_incidencias.php'),
                ),
            ),
            array(
                'id' => 'personal',
                'mark' => 'PE',
                'title' => 'Personal y usuarios',
                'summary' => 'Equipo, contratos, horarios, accesos y datos de usuario.',
                'audience' => 'Administración y perfiles autorizados',
                'patterns' => array('/facturacion/gestion_educadoras.php', '/gestion/docentes/', '/gestion/personal/', '/gestion/usuarios/'),
                'keywords' => 'personal usuario educadora coordinadora cocina contrato horario contraseña acceso alta baja',
                'steps' => array(
                    'Utiliza Gestión de personal para buscar a una trabajadora y consultar su situación.',
                    'Abre Editar para modificar datos, permisos o asignaciones; guarda antes de volver al listado.',
                    'En Usuarios de gestión puedes consultar el usuario y mostrar u ocultar la contraseña con su botón.',
                    'Revisa siempre el centro y el tipo de usuario antes de conceder acceso.',
                ),
                'tips' => array(
                    'Las contraseñas son información confidencial: no las imprimas ni las compartas por mensajería.',
                    'Si un nombre no aparece, entra en Editar y comprueba nombre y apellidos del registro.',
                ),
                'links' => array(
                    array('label' => 'Gestión de personal', 'href' => '/facturacion/gestion_educadoras.php'),
                    array('label' => 'Usuarios de gestión', 'href' => '/gestion/usuarios/gestion_u.php'),
                ),
            ),
            array(
                'id' => 'admisiones',
                'mark' => 'AD',
                'title' => 'Inscripciones web y contactos interesados',
                'summary' => 'Acompaña a una familia desde la solicitud inicial hasta la matrícula.',
                'audience' => 'Administración y coordinación',
                'patterns' => array('/formulario/', '/contactos/', '/lista_espera/'),
                'keywords' => 'inscripción web interesado contacto seguimiento matricular nuevo contacto lista espera familia',
                'steps' => array(
                    'En Inscripciones web revisa los datos enviados por la familia.',
                    'Pulsa “Pasar a interesado” si todavía necesita seguimiento comercial o una visita al centro.',
                    'Completa en Contactos interesados los datos que falten y registra las gestiones realizadas.',
                    'Cuando la familia confirme la plaza, pulsa “Inscribir” para iniciar la matrícula sin volver a copiar sus datos.',
                    'También puedes crear un contacto manual cuando la familia acuda directamente a la escuela.',
                ),
                'tips' => array(
                    'Busca antes por nombre o teléfono para evitar contactos duplicados.',
                    'No matricules hasta haber comprobado centro, curso y fechas.',
                ),
                'links' => array(
                    array('label' => 'Inscripciones web', 'href' => '/formulario/listado.php'),
                    array('label' => 'Contactos interesados', 'href' => '/contactos/listado_contactos.php'),
                ),
            ),
            array(
                'id' => 'mensajes',
                'mark' => 'ME',
                'title' => 'Mensajes',
                'summary' => 'Comunicación con las familias y con el equipo del centro.',
                'audience' => 'Personal con acceso a mensajería',
                'patterns' => array('/gestion/mensajes/', '/herramientas/enviar_mensaje'),
                'keywords' => 'mensaje chat familia educadora coordinadora nuevo responder recibido enviado',
                'steps' => array(
                    'Abre Centro de mensajes y elige arriba el aula que quieres consultar, o selecciona Todas mis aulas.',
                    'Busca por el nombre del alumno o de su familiar; aparecen también las familias que todavía no tienen mensajes.',
                    'Las conversaciones se ordenan por la fecha del último mensaje y las pendientes llevan un contador verde.',
                    'Pulsa una familia para abrir el historial y responde desde el cuadro inferior, como en un chat.',
                    'Pulsa la campana junto a tu nombre para ver lo pendiente y entrar directamente en la conversación correspondiente.',
                ),
                'tips' => array(
                    'No envíes datos personales de otro alumno dentro de una conversación familiar.',
                    'En móvil, utiliza la flecha de la conversación para volver al listado de familias.',
                ),
                'links' => array(
                    array('label' => 'Centro de mensajes', 'href' => '/gestion/mensajes/mensajeria.php'),
                ),
            ),
            array(
                'id' => 'informes',
                'mark' => 'IF',
                'title' => 'Informes personalizados',
                'summary' => 'Elige datos, aplica filtros, guarda configuraciones y exporta a Excel.',
                'audience' => 'Administración',
                'patterns' => array('/gestion/informes/'),
                'keywords' => 'informe personalizado tabla columna filtro relación ordenar excel guardar compartido',
                'steps' => array(
                    'Elige la tabla principal del informe.',
                    'Añade las tablas relacionadas que necesites y selecciona solo las columnas que quieras ver.',
                    'Aplica filtros y ordenación; utiliza Previsualizar para comprobar el resultado.',
                    'Escribe un nombre reconocible y guarda la configuración para utilizarla de nuevo.',
                    'Abre Informes guardados para ejecutarlo con datos actualizados o exportarlo a Excel.',
                ),
                'tips' => array(
                    'Empieza con pocas columnas y amplía el informe después de comprobarlo.',
                    'Un informe guardado conserva la configuración, no una copia antigua de los datos.',
                ),
                'links' => array(
                    array('label' => 'Crear informe', 'href' => '/gestion/informes/'),
                    array('label' => 'Informes guardados', 'href' => '/gestion/informes/guardados.php'),
                ),
            ),
            array(
                'id' => 'gabinete',
                'mark' => 'GA',
                'title' => 'Gabinete psicopedagógico',
                'summary' => 'Seguimiento, pruebas, informes y documentación del alumnado.',
                'audience' => 'Gabinete, coordinación y administración autorizada',
                'patterns' => array('/gestion/gabinete/'),
                'keywords' => 'gabinete psicopedagógico brunet mccarthy comparativo historial evaluación documento informe alumno',
                'steps' => array(
                    'Selecciona el aula y después el alumno con el que vas a trabajar.',
                    'Abre Seguimiento para consultar su información y evaluaciones anteriores.',
                    'Elige la prueba o documento que necesitas cumplimentar.',
                    'Guarda los resultados antes de generar o descargar el informe.',
                ),
                'tips' => array(
                    'Comprueba siempre que estás en la ficha del alumno correcto antes de guardar una prueba.',
                ),
                'links' => array(
                    array('label' => 'Abrir Gabinete', 'href' => '/gestion/gabinete/'),
                ),
            ),
            array(
                'id' => 'personalizacion',
                'mark' => 'PA',
                'title' => 'Personalización del programa',
                'summary' => 'Elige el color y la vista de inicio que te resulte más cómoda.',
                'audience' => 'Todo el personal',
                'patterns' => array('/gestion/panel/personalizacion.php'),
                'keywords' => 'personalizar color vista clásica análisis inicio dashboard',
                'steps' => array(
                    'Selecciona un color recomendado o elige cualquier color con el selector.',
                    'Elige Vista clásica o Vista con análisis.',
                    'Pulsa “Guardar personalización”. El cambio queda asociado a tu usuario.',
                ),
                'tips' => array(
                    'La vista con análisis muestra alumnado, asistencia, comedor y aulas del centro seleccionado.',
                ),
                'links' => array(
                    array('label' => 'Personalizar programa', 'href' => '/gestion/panel/personalizacion.php'),
                ),
            ),
            array(
                'id' => 'familias',
                'mark' => 'FM',
                'title' => 'Aplicación de familias',
                'summary' => 'Inicio, agenda, mensajes, documentos y cierre de sesión en móvil.',
                'audience' => 'Familias',
                'patterns' => array('/app/'),
                'keywords' => 'familias padres app móvil agenda mensajes documentos menú cerrar sesión chat',
                'steps' => array(
                    'La familia accede con las credenciales facilitadas por el centro.',
                    'Desde la barra inferior puede abrir Inicio, Mensajes, Agenda, Documentos o Menú.',
                    'En Mensajes puede abrir una conversación, responder o crear un mensaje para los destinatarios disponibles.',
                    'En Agenda puede cambiar de fecha y consultar los eventos sin hacer zoom.',
                    'Para terminar, debe pulsar el botón de cerrar sesión de la parte superior o del menú.',
                ),
                'tips' => array(
                    'En un móvil se recomienda añadir la página a la pantalla de inicio para abrirla como una aplicación.',
                ),
                'links' => array(
                    array('label' => 'Abrir aplicación de familias', 'href' => '/app/index.php'),
                ),
            ),
            array(
                'id' => 'general',
                'mark' => '?',
                'title' => 'Ayuda general de la pantalla',
                'summary' => 'Cómo orientarte y trabajar con seguridad en cualquier apartado.',
                'audience' => 'Todo el personal',
                'patterns' => array(),
                'keywords' => 'ayuda general pantalla guardar volver cancelar buscar menú centro',
                'steps' => array(
                    'Comprueba el centro, el curso y la fecha que aparecen en la pantalla.',
                    'Lee el título y las indicaciones antes de introducir información.',
                    'Completa los campos y pulsa el botón principal para guardar.',
                    'Revisa el mensaje de confirmación antes de cerrar o volver atrás.',
                ),
                'tips' => array(
                    'Si tienes dudas, abre el manual completo y busca el nombre de la tarea que quieres realizar.',
                    'No pulses dos veces un botón de guardar aunque la operación tarde unos segundos.',
                ),
                'links' => array(
                    array('label' => 'Abrir manual completo', 'href' => '/gestion/ayuda/'),
                ),
            ),
        );
    }

    function lapices_help_normalize_path($path)
    {
        $path = is_string($path) ? $path : '/';
        $parsed = parse_url($path, PHP_URL_PATH);
        if (!is_string($parsed) || $parsed === '') {
            return '/';
        }

        return '/' . ltrim(str_replace('\\', '/', $parsed), '/');
    }

    function lapices_help_for_path($path)
    {
        $path = lapices_help_normalize_path($path);
        $catalog = lapices_help_catalog();
        $fallback = end($catalog);
        $best = false;
        $best_length = -1;

        foreach ($catalog as $entry) {
            foreach ($entry['patterns'] as $pattern) {
                if (strpos($path, $pattern) === 0 && strlen($pattern) > $best_length) {
                    $best = $entry;
                    $best_length = strlen($pattern);
                }
            }
        }

        return $best !== false ? $best : $fallback;
    }
}
