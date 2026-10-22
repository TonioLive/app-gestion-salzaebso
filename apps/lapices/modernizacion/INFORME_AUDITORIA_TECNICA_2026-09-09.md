# Auditoría técnica, rendimiento y seguridad

Fecha: 9 de septiembre de 2026

## Resultado

- Se validaron 2.680 archivos PHP propios del proyecto, excluyendo dependencias externas, con PHP 5.6 y PHP 8.5.
- Resultado final: 0 errores de sintaxis en ambas versiones.
- La auditoría por tokens confirma 0 llamadas ejecutables a la extensión retirada `mysql_*`.
- Se probaron el vencimiento, la renovación y el aviso visual de sesión en PHP 5.6, PHP 8.5 y un viewport móvil de 390 × 844 píxeles.
- Se verificó la configuración de Apache con `Syntax OK`.

## Cambios aplicados

### Sesiones

- Caducidad central de las sesiones de gestión y familias tras más de 30 minutos sin actividad real.
- Las consultas automáticas de mensajes y otros procesos en segundo plano no renuevan la sesión.
- Aviso accesible dos minutos antes del cierre, con cuenta atrás y opción «Seguir trabajando».
- El servidor vuelve a comprobar el plazo en cada navegación; no depende únicamente de JavaScript.
- Renovación protegida mediante POST y token CSRF.
- Eliminación completa de los datos y la cookie al cerrar la sesión.
- Mensaje específico al volver al acceso después de una caducidad.

### Seguridad

- Cabeceras comunes `nosniff`, `SAMEORIGIN`, política de referencia, permisos, HSTS en HTTPS y prohibición de caché para respuestas privadas.
- Errores internos y mensajes de MySQL ocultos al usuario y enviados al registro del servidor. Se pueden mostrar de forma controlada con `APP_DEBUG=1`.
- Inicio de sesión de familias protegido con CSRF, consultas preparadas, comparación de contraseña en tiempo constante y regeneración del identificador de sesión.
- API móvil revisada: HTTPS obligatorio, lista cerrada de orígenes, soporte de `Authorization: Bearer`, validación segura de JWT, control de excepciones, consultas preparadas y corrección del registro FCM.
- Acceso web por token migrado a un controlador seguro y rutas antiguas redirigidas a sus equivalentes actuales.
- Deserialización protegida contra instanciación de objetos en 18 flujos de formularios, contactos y cocina.
- Desactivación del `phpinfo()` público, del listado de carpetas y del acceso web a archivos de migración, registros y configuración auxiliar.
- Credenciales preparadas para variables de entorno: `APP_DB_HOST`, `APP_DB_NAME`, `APP_DB_USER`, `APP_DB_PASSWORD`, `APP_BLOG_DB_NAME`, `APP_BLOG_DB_PASSWORD`, `APP_JWT_SECRET` y `APP_ALLOWED_ORIGINS`.

### Rendimiento y estabilidad

- Reutilización de la conexión MySQL común: se elimina una segunda conexión por petición en el flujo habitual de personal.
- Tiempo máximo de 300 segundos para peticiones web, evitando procesos bloqueados de duración indefinida; los procesos CLI conservan su funcionamiento.
- Tiempo máximo de ocho segundos al abrir una conexión MySQL.
- Consulta de cursos reducida a las columnas necesarias y cacheada cinco minutos por sesión.
- Registro de navegación de familias limitado a páginas reales, excluyendo peticiones automáticas, y acotado a los últimos 16.000 caracteres.
- Limpieza de tokens caducados acotada y ocasional en lugar de ejecutar un borrado completo en cada llamada a la API.
- Corrección de accesos a parámetros inexistentes en la cabecera de familias y de una referencia errónea al leer la fecha de nacimiento de un contacto.

## Índices añadidos a la base de datos local

- `usuarios.idx_usuarios_login (usuario)`
- `padres.idx_padres_login (usuario_webcam)`
- `tokens_activos.idx_tokens_activos_token (token(191))`
- `tokens_activos.idx_tokens_activos_creacion (creacion)`
- `tokens_fcm.idx_tokens_fcm_lookup (id_usuario, id_padre, FCMtoken(128))`
- `control_acceso_padres.idx_control_acceso_sesion (sesion)`

El último es especialmente relevante: `control_acceso_padres` contiene aproximadamente 237.000 filas y antes buscaba la sesión sin índice. El instalador se conserva en `modernizacion/aplicar_indices_seguridad_rendimiento.php`; es idempotente y solo funciona desde línea de comandos.

## Deuda heredada que permanece

La aplicación ya utiliza el adaptador `mysqli`, pero todavía contiene 12.737 llamadas a `db_query()` en 1.510 archivos. Muchas son consultas estáticas válidas, aunque una parte sigue construyendo SQL con valores insertados en cadenas. También se detectan 3.501 lecturas directas de `$_GET` y 3.433 de `$_POST`. No se deben reemplazar mecánicamente: cada operación necesita conocer sus tipos, permisos, centro activo y reglas funcionales.

Las contraseñas antiguas de personal se convierten automáticamente a `password_hash()` cuando el usuario inicia sesión y la columna lo permite. Las credenciales de familias todavía forman parte de procesos históricos de recordatorio en texto claro; cifrarlas exige migrar antes esos procesos a restablecimiento de contraseña para no dejar familias sin acceso.

La siguiente fase recomendada es activar el registro de consultas lentas en una copia de MySQL 8.4 con datos equivalentes y migrar consultas por riesgo y frecuencia: autenticación y permisos, alumnos, facturación, asistencia/comedor y envíos. Así se podrán añadir índices basados en mediciones reales sin sobreindexar tablas grandes.
