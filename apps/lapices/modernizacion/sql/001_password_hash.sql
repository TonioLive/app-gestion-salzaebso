-- Ejecutar en staging y con backup verificado antes de producción.
-- Conserva temporalmente la codificación histórica; password_hash genera ASCII.

ALTER TABLE usuarios
    MODIFY COLUMN pass VARCHAR(255)
    CHARACTER SET latin1
    COLLATE latin1_general_ci
    NULL DEFAULT NULL;

