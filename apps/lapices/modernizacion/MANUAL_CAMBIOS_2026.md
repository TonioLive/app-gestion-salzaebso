# Manual de cambios de Gestión Lápices

Última actualización: 3 de septiembre de 2026.

## 1. Punto de partida

La aplicación recibida era un desarrollo iniciado aproximadamente en 2005. Su
funcionamiento dependía de versiones antiguas de PHP y MySQL, utilizaba HTML de
tablas con anchuras fijas y tenía una interfaz pensada únicamente para pantallas
de ordenador de aquella época.

La auditoría inicial localizó:

- 2.679 archivos PHP ejecutables y unas 700.823 líneas de PHP.
- 270 tablas y aproximadamente 2,2 GB de datos.
- 259 tablas MyISAM y 11 InnoDB.
- Mezcla de codificaciones `latin1`, `utf8` y distintas intercalaciones.
- 28 columnas con fechas cero como valor predeterminado.
- 14 tablas sin clave primaria y ninguna clave foránea declarada.
- Contraseñas históricas almacenadas sin hash en `usuarios.pass`.
- 34.959 llamadas ejecutables a la API eliminada `mysql_*`, repartidas en
  1.512 archivos.
- Menús, formularios y listados con tamaños fijos, letra pequeña y elementos
  cortados en móvil, tablet y monitores actuales.

## 2. Modernización técnica

### PHP y acceso a base de datos

- Se fijó PHP 8.5 como versión objetivo del código modernizado.
- Se convirtieron todas las etiquetas cortas ejecutables y las construcciones
  de sintaxis que ya no son compatibles con PHP actual.
- Se repararon errores de sintaxis que ya existían en algunos módulos antiguos.
- Se retiraron todas las llamadas ejecutables a `mysql_*`.
- Se creó un adaptador común `db_*` construido sobre `mysqli` para mantener el
  funcionamiento de los módulos heredados durante la transición.
- Se añadió `db_query_params()` para nuevas consultas preparadas.
- Configuración, autenticación, sesiones y selector de centro utilizan parámetros
  vinculados en sus operaciones nuevas.
- Se añadieron scripts repetibles para auditar compatibilidad y localizar deuda
  pendiente de `mysqli`.
- Se creó una capa temporal para funciones retiradas como `split`, `ereg`,
  `each` y el comportamiento antiguo de *magic quotes*.

### Seguridad de acceso

- El inicio de sesión utiliza consultas preparadas.
- Se añadieron tokens CSRF en operaciones sensibles nuevas.
- La sesión se regenera después del acceso correcto.
- Los mensajes de error del login ya no revelan si existe un usuario concreto.
- Se endurecieron las cookies de sesión y las cabeceras HTTP de seguridad.
- La entrada y salida del personal se limita al usuario de la sesión.
- Se preparó la conversión progresiva de contraseñas históricas a
  `password_hash()` después de un acceso correcto, siempre que la columna de
  base de datos tenga longitud suficiente.

### Preparación para MySQL 8.4

- Se creó una auditoría previa para fechas cero, motores, claves e intercalaciones.
- Se prepararon migraciones para ampliar el campo de contraseña y para añadir el
  estado civil del alumno.
- Se documentó un procedimiento seguro mediante copia lógica, servidor de prueba,
  validación funcional y plan de vuelta atrás.
- La base de datos real no debe convertirse directamente sin completar ese ensayo.

## 3. Nueva estructura de la gestión del personal

- El `index.php` principal se convirtió en un controlador pequeño y ordenado.
- Se creó una nueva portada con cabecera, menú lateral, contenido y pie comunes.
- Después de iniciar sesión es obligatorio seleccionar un centro antes de ver o
  utilizar las opciones de gestión.
- Una vez elegido el centro se muestran todos los módulos permitidos para el rol.
- Las coordinadoras y usuarios multicentro pueden cambiar de centro desde la
  cabecera sin volver a iniciar sesión.
- Cada opción del menú lateral abre ahora la pantalla que reúne todas las funciones
  de ese módulo.
- Se añadió un buscador global permanente para localizar rápidamente alumnos,
  servicios y opciones del programa.
- La navegación indica visualmente qué módulo está abierto.
- Se eliminó el bloque residual con una X y el texto “Título” que aparecía al final
  de algunos menús.

## 4. Diseño adaptable y accesibilidad

- Se creó un tema administrativo común, aplicado inicialmente a 712 vistas:
  378 pantallas que usan cabeceras compartidas y 334 páginas independientes.
- El contenido utiliza todo el ancho disponible en ordenador.
- En móvil y tablet la navegación se transforma en un menú cómodo, con controles
  táctiles y sin necesidad de ampliar la página.
- Las tablas anchas conservan todos sus datos y disponen de desplazamiento interno.
- Los formularios se apilan en pantallas pequeñas para evitar campos cortados.
- Los listados se reorganizan con tipografía, espacios, tarjetas y botones más
  grandes y legibles.
- Las pantallas de alumnos, aulas, facturación, comedor, almacén, incidencias y
  personal recibieron portadas coherentes con el nuevo estilo.
- Se corrigió la separación entre cabecera y contenido que dejaba media pantalla
  vacía en algunos módulos.
- La impresión mantiene un estilo blanco específico para no imprimir la navegación.

## 5. Permisos y funciones por rol

- Las educadoras recuperaron el acceso a la información de sus aulas asignadas.
- Desde su perfil pueden consultar aula, alumnado, asistencia, comedor y menús,
  según los permisos definidos.
- Coordinación dispone de cambio de centro cuando corresponde.
- La opción de imprimir menús está disponible desde Comedor para los perfiles que
  necesitan consultarla, aunque no administren todos los datos de cocina.
- El buscador global respeta el centro activo y limita el alumnado de una educadora
  a sus aulas.

## 6. Alumnos y matrículas

- Se rediseñó la portada de Gestión de alumnos.
- Se añadió un listado simplificado con nombre, apellidos, aula, fecha de nacimiento,
  piscina, comedor y fechas de inicio y fin del curso.
- El listado de alumnado de un aula volvió al formato de una fila por alumno y
  aprovecha todo el ancho de la pantalla.
- Se conservaron todos los datos y acciones del listado completo mediante una tabla
  desplazable en pantallas pequeñas.
- Se corrigió el paso 2 de edición para que los nombres de familiares no aparezcan
  cortados.
- En el paso 1 se añadió el estado civil con las opciones solicitadas: CASADO/A,
  DIVORCIADO/A, PAREJA DE ECHO, VIUDO/A y SEPARADO/A.
- Al crear un nuevo familiar en el paso 2 se precargan los datos de contacto que ya
  constan en la ficha del alumno.
- En el paso 3 el primer y segundo contacto se proponen desde los familiares creados,
  incluyendo sus teléfonos.
- Los selectores siguen permitiendo corregir manualmente esos contactos antes de
  guardar.

## 7. Aulas, asistencia y agenda diaria

- Se renovaron los menús de Aulas y las pantallas para educadoras.
- El listado de aulas y el listado de alumnado utilizan todo el ancho disponible.
- La agenda diaria conserva las pestañas de alumnos, comida, salud, cambios,
  anotaciones, actividades y programación.
- Se añadió en Comida el botón “Quitar merienda a toda el aula” para un día concreto.
- La misma acción permite restaurar la merienda si se ha pulsado por error.
- El cambio queda asociado a centro, aula y fecha, con validación de sesión,
  permisos y CSRF.
- Las previsiones de comedor, preparación de platos, compras, albaranes y control
  de temperaturas descuentan esa merienda para que cocina no la prepare.

## 8. Comedor, cocina y menús

- Se creó una portada específica de Comedor con las funciones de cocina agrupadas.
- Menús, asistencia, preparación de platos y demás controles relacionados quedan
  accesibles desde un único módulo.
- “Menús del mes” se trasladó dentro de Comedor.
- La impresión de menús aprovecha mejor la pantalla.
- Se preparó una hoja de impresión horizontal para que el calendario mensual quepa
  en una sola página siempre que el contenido lo permita.
- En la agenda de familias se ocultaron los eventos repetidos “Piscina cerrada” de
  sábados y domingos porque dificultaban la lectura.

## 9. Facturación

- Se creó una portada moderna y adaptable para todas las opciones de facturación.
- Los listados anchos de facturas, caja, recibos, servicios e informes utilizan la
  capa responsive común.
- El nuevo flujo de cambios de material se integra con facturación: registra en
  negativo el artículo devuelto y genera el cobro del artículo nuevo.
- Se mantuvieron los procesos históricos de recibos y remesas, pendientes de una
  revisión funcional individual antes de la migración definitiva del servidor.

## 10. Personal y usuarios de gestión

- Gestión de personal y usuarios se rediseñó a pantalla completa.
- Se corrigió la composición del listado para que los nombres disponibles no queden
  ocultos por el diseño.
- Se muestran tipo de usuario, centro, contacto, usuario, alta y acciones en una
  estructura más legible.
- Se añadió una columna de contraseña actual y controles para mostrar u ocultar las
  contraseñas del listado.
- Las contraseñas continúan siendo un dato especialmente sensible y solo deben
  consultarse desde perfiles administrativos autorizados.

## 11. Administración y personalización

- Se eliminó del menú de Administración la opción Gestión de cámaras, que deja de
  utilizarse.
- Se añadió el acceso a Gabinete psicopedagógico.
- Se creó una portada del Gabinete con aulas, seguimiento, pruebas Brunet-Lézine,
  Brunet ampliado, McCarthy, comparativas, historial educativo, informes y documentos.
- Se añadió Personalización del programa para cada usuario.
- Cada persona puede elegir el color principal de la interfaz.
- Se puede elegir entre Vista clásica y Vista con análisis.
- La vista con análisis muestra alumnado del centro, asistencia del día, alumnado de
  comedor y número de aulas.
- Estos datos se actualizan automáticamente cada 30 segundos mientras la pantalla
  está visible.

## 12. Informes personalizados

- Se creó un constructor de informes para Administración.
- Permite elegir una tabla principal y añadir tablas relacionadas autorizadas.
- Permite seleccionar las columnas que aparecerán en el resultado.
- Admite filtros, ordenación y límite de filas.
- Incluye una previsualización antes de guardar.
- Cada informe se guarda con nombre y descripción.
- Existe un apartado independiente de Informes guardados.
- Al volver a ejecutar un informe se conserva su configuración, pero se consultan
  los datos actuales.
- Los resultados pueden exportarse a Excel.
- El constructor utiliza un mapa de tablas y relaciones permitido por el programa;
  no expone escritura SQL libre al usuario.

## 13. Inscripciones web y contactos interesados

- Se conectaron dos procesos que antes obligaban a copiar datos manualmente.
- En Inscripciones web se añadió “Pasar a interesado”.
- La acción copia al módulo Contactos interesados la información disponible en la
  solicitud web.
- En Contactos interesados se mantuvo el alta manual para familias que visitan o
  llaman directamente al centro.
- Se añadió una acción para inscribir al alumno desde el contacto interesado.
- El flujo permite seguir una solicitud desde la web, completar gestiones y terminar
  en matrícula sin volver a escribir todos los datos.

## 14. Gestión de material, uniformes y stock

- Gestión de material se rediseñó como un catálogo visual similar a una tienda.
- Cada artículo dispone de imagen, nombre, familia, talla, existencias y selector de
  cantidad.
- Material didáctico utiliza la imagen corporativa de Lápices.
- Baberos, chándales, mochila y uniformes de verano de niño y niña utilizan las
  fotografías entregadas.
- Todas las tallas de un mismo modelo reutilizan su fotografía correspondiente.
- “Macuto Lápices” utiliza la fotografía de la mochila.
- Los artículos tachados en el PDF facilitado se retiraron del catálogo activo y de
  los informes de material mediante un registro de productos retirados.
- Se añadió una estimación de pedido por curso basada en número de alumnos, aula,
  talla y sexo cuando corresponde.
- La cantidad recomendada se muestra junto a la cantidad editable; el usuario puede
  pedir cualquier número.
- Se añadió un PDF de pedido desglosado con los artículos marcados.
- La generación está protegida contra duplicados mediante un token de petición.
- Al confirmar el pedido se registran sus líneas y las entradas de stock asociadas.
- Se rehízo Control de entregas para seleccionar alumno, artículo y unidades desde
  el stock disponible.
- La entrega puede pasar a facturación.
- Se añadió el cambio de artículo o talla después del cobro, con devolución negativa
  y cobro automático del reemplazo.

## 15. Aplicación de familias

- Se creó una nueva pantalla de acceso móvil con el logotipo y la identidad de
  Lápices.
- Se corrigió su altura y composición para evitar textos, logotipo o formulario
  superpuestos en teléfonos pequeños.
- Toda la zona `/app` recibió una estructura visual actualizada y pensada primero
  para móvil.
- Se creó una cabecera compacta y una navegación inferior tipo aplicación.
- Se añadió un botón visible para cerrar la sesión dentro del programa.
- El listado de mensajes se adaptó para que asunto, estado, fecha y botón de apertura
  se vean correctamente.
- Las conversaciones se presentan con un aspecto tipo WhatsApp, separando mensajes
  enviados y recibidos.
- Nuevo mensaje se rediseñó y permite elegir correctamente entre los destinatarios
  que el centro ofrece a la familia, como educadora o coordinación.
- La agenda se transformó en una vista cómoda para móvil, con navegación por fechas,
  tarjetas de eventos y alternativa de escritorio.
- Se eliminaron de la agenda móvil los avisos repetidos de piscina cerrada en fin de
  semana.
- Documentos y el resto de pantallas utilizan el mismo lenguaje visual.

## 16. Nuevo sistema de ayuda para trabajadores

- Se añadió un botón “Ayuda” siempre visible en las cabeceras modernas y heredadas.
- La tecla F1 abre la misma ayuda contextual.
- La ayuda detecta la pantalla actual y muestra instrucciones numeradas, advertencias
  y accesos directos relacionados.
- Se creó un Centro de ayuda completo, buscable por palabras sencillas y organizado
  por tareas, no por nombres técnicos.
- La letra, los botones y los espacios se han dimensionado para facilitar el uso a
  personas con poca experiencia informática.
- El centro incluye una lista de cinco comprobaciones para evitar los errores más
  frecuentes: centro, curso/aula/fecha, búsqueda previa, guardado único y cierre de
  sesión.
- En móvil la ayuda se abre como una hoja inferior grande y manejable con el dedo.

## 17. Aspectos que todavía requieren un plan de migración

La aplicación funciona sobre `mysqli`, pero la antigüedad y el tamaño del sistema
hacen que la modernización técnica no deba considerarse cerrada únicamente por
haber sustituido `mysql_*`.

Queda como deuda controlada:

- Revisar las aproximadamente 12.747 consultas heredadas `db_query()` y convertir
  las que reciben datos variables a consultas preparadas.
- Tipar y validar todos los accesos directos antiguos a `$_GET` y `$_POST`.
- Sustituir usos antiguos de fechas y consultas que dependen del modo SQL permisivo.
- Migrar gradualmente texto, tablas y conexiones a `utf8mb4`.
- Añadir pruebas funcionales sistemáticas por módulo y por rol.
- Sacar las credenciales de base de datos del código y cargarlas desde variables de
  entorno.
- Actualizar o retirar bibliotecas antiguas de PDF, correo, foro y webcam.
- Ensayar MySQL 8.4 en un servidor separado con una copia anonimizada antes del
  cambio de producción.

## 18. Comprobaciones recomendadas antes de publicar

1. Probar inicio de sesión y cierre de sesión con cada rol.
2. Comprobar selección y cambio de centro.
3. Probar en ordenador, tablet y un móvil real.
4. Dar de alta un alumno de prueba y recorrer los cuatro pasos.
5. Registrar asistencia, comedor y quitar/restaurar merienda de un aula de prueba.
6. Generar e imprimir un menú mensual.
7. Crear un informe personalizado, guardarlo y exportarlo.
8. Pasar una inscripción web de prueba a interesado y después a matrícula.
9. Crear un pedido de material de prueba, comprobar el PDF y verificar el stock.
10. Realizar una entrega, un cobro y un cambio de artículo de prueba.
11. Acceder como familia desde un móvil y comprobar mensajes, agenda, documentos y
    cierre de sesión.
12. Abrir Ayuda desde cada módulo principal y comprobar que muestra la guía correcta.

## 19. Procedimiento recomendado para formar al personal

- Primera sesión de 30 minutos: inicio, selección de centro, buscador y botón Ayuda.
- Segunda sesión por perfiles: cada grupo practica únicamente las tareas que realiza.
- Utilizar alumnos y datos de prueba durante la formación.
- Entregar una ficha de una página con las cinco comprobaciones básicas.
- Pedir a cada trabajadora que complete una tarea real acompañada y otra sin ayuda.
- Recoger las dudas que se repitan y añadirlas al Centro de ayuda contextual.

## 20. Centro de mensajes del personal

- La mensajería de gestión se convirtió en un centro de conversaciones con un
  funcionamiento parecido a WhatsApp Web.
- El personal puede elegir un aula concreta o ver todas las aulas a las que tiene
  acceso.
- Se muestran todos los alumnos activos con sus familiares vinculados, aunque aún
  no exista ningún mensaje, para poder iniciar una conversación sin buscar otra
  pantalla.
- Las conversaciones se ordenan por la fecha del último mensaje; las pendientes de
  leer aparecen destacadas con su contador y las vacías quedan después ordenadas
  alfabéticamente.
- El historial utiliza burbujas diferenciadas para familia y escuela, separadores
  por día y un cuadro de respuesta fijo en la parte inferior.
- La cabecera incluye una campana de notificaciones junto al nombre del trabajador.
  Su desplegable muestra las conversaciones pendientes y permite abrir cada chat
  directamente.
- Educadoras y coordinadoras reciben un aviso emergente cuando hay novedades. Las
  educadoras solo ven sus aulas asignadas; coordinación y administración pueden
  consultar las aulas del centro activo.
- La pantalla se adapta a ordenador, tableta y móvil. En teléfonos se comporta como
  una aplicación: primero muestra la lista y después el chat, con una flecha para
  regresar.
- La ayuda contextual explica el selector de aula, los avisos, la búsqueda y el
  envío de respuestas.

## 21. Paginación y finales de página compactos

- Los listados largos incorporan un selector común de 20, 50 u 80 filas por página.
- Al final de cada tabla aparecen el intervalo visible, el total de registros y
  botones numerados para avanzar o retroceder.
- Buscar alumno y Usuarios de gestión utilizan paginación real en MySQL mediante
  `LIMIT` y `OFFSET`; el servidor solo obtiene las filas de la página solicitada.
- Las tablas heredadas reciben paginación automática y conservan encabezados,
  totales y acciones fuera de las filas ocultas.
- La preferencia de filas se recuerda en el navegador para los siguientes
  listados.
- Las tablas que se incorporan dinámicamente también se detectan y paginan.
- Se retiró la antigua capa flotante `capa1`, que generaba un bloque vacío de una
  pantalla completa bajo numerosos módulos.
- Se eliminaron alturas mínimas innecesarias y se redujo el margen inferior del
  panel; el contenido termina con un espacio visual breve en lugar de una gran
  zona vacía.
