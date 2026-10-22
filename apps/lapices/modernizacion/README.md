# Modernización de la aplicación Lápices

Estado de la segunda fase: 23 de julio de 2026.

## Revisión de textos y accesos del 6 de octubre de 2026

- Las plantillas propias se han normalizado a UTF-8 sin BOM, incluidas las
  cabeceras HTTP/HTML. Se han corregido textos con doble codificación.
- Las conexiones `db_*` y `app_db_connect()` usan `utf8mb4`. Las columnas antiguas
  conservan sus intercalaciones: MySQL convierte en la conexión y el adaptador
  normaliza textos históricos al leer. No se ha hecho una conversión masiva de
  datos ni de tablas. Las columnas `latin1` todavía limitan los caracteres que
  pueden almacenar; su migración física exige una copia y ensayo específicos.
- PDF con fuentes antiguas y ficheros bancarios de ancho fijo convierten a
  Windows-1252 únicamente en su salida. Las bibliotecas externas y sus fuentes
  conservan sus formatos originales. Los XML de aplicación siguen en UTF-8.
- El listado de personal distingue contraseña configurada o pendiente y ofrece
  «Gestionar acceso». El editor conserva la clave si el campo queda vacío y
  guarda las nuevas con bcrypt; valida permisos y CSRF. La consulta de copias
  cifradas asignadas por administración se detalla a continuación. No se han
  cambiado claves de cuentas reales.
- Auditoría repetible, de solo lectura: `node modernizacion/auditar_utf8.cjs`.
  Excluye bibliotecas externas y motores PDF antiguos. No comprueba datos SQL.
- Copias previas e inventario de cambios locales:
  `C:/xampp/tmp/lapices-utf8-before-20261006`.
- Comprobaciones: sintaxis de 1.296 PHP modificados con PHP 5.6.40, guardado y
  lectura de acentos, caracteres internacionales, exportación PDF, permisos de
  acceso, y regresión de avisos y chat mediante datos temporales o transacciones
  revertidas. La prueba visual del listado utiliza datos ficticios.

## Consulta de contraseñas asignadas por administración

El botón «Mostrar contraseñas» consulta las copias cifradas de las claves que
un administrador establece en `gestion/usuarios/config_cand.php`. No recupera
hashes existentes ni captura claves al iniciar sesión. Las claves anteriores
aparecen como «No disponible para consulta»; cada cambio requiere asignar una
clave nueva de forma explícita.

- Solo nivel 4, comprobado también en la base de datos en cada consulta. Se
  exige la contraseña actual del administrador, POST y CSRF. Cinco intentos
  fallidos bloquean la confirmación durante 15 minutos.
- `usuarios.pass` sigue siendo un hash de acceso. `pass_consulta` contiene una
  copia cifrada, vinculada al usuario y al hash vigente mediante MAC y huella.
  Ambos se guardan en la misma sentencia al asignar una clave. Una clave
  cambiada por otro flujo no permite revelar una copia antigua. Las claves
  nuevas asignadas por coordinadoras no generan copias consultables.
- AES-256-CBC con IV aleatorio y HMAC-SHA256 (claves independientes). Se verifica
  el MAC antes de descifrar. Compatible con PHP 5.6 y OpenSSL. Referencia de la
  API: https://www.php.net/manual/en/function.openssl-encrypt.php.
- La clave privada de 64 bytes vive fuera de la web, por defecto en
  `C:/xampp/private/lapices/password-consulta.key`. Puede configurarse mediante
  `LAPICES_PASSWORD_KEY_FILE`. Conservar una copia de seguridad protegida y
  separada de la base de datos: su pérdida impide descifrar las copias, aunque
  los usuarios podrán seguir accediendo con sus hashes. Nunca copiarla a una
  carpeta pública o al repositorio. En este servidor solo tienen acceso la
  cuenta local de trabajo, SYSTEM y Administradores.
- Preparación repetible mediante CLI:
  `php modernizacion/preparar_consulta_passwords.php`. No regenera una clave
  existente. Añade las dos columnas sin tocar los hashes y crea la tabla de
  auditoría `usuarios_password_consulta_log` (actor, fecha, evento y cantidad;
  sin claves ni hashes).
- La respuesta usa `no-store`. Las claves solo aparecen tras confirmar, se
  borran del DOM al ocultar/cambiar de pestaña y tras un minuto, y se excluyen
  de la impresión. La consulta abarca la página actual del listado.
- Validación: 26 pruebas con tablas temporales para cifrado, alteraciones,
  permisos, cambios de clave, CSRF y límites de intentos; comprobación visual
  del formulario, error, consulta y ocultación con cuentas ficticias.
  Ejecutar con `php modernizacion/tests/password_consulta.php` después de
  preparar el esquema. La prueba se detiene si no puede aislar ambas tablas.

## Objetivo técnico

- PHP 8.5 como runtime de aplicación.
- MySQL 8.4 LTS como destino de base de datos.
- `mysqli` y consultas preparadas para todo el código nuevo.
- Interfaz de gestión del personal responsive y accesible.

## Inventario auditado

- 2.679 ficheros PHP ejecutables.
- 700.823 líneas PHP.
- 270 tablas y aproximadamente 2,2 GB de datos.
- 259 tablas MyISAM y 11 InnoDB.
- Mezcla de `latin1`, `utf8` y varias intercalaciones.
- 28 columnas con fechas cero por defecto.
- 14 tablas sin clave primaria y ninguna clave foránea.
- Contraseñas históricas almacenadas sin hash en `usuarios.pass`.

## Trabajo completado en esta fase

- El `index.php` raíz actúa ahora como controlador limpio.
- Nueva portada, navegación, selector de centro, login y panel de personal.
- Login con consultas preparadas, CSRF, regeneración de sesión y mensajes
  genéricos de error.
- Registro moderno de entrada y salida del empleado, limitado al usuario de
  la sesión.
- Cabeceras de seguridad y cookies de sesión endurecidas.
- Conversión de todas las etiquetas cortas ejecutables.
- Conversión de sintaxis incompatible de offsets y `=& new`.
- Reparación de errores de sintaxis que ya existían en módulos antiguos.
- Adaptador nativo `db_*` construido exclusivamente sobre `mysqli`.
- Conversión tokenizada de 34.959 llamadas ejecutables en 1.512 ficheros:
  ya no queda ninguna llamada ejecutable a `mysql_*`.
- Soporte común para consultas preparadas con `db_query_params()`.
- Configuración, autenticación, control de sesiones y selector de centro con
  parámetros vinculados.
- Nueva pantalla de Aulas con consultas preparadas y tarjetas responsive.
- Tema administrativo aplicado a 23 cabeceras compartidas, 378 pantallas que
  las utilizan y 334 documentos administrativos independientes: 712 vistas
  en total. La zona de padres permanece sin cambios y la impresión conserva
  un estilo blanco específico.
- Tablas administrativas adaptables a todo el ancho, con desplazamiento
  interno para listados anchos y formularios apilados en pantallas pequeñas.
- Gestión de usuarios y portada de alumnos rehechas con estructura responsive.
- Cambio de centro habilitado para coordinadoras, accesos de aula restaurados
  para educadoras e impresión de menús disponible dentro de Comedor.
- Alta de alumnos ampliada con estado civil, precarga de datos de contacto al
  crear familiares y selección automática de familiares en los contactos.
- Nuevo listado simplificado por curso con aula, nacimiento, piscina, comedor
  y fechas de inicio y fin.
- Capa transitoria para funciones eliminadas (`split`, `ereg`, `each` y
  magic quotes).
- Scripts repetibles de auditoría y conversión dentro de esta carpeta.

## Qué sigue siendo deuda de migración

La dependencia de `ext/mysql` ya se ha retirado del código ejecutable. La
aplicación completa todavía no debe considerarse modernizada funcionalmente
porque una consulta trasladada a `mysqli` no se convierte automáticamente en
segura. Quedan, entre otros:

- Revisar las 12.747 consultas heredadas `db_query()` y migrar las que
  contienen datos variables a `db_query_params()` o repositorios.
- Validar y tipar los accesos directos a `$_GET` y `$_POST`.
- Sustituir `strftime`, fechas cero y SQL dependiente del modo permisivo.
- Migrar físicamente las columnas antiguas a `utf8mb4` en una copia ensayada;
  las conexiones y el texto de aplicación ya usan UTF-8.
- Revisar cada módulo con pruebas de comportamiento y permisos.
- Sacar las credenciales de base de datos del código y cargarlas desde
  variables de entorno.
- Actualizar o retirar bibliotecas antiguas de PDF, correo, foro y webcam.

## Migración segura de la base de datos

No se debe actualizar la base actual en el mismo servidor ni transformar las
tablas de producción directamente. MariaDB 10.1 y MySQL 8.4 son productos y
generaciones suficientemente distintos como para exigir una migración lógica
ensayada.

1. Obtener y verificar una copia lógica y una copia recuperable del servidor
   actual.
2. Ejecutar `sql/000_preflight_mysql84.sql` en modo lectura.
3. Crear una instancia separada de MySQL 8.4 LTS para pruebas.
4. Restaurar allí el volcado lógico.
5. Corregir fechas cero, intercalaciones, claves primarias y consultas
   incompatibles antes de activar modos SQL estrictos.
6. Ampliar `usuarios.pass` con `sql/001_password_hash.sql`.
7. Añadir `usuarios.estado_civil` con `sql/002_estado_civil_alumno.sql`.
8. Probar login, permisos, personal, facturación, agenda y procesos
   programados con una copia anonimizada.
9. Medir el tiempo de corte, repetir la migración desde un backup nuevo y
   conservar un plan de vuelta atrás.

El código moderno convierte automáticamente una contraseña histórica a
`password_hash()` después de un login correcto, pero solo cuando
`usuarios.pass` ya admite al menos 60 caracteres.

## Comprobaciones

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass `
  -File .\modernizacion\auditar_compatibilidad.ps1

C:\xampp\php\php.exe .\modernizacion\auditar_mysqli.php .
```

Las conversiones son idempotentes: una segunda ejecución no debe modificar
ningún fichero.
