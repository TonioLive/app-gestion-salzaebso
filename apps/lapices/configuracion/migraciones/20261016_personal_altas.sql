CREATE TABLE IF NOT EXISTS personal_altas (
 id_usuario INT NOT NULL PRIMARY KEY,
 creado_por INT NOT NULL,
 id_centro INT NOT NULL,
 pendiente TINYINT NOT NULL DEFAULT 1,
 creado DATETIME NOT NULL,
 completado DATETIME NULL,
 contrato_archivo VARCHAR(80) NOT NULL DEFAULT '',
 contrato_nombre VARCHAR(255) NOT NULL DEFAULT '',
 solicitud CHAR(64) NOT NULL,
 UNIQUE KEY solicitud_unica (solicitud)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
