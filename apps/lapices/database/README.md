# Estructura de la base de datos de Lápices

La estructura no está exportada en este repositorio. En el servidor local accesible MariaDB (puerto 3307) existen las bases de NeoKids y ZEIM, pero la base configurada para Lápices no existe. El servidor anterior no estaba accesible durante esta preparación.

Antes de iniciar Lápices en otro entorno, exporte la estructura desde su servidor correcto o copia privada de seguridad. Para publicar una estructura, excluya datos, usuarios, contraseñas, rutinas, eventos, DEFINER y contadores AUTO_INCREMENT; revise también los valores predeterminados y comentarios. No use la estructura de NeoKids o ZEIM como sustituto.

Los datos de alumnos, familias, permisos, centros, cursos, grupos y conceptos se restauran por un canal privado autorizado. No se crean cuentas iniciales ni se cambian contraseñas en esta preparación.
