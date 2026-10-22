-- Auditoría de solo lectura. Ejecutar primero sobre una copia o staging.

SELECT VERSION() AS server_version;

SELECT @@GLOBAL.sql_mode AS global_sql_mode,
       @@SESSION.sql_mode AS session_sql_mode,
       @@character_set_server AS server_charset,
       @@collation_server AS server_collation;

SELECT ENGINE,
       COUNT(*) AS table_count,
       ROUND(SUM(DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_TYPE = 'BASE TABLE'
GROUP BY ENGINE
ORDER BY total_mb DESC;

SELECT TABLE_NAME,
       ENGINE,
       TABLE_COLLATION,
       ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_TYPE = 'BASE TABLE'
ORDER BY DATA_LENGTH + INDEX_LENGTH DESC
LIMIT 30;

SELECT TABLE_NAME,
       COLUMN_NAME,
       COLUMN_TYPE,
       COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND DATA_TYPE IN ('date', 'datetime', 'timestamp')
  AND (
      COLUMN_DEFAULT = '0000-00-00'
      OR COLUMN_DEFAULT LIKE '0000-00-00%'
  )
ORDER BY TABLE_NAME, ORDINAL_POSITION;

SELECT table_list.TABLE_NAME
FROM (
    SELECT TABLE_NAME
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_TYPE = 'BASE TABLE'
) AS table_list
LEFT JOIN (
    SELECT DISTINCT TABLE_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND CONSTRAINT_NAME = 'PRIMARY'
) AS primary_keys
    ON primary_keys.TABLE_NAME = table_list.TABLE_NAME
WHERE primary_keys.TABLE_NAME IS NULL
ORDER BY table_list.TABLE_NAME;

SELECT TABLE_NAME,
       COLUMN_NAME,
       CHARACTER_SET_NAME,
       COLLATION_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND CHARACTER_SET_NAME IS NOT NULL
  AND CHARACTER_SET_NAME NOT IN ('utf8mb4', 'ascii')
ORDER BY TABLE_NAME, ORDINAL_POSITION;

