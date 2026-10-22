CREATE TABLE IF NOT EXISTS usuarios_preferencias (
    id_usuario INT NOT NULL,
    color_principal CHAR(7) NOT NULL DEFAULT '#21675A',
    vista_inicio VARCHAR(20) NOT NULL DEFAULT 'clasica',
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

