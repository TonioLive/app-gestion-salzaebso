CREATE TABLE IF NOT EXISTS material_pedidos (
    id_pedido BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo VARCHAR(32) NOT NULL DEFAULT '',
    id_centro INT UNSIGNED NOT NULL,
    id_curso INT UNSIGNED NOT NULL,
    centro_nombre VARCHAR(255) NOT NULL DEFAULT '',
    curso_nombre VARCHAR(100) NOT NULL DEFAULT '',
    token_peticion CHAR(64) NOT NULL,
    estado VARCHAR(24) NOT NULL DEFAULT 'pendiente_stock',
    total_lineas INT UNSIGNED NOT NULL DEFAULT 0,
    total_unidades DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_importe_coste DECIMAL(12,2) NOT NULL DEFAULT 0,
    creado_por INT UNSIGNED NOT NULL DEFAULT 0,
    creado_por_nombre VARCHAR(255) NOT NULL DEFAULT '',
    fecha_creacion DATETIME NOT NULL,
    fecha_stock DATETIME NULL,
    PRIMARY KEY (id_pedido),
    UNIQUE KEY uq_material_pedido_token (token_peticion),
    KEY idx_material_pedido_centro (id_centro, id_curso, fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS material_pedido_lineas (
    id_linea BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_pedido BIGINT UNSIGNED NOT NULL,
    id_material INT UNSIGNED NOT NULL,
    articulo VARCHAR(255) NOT NULL,
    categoria VARCHAR(255) NOT NULL DEFAULT '',
    destinatario VARCHAR(32) NOT NULL DEFAULT 'Unisex',
    talla INT UNSIGNED NOT NULL DEFAULT 0,
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 0,
    precio_coste DECIMAL(12,4) NOT NULL DEFAULT 0,
    precio_venta DECIMAL(12,4) NOT NULL DEFAULT 0,
    importe_coste DECIMAL(12,2) NOT NULL DEFAULT 0,
    id_movimiento_stock INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id_linea),
    UNIQUE KEY uq_material_pedido_articulo (id_pedido, id_material),
    KEY idx_material_pedido_lineas_material (id_material)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @material_order_line_column = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'movimientos_material_escuelas'
      AND COLUMN_NAME = 'id_linea_pedido'
);
SET @material_order_line_sql = IF(
    @material_order_line_column = 0,
    'ALTER TABLE movimientos_material_escuelas ADD COLUMN id_linea_pedido BIGINT UNSIGNED NULL',
    'SELECT 1'
);
PREPARE material_order_line_statement FROM @material_order_line_sql;
EXECUTE material_order_line_statement;
DEALLOCATE PREPARE material_order_line_statement;

SET @material_order_line_index = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'movimientos_material_escuelas'
      AND INDEX_NAME = 'uq_material_order_line'
);
SET @material_order_line_index_sql = IF(
    @material_order_line_index = 0,
    'ALTER TABLE movimientos_material_escuelas ADD UNIQUE KEY uq_material_order_line (id_linea_pedido)',
    'SELECT 1'
);
PREPARE material_order_line_index_statement FROM @material_order_line_index_sql;
EXECUTE material_order_line_index_statement;
DEALLOCATE PREPARE material_order_line_index_statement;
