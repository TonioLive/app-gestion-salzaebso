# app-gestion-salzaebso

Código de las aplicaciones de gestión de Lápices, ZEIM (escuelas municipales de Zaebsal) y NeoKids (piscina), organizado en un único repositorio privado.

| Aplicación | Carpeta | Origen de la copia |
| --- | --- | --- |
| Lápices | [apps/lapices](apps/lapices/README-SETUP.md) | `agenda.escuelasinfantileslapices.es` |
| ZEIM | [apps/zeim](apps/zeim/README-SETUP.md) | `santpau.escuelas-infantiles.com` |
| NeoKids | [apps/neokids](apps/neokids/README-SETUP.md) | `nueva.neo-kids.es` |

Cada carpeta es una aplicación independiente, con su propio dominio, configuración, base de datos, administración y acceso de familias. No se deben servir las tres aplicaciones como subcarpetas de un mismo dominio: emplean rutas que parten de `/`.

## Instalación

1. Consultar `README-SETUP.md` dentro de la aplicación que se vaya a instalar.
2. Instalar sus dependencias y preparar las extensiones y configuración PHP indicadas. Mantener las versiones fijadas por los archivos de dependencias.
3. Configurar un dominio y un directorio público separados para cada aplicación. Activar HTTPS en instalaciones accesibles desde Internet.
4. Crear las configuraciones privadas a partir de los ejemplos incluidos. Las claves se guardan fuera del control de versiones; nunca deben escribirse en el código.
5. Preparar una base de datos independiente. Los archivos `database/schema.sql`, cuando se incluyen, contienen únicamente estructura, sin registros. No constituyen una instalación con usuarios o catálogos iniciales. Para trasladar una instalación existente se restaura una copia de sus datos por un canal privado.
6. Crear los directorios de almacenamiento excluidos de Git y aplicar permisos de escritura únicamente a los que lo necesiten.
7. Comprobar el acceso del personal y de familias, mensajes, facturación y las integraciones configuradas antes de usar la nueva instalación.

Se incluyen los esquemas de ZEIM (237 tablas) y NeoKids (256 tablas). El servidor de la base de Lápices no estaba accesible al preparar esta copia: se conservan sus migraciones, pero su estructura completa debe obtenerse del servidor original o de una copia privada. Consulte [el estado de la base de Lápices](apps/lapices/database/README.md).

## Datos que no se incluyen

No forman parte de este repositorio las contraseñas, claves API, certificados privados, bases de datos con registros, sesiones, copias de seguridad, documentos de identidad, fotografías personales, firmas, recibos ni exportaciones de datos. Los ejemplos de configuración no contienen credenciales válidas.

El código y los datos son copias de seguridad diferentes. Para recuperar una instalación hacen falta tanto el código como sus configuraciones privadas, base de datos y archivos de usuarios, custodiados aparte.

## Trabajo local

Esta preparación crea una copia independiente del código. Las carpetas que sirven las aplicaciones locales originales no se mueven ni se sustituyen. Los cambios realizados en este repositorio no aparecerán automáticamente en esos dominios locales: primero se debe configurar un entorno de desarrollo que sirva las carpetas `apps/*` o realizar un despliegue revisado.

Las modificaciones para retirar credenciales y adaptar rutas de instalación están documentadas dentro de cada aplicación. Conservar las codificaciones existentes al editar archivos antiguos; no convertir masivamente textos o bases de datos.

## Publicación y mantenimiento

Mantener el repositorio privado. Revisar los archivos preparados para cada commit, especialmente los procedentes de carpetas de almacenamiento. `.gitignore` ayuda a evitar inclusiones accidentales, pero no sustituye la revisión de credenciales y datos reales dentro del código.

No se configura despliegue automático ni se cambian las aplicaciones en servicio al publicar este repositorio.
