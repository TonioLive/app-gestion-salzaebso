CREATE TABLE IF NOT EXISTS contactos_web_interesados (
    id_relacion BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    tabla_origen VARCHAR(32) NOT NULL,
    id_contacto_web INT UNSIGNED NOT NULL,
    id_contacto_interesado INT UNSIGNED NOT NULL,
    datos_origen LONGTEXT NULL,
    creado_por INT UNSIGNED NOT NULL DEFAULT 0,
    fecha_creacion DATETIME NOT NULL,
    PRIMARY KEY (id_relacion),
    UNIQUE KEY uq_contacto_web (tabla_origen, id_contacto_web),
    KEY idx_contacto_interesado (id_contacto_interesado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS material_previsiones (
    id_prevision BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_centro INT UNSIGNED NOT NULL,
    id_curso INT UNSIGNED NOT NULL,
    id_material INT UNSIGNED NOT NULL,
    cantidad_recomendada DECIMAL(10,2) NOT NULL DEFAULT 0,
    cantidad_planificada DECIMAL(10,2) NOT NULL DEFAULT 0,
    actualizado_por INT UNSIGNED NOT NULL DEFAULT 0,
    fecha_actualizacion DATETIME NOT NULL,
    PRIMARY KEY (id_prevision),
    UNIQUE KEY uq_prevision (id_centro, id_curso, id_material),
    KEY idx_prevision_curso (id_curso, id_centro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS material_entregas (
    id_entrega BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_centro INT UNSIGNED NOT NULL,
    id_curso INT UNSIGNED NOT NULL,
    id_alumno INT UNSIGNED NOT NULL,
    id_material INT UNSIGNED NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 1,
    movimiento_stock DECIMAL(10,2) NOT NULL DEFAULT 1,
    tipo_movimiento VARCHAR(20) NOT NULL DEFAULT 'entrega',
    estado VARCHAR(20) NOT NULL DEFAULT 'entregado',
    fecha_entrega DATETIME NOT NULL,
    entregado_por INT UNSIGNED NOT NULL DEFAULT 0,
    id_servicio_contratado INT UNSIGNED NOT NULL DEFAULT 0,
    id_factura INT UNSIGNED NOT NULL DEFAULT 0,
    id_detalle_factura INT UNSIGNED NOT NULL DEFAULT 0,
    id_pago INT UNSIGNED NOT NULL DEFAULT 0,
    id_entrega_origen BIGINT UNSIGNED NOT NULL DEFAULT 0,
    notas VARCHAR(500) NOT NULL DEFAULT '',
    PRIMARY KEY (id_entrega),
    KEY idx_entrega_centro_curso (id_centro, id_curso),
    KEY idx_entrega_alumno (id_alumno),
    KEY idx_entrega_material (id_material),
    KEY idx_entrega_factura (id_factura),
    KEY idx_entrega_origen (id_entrega_origen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS aulas_exclusiones_merienda (
    id_exclusion BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_centro INT UNSIGNED NOT NULL,
    id_curso INT UNSIGNED NOT NULL,
    id_aula INT UNSIGNED NOT NULL,
    fecha DATE NOT NULL,
    motivo VARCHAR(255) NOT NULL DEFAULT '',
    activa TINYINT(1) NOT NULL DEFAULT 1,
    creado_por INT UNSIGNED NOT NULL DEFAULT 0,
    fecha_actualizacion DATETIME NOT NULL,
    PRIMARY KEY (id_exclusion),
    UNIQUE KEY uq_exclusion_merienda (id_centro, id_curso, id_aula, fecha),
    KEY idx_exclusion_fecha (fecha, id_centro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
