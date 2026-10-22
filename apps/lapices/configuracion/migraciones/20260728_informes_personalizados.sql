CREATE TABLE IF NOT EXISTS informes_personalizados (
    id_informe INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(160) NOT NULL,
    descripcion VARCHAR(500) NOT NULL DEFAULT '',
    definicion LONGTEXT NOT NULL,
    id_usuario_creador INT NOT NULL,
    compartido TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion DATETIME NOT NULL,
    fecha_actualizacion DATETIME NOT NULL,
    ultima_ejecucion DATETIME DEFAULT NULL,
    PRIMARY KEY (id_informe),
    KEY idx_informes_creador (id_usuario_creador),
    KEY idx_informes_compartido (compartido),
    KEY idx_informes_actualizacion (fecha_actualizacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM menu_administrador
WHERE link = '/gestion/centros/listado_camaras.php'
   OR LOWER(Texto) = 'gestión cámaras'
   OR LOWER(Texto) = 'gestion camaras';

DELETE FROM menu_administrador
WHERE link IN (
    '/gestion/informes/',
    '/gestion/informes/guardados.php'
);

INSERT INTO menu_administrador
    (texto_ayuda, link, Orden, Texto, nivel_acceso)
VALUES
    (
        'Crea informes eligiendo tablas, columnas, filtros, uniones y ordenación',
        '/gestion/informes/',
        5,
        'Informes personalizados',
        4
    ),
    (
        'Ejecuta, edita y exporta los informes guardados con datos actualizados',
        '/gestion/informes/guardados.php',
        6,
        'Informes guardados',
        4
    );
