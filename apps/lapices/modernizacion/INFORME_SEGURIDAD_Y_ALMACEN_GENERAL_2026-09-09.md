# Auditoría de seguridad y Almacén general

Fecha: 9 de septiembre de 2026

## Resumen ejecutivo

Se ha realizado una revisión estática y funcional de los puntos de mayor riesgo del programa heredado y se ha creado el nuevo módulo **Almacén general**. La intervención cierra riesgos críticos que sí estaban presentes: contraseñas recuperables, una clave JWT fija en el código, escrituras privadas sin una protección transversal y una autorización insuficiente en la gestión familiar de personas autorizadas.

La revisión reduce de forma importante la superficie de ataque, pero no permite afirmar que un programa heredado de este tamaño esté libre de cualquier vulnerabilidad. Queda deuda técnica en consultas SQL antiguas y en componentes que ya no reciben soporte.

## Correcciones de seguridad aplicadas

### Credenciales

- Se ampliaron `usuarios.pass` y `padres.password_webcam` a `VARCHAR(255)`.
- Se migraron 70 credenciales de personal y 9.846 credenciales familiares a bcrypt.
- Verificación final: 0 credenciales antiguas sin hash en ambas tablas.
- Los accesos modernos verifican bcrypt y conservan una actualización automática para cualquier registro legado que pudiera importarse en el futuro.
- La pantalla de personal ya no muestra contraseñas ni hashes. Solo permite saber que la credencial está protegida.
- La recuperación de contraseñas fue sustituida por un flujo de asignación de una clave nueva: una contraseña segura no se puede recuperar.
- Las altas y cambios de credenciales revisados guardan bcrypt desde el primer momento.

### Sesiones y rutas privadas

- Se mantiene el cierre automático por 30 minutos de inactividad tanto en gestión como en familias.
- Las rutas privadas de gestión exigen sesión válida y bloquean escrituras procedentes de otros orígenes.
- Las rutas familiares autenticadas aplican la misma protección transversal a operaciones de escritura.
- Se añadieron tokens CSRF explícitos a las operaciones de almacén renovadas y a las eliminaciones heredadas revisadas.
- Se incorporaron cabeceras de seguridad para impedir interpretación MIME, marcos ajenos, caché privada indebida y permisos de navegador no necesarios.

### Autorización familiar

- La edición y eliminación de personas autorizadas ya no confía en los identificadores recibidos del navegador.
- Se comprueba que el usuario conectado sea padre o madre del alumno y que la relación, el alumno y la persona autorizada coincidan.
- Las consultas críticas de este flujo usan parámetros preparados.
- Una credencial nueva solo se muestra una vez y durante un máximo de cinco minutos en la sesión; en la base de datos solo se conserva el hash.

### Tokens y componentes antiguos

- Se retiró la clave JWT fija que estaba repetida en el código.
- El secreto JWT se obtiene de `APP_JWT_SECRET` o de un archivo local aleatorio no publicable.
- Las rutas antiguas de acceso y token se redirigen a los controladores seguros.
- El foro phpBB 3.0.7-PL1 de 2010 quedó aislado mediante denegación de acceso. No se eliminó contenido para conservar una posible migración futura.
- Apache bloquea listados de directorios, archivos ocultos y extensiones sensibles como SQL, logs, copias y archivos de configuración.

## Nuevo módulo Almacén general

### Modelo de datos

Se añadieron tablas InnoDB y UTF-8 para:

- familias personalizables;
- zonas de almacén;
- ubicaciones dentro de cada zona;
- artículos, referencias, unidades, stock actual y stock mínimo;
- historial inmutable de entradas, salidas y ajustes.

Las zonas iniciales son **Lápices · G.A.** y **Piscina NeoKids**. Las familias iniciales son material escolar, limpieza, informática, herramientas, material de aula, repuestos del centro y piscina.

### Funciones

- Alta y edición de artículos.
- Entrada, salida y ajuste de existencias.
- Bloqueo de stock negativo.
- Historial con usuario, fecha, motivo, cantidad y saldo anterior/posterior.
- Personalización de familia y zona mediante nombre, color e icono.
- Ubicaciones con código y descripción.
- Buscador, filtros por familia y zona, aviso de stock bajo y paginación 20/50/80.
- Diseño de tabla en escritorio y tarjetas en móvil.

### Integridad y permisos

- Las operaciones usan consultas preparadas y token CSRF.
- Cada movimiento se ejecuta dentro de una transacción con bloqueo de la fila del artículo.
- El módulo exige sesión de personal, centro seleccionado y permiso de almacén.
- Las claves foráneas evitan borrar familias, zonas o ubicaciones que estén en uso.

## Verificaciones realizadas

- Migración de credenciales finalizada y verificada mediante conteos agregados, sin exponer contraseñas ni hashes.
- Prueba funcional de stock: entrada, salida y ajuste con saldo final correcto e historial completo; los datos de prueba se retiraron.
- Sintaxis correcta en 48 archivos intervenidos tanto con PHP 5.6 como con PHP 8.5.
- JavaScript del nuevo almacén validado con Node.
- Configuración de Apache validada: `Syntax OK`.
- Accesos privados sin sesión devuelven 401 o redirigen al inicio.
- Operaciones heredadas sensibles sin token/origen válido devuelven 403.
- Revisión visual del almacén en escritorio y móvil sin desbordamiento de la página.

## Riesgos pendientes y prioridades

### Alta: actualizar el servidor de base de datos

El entorno local sigue usando MariaDB 10.1.38, una versión obsoleta. El esquema nuevo está preparado con InnoDB y `utf8mb4`, pero el servidor completo debe migrarse a una versión soportada de MySQL 8 o MariaDB LTS utilizando primero una copia de los datos y pruebas de restauración.

### Alta: retirar credenciales de base de datos del código

La configuración acepta variables `APP_DB_HOST`, `APP_DB_NAME`, `APP_DB_USER` y `APP_DB_PASSWORD`, pero mantiene valores heredados de respaldo para que el entorno local no deje de funcionar. En producción deben definirse las variables y después eliminarse esos respaldos del código.

### Media: continuar la conversión SQL módulo a módulo

El inventario estático todavía localiza aproximadamente 12.708 llamadas heredadas a `db_query()` fuera de dependencias y del foro. La capa transversal bloquea accesos anónimos y peticiones cruzadas, pero no convierte automáticamente todas las consultas en preparadas. Conviene continuar por módulos, priorizando escrituras, búsquedas públicas y campos de texto libre.

### Media: política CSP y dependencias antiguas

Una Content-Security-Policy estricta todavía rompería JavaScript y estilos incrustados de pantallas antiguas. Debe activarse progresivamente conforme esas vistas se modernicen. También es recomendable inventariar librerías antiguas de PDF, correo y JavaScript y reemplazar las que hayan quedado sin mantenimiento.

### Media: revisión específica de cargas de archivos

Las cargas históricas de fotografías, DNI y documentos necesitan una campaña separada para unificar límites de tamaño, lista blanca real de formatos, renombrado aleatorio, almacenamiento fuera del directorio público y análisis de contenido.

## Recomendación operativa

Antes de publicar en el servidor nuevo: copia completa, restauración de ensayo, migración de base de datos, variables de entorno, HTTPS obligatorio y prueba de los recorridos críticos por cada rol. Después de la publicación conviene revisar los logs de autenticación y errores durante los primeros días.
