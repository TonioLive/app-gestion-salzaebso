# Lápices

Código de gestión de escuelas y área de familias. El directorio conserva las rutas del sitio original; debe ser la raíz de un sitio virtual propio, no una subcarpeta de otro programa.

## Puesta en marcha

1. Configure Apache con HTTPS y PHP 8.5, mysqli, mbstring, openssl, curl, gd y las extensiones necesarias para los módulos utilizados. Las rutas de transición están en `.htaccess`.
2. Configure las variables de `.env.example` en el entorno del servicio PHP/Apache. El archivo de ejemplo no se carga automáticamente. `APP_DB_*` corresponde al registro principal; no se publica ninguna contraseña. Si hay varias conexiones antiguas, las variables `LAPICES_DB_*` permiten configurarlas de forma independiente. Busque el nombre de cada variable en el código para identificar el módulo que la usa.
3. Proporcione `APP_JWT_SECRET` mediante el gestor de secretos; mantenga el mismo valor del entorno migrado si deben seguir vigentes sus tokens. No genere ni restablezca las contraseñas existentes de familias.
4. Restaure la estructura y los datos autorizados de su copia privada de seguridad. No hay filas ni cuentas iniciales en este repositorio; los catálogos de centros, cursos, permisos, grupos y conceptos son datos privados de cada instalación. Revise `database/` y la documentación de la raíz para el estado de disponibilidad del esquema. Las migraciones de `configuracion/migraciones/` y `modernizacion/sql/` se conservan como código y deben aplicarse solo tras revisar su versión.
5. Restaure, desde almacenamiento privado, los documentos/modelos que realmente utilice su instalación, fotos, firmas y archivos de alumnos. Dé permiso de escritura únicamente a los directorios de almacenamiento que cada módulo necesite. Los placeholders sin foto/sin firma sí se incluyen.

## Integraciones opcionales

- Correo: configure `LAPICES_SMTP_HOST`, `LAPICES_SMTP_USER` y la variable `LAPICES_SMTP_PASSWORD_*` correspondiente al módulo. Revise también sus direcciones de remitente antes de permitir envíos.
- Formularios: configure `LAPICES_RECAPTCHA_SECRET` y la clave pública de su propio sitio.
- Foro/blog y conexiones antiguas: configure sus variables de base de datos únicamente si mantiene activos esos módulos.
- Firma de PDF: instale certificado y clave fuera del repositorio y configure `LAPICES_PDF_CERTIFICATE`, `LAPICES_PDF_PRIVATE_KEY` y `LAPICES_PDF_PRIVATE_KEY_PASSWORD`. Sin certificado/clave configurados, los dos generadores de facturas producen el documento sin firma digital.
- Cámaras, galerías y huella: se conserva su código, pero las credenciales, imágenes históricas, bases de datos biométricas, certificados y binarios de instalación no se versionan. Deben suministrarse por los canales privados del entorno.

## Contenido excluido

Sesiones, cachés de foro y galería, `.private`, certificados/claves, exportaciones, documentos de personas/facturas, modelos RTF/PDF/DOC sin revisión, fotografías históricas de cámaras, firmas de correo del personal, metadatos de editores, copias y binarios de instaladores. Ningún original de la instalación local se ha modificado al preparar esta copia.

Esta copia sirve como base de versionado y despliegue configurable; no es una certificación de seguridad del código heredado. Antes de abrir una instalación nueva, revise qué módulos antiguos van a exponerse y pruebe con una copia privada de datos.

## Archivos privados y bancos

`APP_BASE_URL` define el dominio de esta instalación. `LAPICES_STORAGE_PATH` define su almacenamiento privado; si se omite se utiliza `.private/storage` dentro de esta copia. La configuración y los módulos con rutas absolutas de Windows ya no apuntan a las carpetas de la instalación original. Restaure bajo esta raíz las subcarpetas `gabinete`, `documentacion`, `fotos_padres`, `prl`, `temp_informes` y `tmp_zip` que utilice. Mantenga el acceso web denegado a `.private` o sitúe esta raíz fuera del directorio público. Las imágenes que el código sirve como recursos del sitio conservan rutas dentro de la propia aplicación; configure sus permisos y restaure los datos por separado.

Las remesas usan variables `LAPICES_BANK_*`, separadas por módulo, para las cuentas completas y sus segmentos. Deben contener solo los dígitos correspondientes, o un IBAN español de 24 caracteres sin espacios. Si falta un campo necesario, el generador se detiene con un error de configuración en vez de emitir una cuenta vacía. Configure y valide los ficheros bancarios con su entidad antes de remitirlos. El repositorio no incluye cuentas reales, firmas manuscritas ni formularios administrativos escaneados.

Las seis pantallas antiguas de cámara requieren `LAPICES_CAMERA_LOGIN`, `LAPICES_CAMERA_PASSWORD` y `LAPICES_CAMERA_CODE`; las credenciales originales no se incluyen. Ese cliente heredado utiliza estos valores en el navegador, por lo que solo debe habilitarse para los usuarios autorizados de una red privada, usando credenciales de cámara limitadas. Para acceso externo, sustituya ese cliente por una integración de servidor antes de activarlo.
