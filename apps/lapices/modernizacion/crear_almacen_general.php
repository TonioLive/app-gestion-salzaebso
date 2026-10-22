<?php
/**
 * Crea de forma idempotente el esquema del almacén general de G.A.
 * Uso: php modernizacion/crear_almacen_general.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

ini_set('session.save_path', sys_get_temp_dir());
require_once dirname(__DIR__) . '/configuracion/config.php';

$database = app_db_connect($sql_host, $sql_usuario, $sql_pass, $sql_db);
if (!($database instanceof mysqli)) {
    fwrite(STDERR, "No se ha podido conectar con la base de datos.\n");
    exit(1);
}

$statements = array(
    "CREATE TABLE IF NOT EXISTS almacen_general_familias (
        id_familia INT UNSIGNED NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        color CHAR(7) NOT NULL DEFAULT '#21675A',
        icono VARCHAR(30) NOT NULL DEFAULT 'caja',
        orden SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_por INT UNSIGNED NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id_familia),
        UNIQUE KEY uq_almacen_general_familia_nombre (nombre),
        KEY idx_almacen_general_familia_activo_orden (activo, orden, nombre)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    "CREATE TABLE IF NOT EXISTS almacen_general_zonas (
        id_zona INT UNSIGNED NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(100) NOT NULL,
        color CHAR(7) NOT NULL DEFAULT '#21675A',
        icono VARCHAR(30) NOT NULL DEFAULT 'almacen',
        orden SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_por INT UNSIGNED NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id_zona),
        UNIQUE KEY uq_almacen_general_zona_nombre (nombre),
        KEY idx_almacen_general_zona_activo_orden (activo, orden, nombre)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    "CREATE TABLE IF NOT EXISTS almacen_general_ubicaciones (
        id_ubicacion INT UNSIGNED NOT NULL AUTO_INCREMENT,
        id_zona INT UNSIGNED NOT NULL,
        nombre VARCHAR(120) NOT NULL,
        codigo VARCHAR(50) NOT NULL,
        descripcion VARCHAR(255) NOT NULL DEFAULT '',
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_por INT UNSIGNED NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id_ubicacion),
        UNIQUE KEY uq_almacen_general_ubicacion_codigo (id_zona, codigo),
        KEY idx_almacen_general_ubicacion_zona (id_zona, activo, nombre),
        CONSTRAINT fk_almacen_general_ubicacion_zona FOREIGN KEY (id_zona)
            REFERENCES almacen_general_zonas (id_zona)
            ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    "CREATE TABLE IF NOT EXISTS almacen_general_articulos (
        id_articulo INT UNSIGNED NOT NULL AUTO_INCREMENT,
        id_centro SMALLINT UNSIGNED NOT NULL DEFAULT 6,
        id_familia INT UNSIGNED NOT NULL,
        id_ubicacion INT UNSIGNED NOT NULL,
        nombre VARCHAR(150) NOT NULL,
        referencia VARCHAR(80) NULL,
        unidad VARCHAR(30) NOT NULL DEFAULT 'unidad',
        stock_actual DECIMAL(12,3) NOT NULL DEFAULT 0.000,
        stock_minimo DECIMAL(12,3) NOT NULL DEFAULT 0.000,
        descripcion TEXT NULL,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_por INT UNSIGNED NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id_articulo),
        UNIQUE KEY uq_almacen_general_articulo_referencia (referencia),
        KEY idx_almacen_general_articulo_busqueda (activo, id_familia, id_ubicacion, nombre),
        KEY idx_almacen_general_articulo_stock (activo, stock_actual, stock_minimo),
        KEY idx_almacen_general_articulo_centro (id_centro),
        CONSTRAINT fk_almacen_general_articulo_familia FOREIGN KEY (id_familia)
            REFERENCES almacen_general_familias (id_familia)
            ON UPDATE CASCADE ON DELETE RESTRICT,
        CONSTRAINT fk_almacen_general_articulo_ubicacion FOREIGN KEY (id_ubicacion)
            REFERENCES almacen_general_ubicaciones (id_ubicacion)
            ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    "CREATE TABLE IF NOT EXISTS almacen_general_movimientos (
        id_movimiento BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        id_articulo INT UNSIGNED NOT NULL,
        tipo ENUM('ENTRADA','SALIDA','AJUSTE') NOT NULL,
        cantidad DECIMAL(12,3) NOT NULL,
        stock_anterior DECIMAL(12,3) NOT NULL,
        stock_posterior DECIMAL(12,3) NOT NULL,
        motivo VARCHAR(150) NOT NULL DEFAULT '',
        observaciones VARCHAR(500) NOT NULL DEFAULT '',
        id_usuario INT UNSIGNED NOT NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id_movimiento),
        KEY idx_almacen_general_movimiento_articulo_fecha (id_articulo, creado_en),
        KEY idx_almacen_general_movimiento_fecha (creado_en),
        KEY idx_almacen_general_movimiento_usuario (id_usuario),
        CONSTRAINT fk_almacen_general_movimiento_articulo FOREIGN KEY (id_articulo)
            REFERENCES almacen_general_articulos (id_articulo)
            ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

foreach ($statements as $statement) {
    if (!mysqli_query($database, $statement)) {
        fwrite(STDERR, "Error creando el esquema: " . mysqli_error($database) . "\n");
        exit(1);
    }
}

$families = array(
    array('Material escolar', '#E5A93D', 'lapiz', 10),
    array('Material de limpieza', '#33A176', 'limpieza', 20),
    array('Informática', '#4C78D4', 'informatica', 30),
    array('Herramientas', '#697782', 'herramientas', 40),
    array('Material de aula', '#DD6E93', 'aula', 50),
    array('Repuestos del centro', '#D16A3A', 'repuestos', 60),
    array('Piscina', '#258EAF', 'piscina', 70),
);

$familyStatement = mysqli_prepare(
    $database,
    "INSERT INTO almacen_general_familias (nombre, color, icono, orden, activo)
     VALUES (?, ?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE nombre=VALUES(nombre)"
);
foreach ($families as $family) {
    mysqli_stmt_bind_param($familyStatement, 'sssi', $family[0], $family[1], $family[2], $family[3]);
    mysqli_stmt_execute($familyStatement);
}
mysqli_stmt_close($familyStatement);

$zones = array(
    array('Lápices · G.A.', '#21675A', 'almacen', 10),
    array('Piscina NeoKids', '#258EAF', 'piscina', 20),
);
$zoneStatement = mysqli_prepare(
    $database,
    "INSERT INTO almacen_general_zonas (nombre, color, icono, orden, activo)
     VALUES (?, ?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE nombre=VALUES(nombre)"
);
foreach ($zones as $zone) {
    mysqli_stmt_bind_param($zoneStatement, 'sssi', $zone[0], $zone[1], $zone[2], $zone[3]);
    mysqli_stmt_execute($zoneStatement);
}
mysqli_stmt_close($zoneStatement);

$zoneIds = array();
$zoneResult = mysqli_query($database, "SELECT id_zona, nombre FROM almacen_general_zonas");
while ($zoneResult && ($zoneRow = mysqli_fetch_assoc($zoneResult))) {
    $zoneIds[$zoneRow['nombre']] = (int) $zoneRow['id_zona'];
}

$locations = array(
    array('Lápices · G.A.', 'Almacén principal', 'GA-PRINCIPAL', 'Zona general de almacenamiento de Lápices.'),
    array('Piscina NeoKids', 'Almacén piscina', 'NK-PRINCIPAL', 'Zona general de almacenamiento de la piscina NeoKids.'),
);
$locationStatement = mysqli_prepare(
    $database,
    "INSERT INTO almacen_general_ubicaciones (id_zona, nombre, codigo, descripcion, activo)
     VALUES (?, ?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), descripcion=VALUES(descripcion)"
);
foreach ($locations as $location) {
    if (!isset($zoneIds[$location[0]])) {
        continue;
    }
    $zoneId = $zoneIds[$location[0]];
    mysqli_stmt_bind_param($locationStatement, 'isss', $zoneId, $location[1], $location[2], $location[3]);
    mysqli_stmt_execute($locationStatement);
}
mysqli_stmt_close($locationStatement);

echo "Almacén general preparado correctamente.\n";
