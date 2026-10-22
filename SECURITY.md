# Credenciales y datos privados

Mantener separados el código de las aplicaciones y los datos de las instalaciones.

- Usar los ejemplos de configuración como punto de partida y guardar los valores reales en archivos privados o en las variables de entorno indicadas.
- No incluir archivos de alumnos, familias o empleados, volcados de bases de datos con registros ni documentos de facturación en commits, incidencias o pull requests.
- Comprobar los cambios antes de subirlos, aunque el repositorio sea privado.
- Si una credencial se publica por error, revocarla o cambiarla en el servicio correspondiente. Borrar su archivo del último commit no elimina versiones anteriores del historial.
- Documentar incidencias de seguridad sin añadir datos personales ni claves. Comunicar los detalles por un canal privado a la administración del proyecto.

La preparación para GitHub no equivale a certificar que todas las funciones del código heredado carecen de vulnerabilidades. Las revisiones funcionales y de seguridad continúan por aplicación.
