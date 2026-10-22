# ZEIM · Escuelas municipales

Código del programa de gestión y del área de familias. La carpeta de origen es `santpau.escuelas-infantiles.com`; el identificador interno `santpau` se conserva para no alterar sesiones, permisos ni integraciones.

## Requisitos

- PHP 8.5 con `mysqli`, `mbstring`, `openssl`, `iconv`, `gd`, `curl` y las extensiones exigidas por Composer.
- MariaDB compatible con el esquema incluido. El entorno de origen utiliza MariaDB 12.3.
- Apache con `mod_rewrite`, cabeceras y `AllowOverride All` para aplicar los `.htaccess`.
- Ejecutar `composer install --no-dev --prefer-dist` dentro de esta carpeta. `composer.lock` fija las versiones utilizadas. El cargador moderno usa `vendor/autoload.php`. Las bibliotecas anteriores se conservan en `legacy-vendor` para las rutas de compatibilidad originales, sin publicar almacenamiento privado.

## Configuración de una instalación

1. Crear un virtual host cuyo `DocumentRoot` sea esta carpeta. No servir la raíz del monorepositorio.
2. Configurar `auto_prepend_file` de PHP con la ruta absoluta a `configuracion/php85-bootstrap.php`. Esta entrada aplica las comprobaciones comunes de sesión, permisos y solicitudes; no debe omitirse al desplegar páginas antiguas.
3. Crear `.private` fuera del control de versiones y protegerla con un `.htaccess` que contenga `Require all denied`. Permitir escritura al usuario del servidor en las carpetas privadas de sesiones, intentos de acceso y caché que usa el programa. Configurar `session.save_path` con una carpeta privada exclusiva de esta aplicación.
4. Copiar `configuracion/examples/database.php.example` a `.private/database.php` y proporcionar `ZEIM_DB_HOST`, `ZEIM_DB_USER`, `ZEIM_DB_PASSWORD` y `ZEIM_DB_NAME` mediante el entorno del proceso PHP. `ZEIM_DB_HOST` admite `host:puerto`.
5. Copiar `configuracion/examples/api-auth.php.example` a `.private/api-auth.php` y configurar una clave aleatoria privada de al menos 32 caracteres en `ZEIM_API_AUTH_KEY`.
6. Restaurar `.private/family-credentials.key` desde el almacenamiento seguro del entorno que se migra. Es una clave binaria de 32 bytes necesaria para recuperar las copias cifradas de las contraseñas familiares. **No sustituirla ni regenerarla al trasladar una base de datos existente.** En una instalación completamente nueva puede generarse una vez con `random_bytes(32)` y guardarse directamente en ese archivo, sin publicarla.
7. En una base de datos nueva y vacía, importar `database/schema.sql`. Contiene exclusivamente estructura: no incluye alumnos, usuarios, contraseñas, catálogos, recibos ni ninguna fila de negocio. Después hay que configurar los centros, cursos, catálogos y una cuenta administradora mediante un procedimiento autorizado. Para migrar un entorno existente, restaurar su copia privada por un canal separado de GitHub y conservar sus claves originales.
8. Incorporar, por un canal privado, las plantillas documentales, documentos, fotos y demás datos que requiera esa instalación. No añadirlos al repositorio.

Los módulos históricos de correo, SMS, cámaras y firma PDF usan las variables vacías enumeradas en `configuracion/examples/integrations.env.example`. `integrations-map.json` identifica los archivos que consumen cada variable, sin contener sus valores. Configurarlas solo si se utiliza la integración correspondiente. Las rutas de certificados de firma admiten el formato original `file://...`; sus claves privadas se almacenan fuera del código. Recrear las carpetas vacías y escribibles `mailer/phpqrcode/cache` y `mailer/phpqrcode/temp` si se usa la generación de QR.

Las cuentas bancarias de remesas también se configuran mediante las variables `ZEIM_BANK_*`; la generación se detiene si falta su configuración. No se incluyen números reales de cuenta ni fotografías tomadas por las cámaras de aula, aunque estuvieran guardadas junto a recursos de la interfaz.

## Archivos excluidos

Se mantienen el código de gestión, familia/API, estilos, recursos de interfaz y declaraciones de dependencias. Se excluyen `.private` original, contraseñas y claves, certificados, sesiones, registros de acceso, copias históricas, ejecutables/APK/ActiveX, documentos de personas, fotos y firmas, hojas de cálculo, remesas bancarias y recibos, gráficos generados, capturas de pantalla y cachés QR. Las plantillas RTF/DOC/PDF originales se gestionan aparte porque pueden contener datos reales. El avatar genérico `fotos_usuarios/sin_foto.jpg` se conserva.

La copia del código no incluye ni modifica las bases de datos ni los archivos operativos del servidor original. No es una instalación con datos de demostración ni dispone de usuarios o contraseñas predeterminados.

### Certificados de comedor

Configurar `ZEIM_CATERING_CERTIFICATE_SIGNER_TEXT` con el texto autorizado del firmante (identidad, cargo e identificación cuando corresponda). Los certificados de comedor se detienen si falta esa configuración. El repositorio no incluye la identidad personal usada en el servidor anterior.
