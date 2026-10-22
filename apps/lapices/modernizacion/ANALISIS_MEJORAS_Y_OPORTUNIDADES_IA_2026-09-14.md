**Análisis de mejoras y oportunidades de IA — Gestión Lápices**

Fecha: 14 de septiembre de 2026. Prioridad indicada por el usuario: administración, facturación e informes.

Mi recomendación es empezar con un asistente interno de informes y un panel de comprobación de facturación. Después incorporaría lectura de facturas de proveedores y, para las familias, explicación de recibos y ayuda con trámites. Son propuestas apoyadas en módulos que ya existen y en tareas repetitivas concretas.

**1. Alcance y fiabilidad del análisis**

He revisado la copia local de `C:\xampp\agenda.escuelasinfantileslapices.es`: documentación de modernización, configuración y rutas, motor de informes, facturación y cobro de material, gastos, mensajes de personal y familias, ayuda, acceso familiar, documentos y paginación. He ejecutado el inventario existente de llamadas de base de datos y comprobado los binarios de XAMPP y su configuración PHP.

Este es un análisis estático y de arquitectura con comprobaciones locales de inventario. No he probado operaciones con datos reales, reproducido fallos de concurrencia, recorrido la interfaz con sesiones de cada rol ni inspeccionado el servidor de producción. No he modificado el código funcional ni la base de datos. Los riesgos descritos distinguen lo observable en el código de las consecuencias que necesitan una prueba controlada.

No he leído expedientes personales ni volcado registros de familias para elaborar las propuestas. Las consultas web utilizadas son generales y no incluyen datos del programa.

Resultado del script existente `modernizacion/auditar_mysqli.php`:

| Medida del inventario local | Resultado |
|---|---:|
| Archivos PHP escaneados | 2.747 |
| Referencias ejecutables a la API retirada `mysql_*` detectadas | 0 |
| Referencias a `db_query` | 12.713 en 1.507 archivos |
| Referencias a `db_query_params` | 72 en 30 archivos |
| Referencias a `mysqli_prepare` | 74 |

El script cuenta identificadores PHP mediante tokens y tiene sus propias exclusiones. Incluye código conservado y dependencias; no representa 2.747 pantallas activas ni 12.713 vulnerabilidades. Sirve para medir la magnitud de la capa heredada. No es una prueba completa de seguridad ni de compatibilidad funcional.

Los documentos anteriores contienen estados distintos de la migración. Por ejemplo, el manual de septiembre todavía describe mostrar contraseñas, mientras que el informe posterior documenta su retirada y migración a hashes. He comprobado que los accesos actuales utilizan `password_verify`; no he repetido el recuento de credenciales en la base de datos. Conviene consolidar esa documentación antes de convertirla en fuente de respuestas de IA.

**2. Lo que ya tiene valor y conviene aprovechar**

El programa reúne matrículas y familiares, centros y aulas, asistencia, agenda educativa, comedor, material, almacén, facturación, gastos, personal, comunicaciones y gabinete. Esa integración permite resolver preguntas de negocio que un chatbot aislado no podría contestar con fiabilidad.

Hay una base reutilizable clara:

- Controladores y funciones comunes de autenticación, sesión, CSRF y consultas preparadas.
- Un motor de informes que recibe una definición estructurada, valida campos y operadores, prepara consultas y exporta resultados.
- Una capa de mensajes de personal que aplica filtros de centro y aula antes de devolver conversaciones.
- Un catálogo de ayuda con tareas, pasos y enlaces.
- Un almacén general que utiliza transacciones y bloqueo de existencias: es un buen patrón para otras operaciones críticas.

La modernización ha avanzado en interfaz, compatibilidad y varios servicios internos. El siguiente salto útil es consolidar reglas de negocio, permisos y datos para que todos los módulos compartan el mismo comportamiento.

**3. Mejoras del programa que priorizaría**

| Prioridad | Mejora | Evidencia y consecuencia práctica |
|---|---|---|
| Alta | Hacer indivisible el cobro y el cambio de material | La creación de factura, pago y actualización de entrega no está envuelta en una transacción común en los flujos revisados. Puede quedar una operación parcial. |
| Alta | Proteger numeración y reintentos de cobro | Se calcula el siguiente número con `MAX(...) + 1`. Dos peticiones simultáneas pueden competir; un segundo clic tampoco debe crear otra factura. |
| Alta | Restringir el catálogo que podrá consultar la IA | El motor de informes enumera tablas y columnas de la base de datos. Antes de usarlo con IA necesita una selección explícita de datos y relaciones de negocio. |
| Alta | Completar consultas preparadas y autorización por registro | La búsqueda de mensajes de familias sigue componiendo SQL con texto recibido del navegador. Es un caso concreto para corregir, además de revisar facturación y gastos. |
| Alta para despliegue | Terminar la migración del entorno | Los binarios de XAMPP comprobados son PHP 5.6.40 y MariaDB 10.1.38; su configuración Apache carga PHP 5. Existe también un PHP 8.5 de pruebas separado. |
| Media | Unificar texto, fechas y significados de los datos | Conviven conexiones `latin1` y `utf8mb4`, y distintos identificadores de persona y matrícula. Afecta a búsquedas, documentos e informes. |
| Media | Paginar y actualizar desde el servidor | La paginación visual de tablas heredadas oculta filas ya cargadas. El centro de mensajes recupera historiales completos y refresca periódicamente. |
| Media | Consolidar documentos, pruebas y despliegues | Hay controles de carga dispersos y copias antiguas. No he localizado una suite propia de pruebas de negocio ni un repositorio Git en esta copia. |

Detalle de los hallazgos más relevantes para administración:

**Cobros y pagos.** En `material_create_invoice`, si falla la inserción en `pagos_a_cuenta`, la función conserva `id_pago=0` y devuelve `ok=true`. El flujo de cobro puede marcar la entrega como cobrada y mostrar éxito. Es un fallo de propagación de errores visible en el código; no significa que se haya producido ya con datos reales. Debe fallar la operación completa o quedar explícitamente pendiente de cobro, según la regla acordada.

**Concurrencia.** `material_charge_delivery` comprueba primero que no exista factura, la crea y después actualiza la entrega con `WHERE id_factura=0`, sin comprobar las filas afectadas. Dos peticiones simultáneas podrían crear dos facturas aunque solo una termine vinculada. La numeración también necesita una reserva atómica y una restricción de unicidad acorde a las series y ejercicios del negocio. Antes de añadir transacciones hay que verificar que todas las tablas implicadas usan un motor que las soporte.

**Cambios de material.** El código contempla expresamente una salida de error en la que la factura ya está creada, pero falla el movimiento de stock. Recomiendo reunir factura, pago, devolución, nueva entrega y stock en una operación consistente, con reintentos que reutilicen el resultado anterior. El Almacén general ya ofrece una referencia local con `FOR UPDATE` y rollback.

**Informes fiables.** Hay que fijar las definiciones de «facturado», «cobrado», «pendiente», «abono», «alumno activo» y «curso». No se deben sumar facturas después de unir directamente todas sus líneas y todos sus pagos: esas relaciones pueden multiplicar filas e importes. Cada magnitud debe agregarse primero a su nivel correcto. También hay que distinguir `usuarios.id_usuario`, `alumnos.id_alumno` y las referencias históricas llamadas `id_alumno` que apuntan a una persona.

**Catálogo de informes.** El acceso actual está limitado a perfiles administrativos, pero la selección de tablas y columnas procede de `information_schema`. La comprobación de que un campo existe no equivale a autorizar su uso por una IA. El filtro de centro es una opción de la definición, no una restricción obligatoria añadida por el motor. Para el asistente habrá que excluir credenciales, tokens, documentos identificativos y datos clínicos, e imponer el ámbito permitido desde el servidor. Los informes globales entre centros deben tener un permiso explícito.

**Búsqueda de familias.** En `app/mensajeria.php`, `q` se concatena en condiciones `LIKE` e `i` en `LIMIT`. La sesión y la comprobación de origen no sustituyen la parametrización. No he ejecutado intentos de explotación; la construcción insegura está visible en la ruta actual.

**Entorno.** PHP 5.6 y MariaDB 10.1 están fuera de soporte según sus proyectos oficiales. Lo comprobado corresponde al XAMPP local y su configuración; no acredita la versión efectiva del servidor público. La migración debe ensayarse con restauración y recorridos funcionales antes del cambio. [PHP: ramas sin soporte](https://www.php.net/eol.php), [MariaDB: política de mantenimiento](https://mariadb.org/about/).

**Documentos.** La carga del gabinete revisada comprueba extensiones y construye rutas con parámetros y nombres originales. Conviene centralizar límites, validación real del formato, identificación del documento y permisos de descarga. Para OCR empezaría por facturas de proveedores en un flujo nuevo controlado, dejando fuera los expedientes del gabinete.

Mejoras de uso que complementan estas correcciones:

- Una bandeja de administración con facturas por revisar, diferencias pendientes, documentos incompletos y enlaces a la acción correspondiente.
- Una vista previa de cierre que explique qué se va a facturar y por qué, con comparación respecto al periodo anterior.
- Filtros guardados por usuario y centro, exportación coherente con los filtros y fecha visible de actualización.
- Estados claros: borrador, revisado, emitido, cobrado, devuelto y anulado, ajustados a las reglas reales del programa.
- Historial de cambios para importes, descuentos, servicios y cuentas, con autor y motivo; una copia de seguridad comprobada mediante restauración.
- Pruebas propias de doble clic, pago parcial, devolución, descuento, redondeo, numeración y acceso de una familia a datos de otra.

**4. Oportunidades de IA ordenadas según vuestra prioridad**

El esfuerzo es relativo y corresponde a un piloto acotado, no a todo el producto. Su orden puede cambiar tras medir el volumen real de tareas.

| Orden | Función | Ejemplo de uso | Beneficio esperado | Esfuerzo / condición |
|---|---|---|---|---|
| 1 | Informes con lenguaje natural | «Compara lo facturado y cobrado de este centro entre dos meses» | Menos tiempo buscando pantallas y preparando Excel | Medio; catálogo y métricas autorizadas |
| 2 | Revisión previa a facturar | «Muéstrame qué tengo que revisar antes del cierre» | Concentrar la atención en diferencias y errores | Medio; reglas exactas antes del texto de IA |
| 3 | Lectura de facturas de proveedores | Subir PDF y recibir campos propuestos para gastos | Reducir tecleo y clasificación manual | Medio; validar importes y proveedor |
| 4 | Resumen de gestión | Explicar cambios de facturación, gastos y ocupación | Preparar reuniones y detectar qué revisar | Medio; datos agregados consistentes |
| 5 | Ayuda interna conversacional | «¿Cómo tramito una devolución de material?» | Menos interrupciones y formación más rápida | Bajo; actualizar el catálogo de ayuda |
| 6 | Explicar recibos a familias | «¿Por qué este mes aparece un importe distinto?» | Reducir consultas a administración | Medio; recibos propios y desglose fiable |
| 7 | Ayuda con trámites de familias | Explicar documentos requeridos y dónde entregarlos | Menos consultas repetidas y solicitudes incompletas | Bajo/medio; instrucciones vigentes por centro y curso |
| 8 | Borradores y traducción de mensajes | Preparar una respuesta clara sobre un trámite | Acelerar comunicaciones frecuentes | Bajo/medio; revisión de la persona que envía |
| 9 | Resumen de agenda | Redactar un resumen a partir de registros aprobados | Ahorrar redacción a educadoras y facilitar lectura | Medio; datos disponibles y publicados |
| 10 | Previsión de compras y consumos | Proponer cantidades usando matrícula, stock y consumo | Mejorar planificación de material y cocina | Medio/alto; historial consistente y comparación con reglas actuales |

**5. Primer proyecto propuesto: asistente de informes**

Lo incorporaría dentro del módulo Informes, con una entrada «Describe el informe que necesitas». Utilizaría el motor actual como punto de partida, añadiendo una capa de permisos y definiciones de negocio.

Flujo propuesto:

1. La persona escribe la petición.
2. El sistema identifica centro, curso, fechas y tipo de informe. Si «ingresos» puede significar facturado o cobrado, solicita esa precisión.
3. La IA propone una definición estructurada perteneciente a un catálogo autorizado.
4. El servidor valida permisos, campos, relaciones, periodo y límites; calcula los importes mediante código y consultas preparadas.
5. Se muestra la tabla con sus filtros y un resumen breve. Cada afirmación cuantitativa se puede contrastar con el resultado.
6. El usuario guarda o exporta mediante las funciones del programa.

Comenzaría con cinco informes: facturado por periodo y centro; cobros registrados por fecha; saldo pendiente a una fecha de corte; gastos por partida/proveedor; comparación de material entregado y cobrado. Cada uno necesita definición validada y casos de referencia. El saldo histórico puede exigir información adicional si el programa solo conserva estados actuales de determinadas operaciones.

La primera versión puede producir la configuración del informe sin recibir sus filas personales. El servidor ejecuta y muestra el resultado. Si se añade explicación, puede recibir únicamente los agregados necesarios. Así se reduce la información expuesta al proveedor.

Las sumas, descuentos, saldos y criterios de inclusión corresponden al programa. La IA aporta comprensión de la petición y explicación. No ejecutaría SQL libre generado por el modelo ni permitiría que este eligiera cualquier tabla del esquema.

**6. Segundo proyecto: revisión de facturación**

Crearía una pantalla de incidencias antes de emitir, empezando por reglas configuradas:

- Posibles duplicados según una clave de negocio definida para cada servicio; no basta con repetir importe.
- Entregas marcadas como cobradas sin pago asociado, considerando excepciones válidas como operaciones de importe cero.
- Diferencias entre totales y detalle conforme a las reglas actuales de descuentos, impuestos y redondeo.
- Servicios fuera del periodo contratado, datos necesarios incompletos y descuentos que requieren revisión.
- Cambios significativos respecto a un periodo comparable, con umbral configurable y posibilidad de descartarlos como correctos.

La IA convertiría esos resultados en explicaciones y agruparía motivos: «Estas diferencias corresponden a nuevos servicios; estas otras necesitan comprobarse». Una variación no prueba un error ni su causa. Cada aviso debe enlazar a sus líneas y datos de origen.

Los controles de duplicados, importes y fechas pueden aportar valor antes de incorporar IA. La emisión, anulación, cobro y remesas seguirían pasando por las acciones explícitas del usuario y las reglas del programa.

**7. Tercer proyecto: facturas de proveedores y documentación administrativa**

Los módulos de gastos ya manejan proveedor, número de factura, fechas, importe, forma de pago y asignación a centros. Un lector de PDF o imagen puede preparar esos campos y sugerir una partida contable.

El flujo sería: documento → extracción → propuesta editable junto al original → validaciones → confirmación → alta mediante un servicio de gastos. Las fechas, importes y correspondencia del proveedor se verifican; los campos ausentes se dejan pendientes. Se conserva el original y se detectan posibles duplicados mediante proveedor y referencia normalizada, considerando documentos rectificativos y excepciones.

Antes de guardar desde este asistente conviene renovar el controlador heredado de gastos. No conectaría directamente la extracción a `guarda_coste.php`, que todavía recoge y utiliza numerosos parámetros sin una capa moderna de validación.

La conciliación con movimientos bancarios sería una ampliación posterior. Requiere confirmar que disponéis de una importación fiable y de referencias para relacionar pagos y facturas; no he acreditado ese circuito en esta revisión.

**8. IA en la aplicación de familias**

Priorizaría funciones que también descarguen trabajo de administración:

- **Explicación de recibos.** En la pantalla del recibo, «Explicar este recibo» muestra sus conceptos y diferencias con otro periodo. Siempre parte de documentos y pagos autorizados para esa familia. Si el sistema no conserva el motivo de un ajuste, debe decirlo y ofrecer contacto con administración.
- **Ayuda con trámites.** Respuestas basadas en normas publicadas del centro y curso: dónde consultar documentación, cómo comunicar una ausencia o cómo solicitar un cambio. Un estado personal de trámite solo se responde si existe registrado y se puede consultar con autorización.
- **Mensajes más claros y traducción.** Conservar el original, señalar la traducción y permitir contactar con una persona. En comunicaciones sensibles hay que preservar literalmente los datos críticos y mantener revisión humana.
- **Resumen de agenda.** Generarlo a partir de notas ya aprobadas para familias, con fechas y enlaces. No inferir que un niño comió, durmió o realizó una actividad porque falte un registro; indicar que no consta.

Para las familias, el asistente debe identificarse como IA y ofrecer una salida clara hacia el personal. Sus respuestas personales se limitarían a hijos vinculados y a datos que esa familia puede ver en ese momento. La autorización se vuelve a comprobar en cada consulta, incluso al descargar archivos o reutilizar una respuesta guardada.

Evitaría como primera fase diagnósticos o valoraciones automáticas del desarrollo, recomendaciones de medicación, reconocimiento de menores en fotografías y decisiones sobre admisión o prioridad de familias. El gabinete podría beneficiarse más adelante de ayuda de redacción sobre observaciones seleccionadas por profesionales, con revisión y un análisis específico del tratamiento de esos datos.

**9. Integración técnica propuesta**

Se puede añadir una capa de IA al programa PHP por funciones concretas. No hace falta sustituir todo el sistema ni entrenar un modelo con la base de datos.

Secuencia: pantalla actual → endpoint autenticado → autorización y selección mínima de datos → proveedor de IA → validación de respuesta → borrador o resultado con fuentes.

Separaría dos tipos de información:

- Documentación: buscar fragmentos vigentes de ayuda, procedimientos y normas; dar al modelo solo los relevantes y enlazarlos en la respuesta. Esta técnica de recuperación de fuentes se conoce como RAG. La documentación técnica de Microsoft describe el filtrado por autorización antes de recuperar información; aquí se adaptaría a centros, aulas y familias, sin implicar una elección de proveedor. [Arquitectura de recuperación con permisos](https://learn.microsoft.com/en-us/azure/architecture/ai-ml/guide/secure-multitenant-rag).
- Datos operativos: obtenerlos mediante funciones cerradas como «consultar resumen de facturación» o «consultar recibo autorizado». Los datos actuales se consultan al pedirlos, con fecha de corte, en lugar de entrenar el modelo con históricos que envejecen.

Requisitos de esa capa:

- Credencial del proveedor únicamente en el servidor; permisos determinados por la sesión y el programa.
- Respuestas estructuradas, límites de tamaño, validación de campos y renderizado de texto seguro.
- Presupuesto por función y centro, límites de peticiones, tiempo máximo y opción de desactivar una función sin impedir el trabajo habitual.
- Registro de versión del modelo, configuración, referencias de origen, tiempos, consumo y aceptación/correcciones. Evitar guardar expedientes completos en logs.
- Procesos en segundo plano para documentos grandes o resúmenes extensos. Un fallo del proveedor debe dejar disponible el flujo manual.
- Versionado de documentos, permisos y respuestas guardadas; separación de cachés para impedir reutilizar una respuesta privada entre familias o centros.

Los mensajes y documentos son contenido no confiable: pueden incluir instrucciones que intenten cambiar el comportamiento del modelo. Por eso la seguridad no puede depender solo de decirle «no reveles datos». El servidor debe limitar datos y acciones, y validar toda petición generada. OWASP documenta que la inyección de instrucciones también afecta a sistemas que recuperan documentos. [OWASP: prompt injection](https://genai.owasp.org/llmrisk/llm01-prompt-injection/).

Antes de procesar datos personales con un proveedor hay que concretar finalidad, base jurídica, contrato, conservación, ubicación y transferencias, e informar adecuadamente. Por el contexto de menores y los datos de salud, revisaría con vuestro responsable o delegado de protección de datos si cada caso requiere una evaluación de impacto. El uso de IA no permite dar por resuelto ese análisis con un consentimiento genérico. [AEPD: adecuación de tratamientos con IA](https://www.aepd.es/sites/default/files/2020-02/adecuacion-rgpd-ia.pdf), [AEPD: evaluación de impacto](https://www.aepd.es/preguntas-frecuentes/2-tus-obligaciones-como-responsable-del-tratamiento/10-evaluacion-de-impacto).

No seleccionaría todavía un proveedor ni prometería una cuota mensual. Compararía las opciones con el mismo conjunto de ejemplos y sus condiciones de tratamiento de datos. Para un primer piloto, una API gestionada puede simplificar la operación; alojar el modelo requiere valorar recursos, mantenimiento y calidad con esos ejemplos.

**10. Plan para decidir con resultados**

| Etapa | Entrega | Criterio para avanzar |
|---|---|---|
| Preparación | Cinco métricas definidas, catálogo autorizado, ejemplos y revisión de cobros | Resultados contrastados con administración y permisos probados |
| Piloto de informes | Preguntas sobre cinco informes, filtros visibles y exportación | Cifras exactas en los casos de referencia y ahorro medido |
| Revisión de facturación | Bandeja de comprobaciones y explicación de diferencias | Avisos útiles, trazables y con pocos falsos positivos |
| Lectura de documentos | Propuestas de campos para facturas de proveedores | Menos tiempo total incluyendo correcciones |
| Piloto para familias | Explicación de recibos y ayuda de trámites | Respuestas verificables, aislamiento entre familias y derivación correcta |

Empezaría con una o dos personas de administración y un centro. Antes del desarrollo, recogería 20–30 preguntas habituales de informes y ejemplos de documentos anonimizados o sintéticos. Mediría durante una semana cuánto tardan ahora. Después observaría el piloto durante dos semanas de uso real controlado. Son ventanas de evaluación propuestas, no un compromiso de plazo de desarrollo.

Indicadores: minutos por informe, porcentaje de filtros acertados, concordancia de importes con el cálculo de referencia, respuestas sin fundamento, campos OCR corregidos, coste por tarea completada y consultas familiares que requieren intervención. Los casos de acceso cruzado deben quedar bloqueados en todas las pruebas; eso no sustituye la revisión continua de permisos.

Ejemplo de rentabilidad exclusivamente ilustrativo: si dos personas ahorran 30 minutos diarios durante 20 días, se recuperan 20 horas al mes. Hay que descontar revisión y mantenimiento. El coste total incluye integración, consumo de IA/OCR, almacenamiento y soporte. Falta conocer el volumen mensual de informes y documentos para estimarlo con rigor.

El primer entregable que elegiría es «Informes asistidos para administración», limitado a consultas y exportaciones, acompañado por la corrección de los fallos de cobro identificados. El siguiente sería «Revisión previa a facturar». La primera función para padres sería «Explicar mi recibo», aprovechando las mismas definiciones y comprobaciones internas.

**11. Referencias de código para continuar el trabajo**

Los enlaces apuntan a la copia local revisada. Las líneas son referencias de esta fecha.

| Referencia | Qué acredita o permite reutilizar |
|---|---|
| [Motor de informes](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/informes/report_engine.php:69) | Enumeración de tablas y posterior catálogo de columnas |
| [Filtros de informes](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/informes/report_engine.php:348) | Centro como modo de filtro configurable |
| [Numeración de material](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/material/material_service.php:1142) | Cálculo del siguiente número de factura |
| [Resultado del pago](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/material/material_service.php:1284) | Inserción de pago y retorno posterior de éxito |
| [Cobro de entrega](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/material/material_service.php:1299) | Secuencia de creación de factura y vinculación a entrega |
| [Cambio de material](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/material/material_service.php:1358) | Secuencia de devolución, factura y stock |
| [Almacén general](C:/xampp/agenda.escuelasinfantileslapices.es/almacen/general/bootstrap.php:161) | Referencia de transacción y bloqueo de existencias |
| [Búsqueda familiar](C:/xampp/agenda.escuelasinfantileslapices.es/app/mensajeria.php:34) | Parámetros concatenados en búsqueda y paginación |
| [Recibos de familias](C:/xampp/agenda.escuelasinfantileslapices.es/app/ver_recibos.php:105) | Comprobación de vínculo familiar y consulta del estado de cuentas |
| [Mensajes del personal](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/mensajes/centro_mensajes_lib.php:300) | Selección de alumno con ámbito de centro y aula |
| [Carga de mensajes](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/mensajes/centro_mensajes_lib.php:362) | Recuperación de eventos sin paginación del historial |
| [Ayuda existente](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/ayuda/catalogo.php:4) | Base documental de tareas, pasos y enlaces |
| [Carga de documentos del gabinete](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/gabinete/upload_documento1.php:27) | Construcción de rutas y comprobación de extensiones |
| [Paginación heredada](C:/xampp/agenda.escuelasinfantileslapices.es/gestion/panel/assets/table-pagination.js:187) | Ocultación de filas ya presentes en la página |
| [Alta y edición de gastos](C:/xampp/agenda.escuelasinfantileslapices.es/gestion_gastos/guarda_coste.php:30) | Campos administrativos candidatos a extracción documental |

No se ha implementado ni activado IA en este análisis. El resultado es una propuesta priorizada y una lista de mejoras verificables para preparar esa implementación.
