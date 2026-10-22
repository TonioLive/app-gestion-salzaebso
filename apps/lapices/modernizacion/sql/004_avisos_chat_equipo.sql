CREATE TABLE IF NOT EXISTS comunicacion_equipo (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    centro_id INT NOT NULL,
    autor_id INT NOT NULL,
    texto TEXT NOT NULL,
    clave_cliente VARCHAR(64) NOT NULL,
    creado DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY envio_unico (centro_id, autor_id, clave_cliente),
    KEY centro_historial (centro_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comunicacion_equipo_lecturas (
    centro_id INT NOT NULL,
    usuario_id INT NOT NULL,
    ultimo_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (centro_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comunicacion_avisos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    centro_id INT NOT NULL,
    autor_id INT NOT NULL,
    titulo VARCHAR(160) NOT NULL,
    texto TEXT NOT NULL,
    clave_cliente VARCHAR(64) NOT NULL,
    creado DATETIME NOT NULL,
    archivado DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY aviso_unico (centro_id, autor_id, clave_cliente),
    KEY centro_tablon (centro_id, archivado, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comunicacion_avisos_lecturas (
    aviso_id BIGINT UNSIGNED NOT NULL,
    familiar_id INT NOT NULL,
    leido DATETIME NOT NULL,
    PRIMARY KEY (aviso_id, familiar_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
