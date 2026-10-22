# NeoKids

Código del programa de gestión de piscina y de la app web de familias. El árbol procede de `nueva.neo-kids.es`; la raíz pública de este programa debe ser **esta carpeta**, con su propio dominio/vhost. Las URL `/app/`, `/gestion/`, etc. dependen de ello.

## Preparar una instalación

1. Usar PHP 8.5 con mysqli, mbstring, curl, OpenSSL, DOM/XML, GD y Argon2id, y MariaDB compatible con las consultas `WITH` del calendario. Activar `short_open_tag=On` para las pantallas heredadas.
2. Instalar las versiones de dependencias fijadas en `composer.lock` con `composer install --no-dev`. El directorio `vendor/` se genera localmente; se conserva el adaptador de clases heredadas.
3. Crear `.private/` fuera del control de versiones, con permisos restringidos. Añadir allí un `.htaccess` con `Require all denied` y configurar el servidor para denegar todo acceso HTTP a esa carpeta.
4. Copiar `config-examples/database.php` a `.private/database.php` y configurar las variables `NEOKIDS_DB_*` en el proceso PHP. `sql_host` puede incluir el puerto (`127.0.0.1:3306`). Los ficheros de ejemplo no leen automáticamente un `.env`.
5. Crear una base de datos vacía e importar `database/schema.sql`. Contiene únicamente la estructura de 256 tablas: **no contiene usuarios, contraseñas, alumnos, facturas ni catálogos de explotación**. Una instalación nueva necesita cargar catálogos ficticios y crear su cuenta de administración mediante un procedimiento autorizado. No usar una copia de datos reales como datos de ejemplo.
6. Para recuperar una instalación existente, importar su copia privada de la base de datos y sus ficheros privados por un canal separado. Conservar la clave original `.private/family-credentials.key`: cambiarla impediría recuperar las claves de acceso familiares ya cifradas. En una instalación realmente nueva, generar 32 bytes aleatorios para ese fichero y protegerlo.
7. Configurar PHP para ejecutar `configuracion/php85-bootstrap.php` mediante `auto_prepend_file` (ruta absoluta de la nueva instalación). El arranque aplica las comprobaciones comunes de seguridad y compatibilidad UTF-8. Crear un directorio privado de sesiones y configurarlo como `session.save_path`.
8. Apache necesita `mod_rewrite`, `mod_headers` y permitir las reglas `.htaccess`. Publicar con HTTPS para las sesiones y las notificaciones de la app.

## Integraciones opcionales

- **Escuelas:** copiar `config-examples/schools-database.php` a `.private/` y proporcionar las variables `NEOKIDS_SCHOOLS_DB_*`. Es una conexión independiente de la base de la piscina; no asumir el mismo puerto o usuario.
- **Equipos de piscina:** copiar `config-examples/pool-config.php` a `.private/`. Las variables `NEOKIDS_POOL_WATER_URL` y `NEOKIDS_POOL_AIR_URL` son las direcciones base completas, sin `/standard/default.php`. Las credenciales de los controladores, la cuenta VistaPool, su identificador de piscina y API key se configuran en ese mismo fichero privado. Programar `php workers/pool/monitor.php` cada 10 minutos; la definición de la tarea programada y sus secretos no se incluyen en Git.
- **Correo heredado:** configurar `NEOKIDS_SMTP_HOST`, `NEOKIDS_SMTP_USER` y `NEOKIDS_SMTP_PASSWORD`. Los remitentes y opciones de transporte de cada módulo permanecen como en el programa original.
- **SMS heredado:** configurar `NEOKIDS_SMS_USER` y `NEOKIDS_SMS_PASSWORD`. No ejecutar envíos de prueba con datos reales.
- **Blog heredado y firma PDF:** configurar, si se usan, `NEOKIDS_BLOG_DB_PASSWORD` y `NEOKIDS_PDF_SIGN_PASSWORD`. Instalar los certificados y claves de firma por separado; los certificados de ejemplo empaquetados anteriormente también se han excluido.
- **Notificaciones:** instalar Node.js y ejecutar `npm ci` dentro de `workers/notifications/`. Copiar `config-examples/notifications-config.json` a `.private/notifications/config.json` y sustituir sus marcadores por un par VAPID nuevo para una instalación nueva (o restaurar el par original al migrar una existente), el contacto de administración y el ejecutable PHP con su configuración CLI. Programar `node workers/notifications/worker.cjs` y `node workers/pool/admin-worker.cjs` cada minuto. El código de estos procesos sí se conserva; sus claves, suscripciones, colas, estados y tareas se restauran/configuran de forma privada. `node workers/notifications/worker.cjs --check` comprueba configuración y conexión sin enviar avisos. No se han copiado claves ni datos de destinatarios.
- **API heredada:** el ejemplo `config-examples/api-auth.php` muestra la configuración privada cuando sea necesaria.
- **Cámaras heredadas:** si se conserva la integración IPCam, configurar sus parámetros privados `NEOKIDS_CAMERA_LOGIN`, `NEOKIDS_CAMERA_PASSWORD` y `NEOKIDS_CAMERA_CODE`. No se incluyen los valores opacos de autenticación que tenían las páginas antiguas.
- **Facturación y remesas:** configurar `NEOKIDS_INVOICE_BANK_DETAILS` para los datos bancarios que aparecen en las facturas y `NEOKIDS_DIRECTOR_NAME` para el nombre de dirección. Cada modalidad de remesa requiere su cuenta acreedora privada: `NEOKIDS_SABADELL_IBAN`, `NEOKIDS_SANTANDER_IBAN` o `NEOKIDS_LEGACY_IBAN`, sin espacios. El código no incluye cuentas bancarias reales.

## Archivos fuera de Git

No se incluyen `.private/`, sesiones, registros, copias de seguridad, fotos de personas, DNI, firmas, recibos XML, informes de alumnos, bases de datos con filas ni documentos de explotación. También se han retirado imágenes de cámaras, firmas y anexos incrustados en carpetas del código. Quedan fuera los PDF/documentos de `descargas/`, `modelos/`, `modelos_sub/`, `pdf/` y carpetas equivalentes: antes de utilizar una función de impresión, recuperar únicamente sus plantillas autorizadas mediante el almacén privado de la instalación.

Las bibliotecas, CSS, JavaScript, fuentes e imágenes estáticas del programa sí forman parte del código. Se mantienen módulos heredados para no romper enlaces; esta preparación de Git no equivale a una nueva auditoría completa de seguridad ni cambia los programas locales en funcionamiento.

Las imágenes subidas de programación de grupos (`gestion/grupos*/imagenes/`) y las fotografías antiguas de aulas de `webcam/imag/popup/` también pertenecen al almacenamiento privado de la instalación. Se conservan los marcadores genéricos `sin_imagen.jpg`; no se incluyen fotografías de personas o instalaciones ni el contenido subido en esas rutas. Restaurarlo únicamente desde una copia autorizada, conservando los controles de acceso.
