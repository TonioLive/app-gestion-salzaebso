CREATE TABLE IF NOT EXISTS material_catalogo_retirado (
    id_material INT UNSIGNED NOT NULL,
    articulo VARCHAR(255) NOT NULL,
    motivo VARCHAR(255) NOT NULL DEFAULT '',
    documento_origen VARCHAR(255) NOT NULL DEFAULT '',
    fecha_retirada DATETIME NOT NULL,
    retirado_por INT UNSIGNED NOT NULL DEFAULT 0,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_material),
    KEY idx_material_retirado_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO material_catalogo_retirado (
    id_material,
    articulo,
    motivo,
    documento_origen,
    fecha_retirada,
    retirado_por,
    activo
)
SELECT
    tarifa.ID,
    tarifa.ARTICULO,
    'Artículo tachado en la revisión del catálogo de material',
    'doc00173020260730161527.pdf',
    NOW(),
    0,
    1
FROM tarifa
WHERE tarifa.ID IN (
    468, 469, 470, 471, 472, 2600,
    1743, 1744, 1745, 1746, 1747, 1748, 1749, 1750,
    1751, 1752, 1753,
    2932, 476, 477, 478, 480, 1754,
    2170, 2171, 2172, 2173, 2174, 2175, 2176, 2177,
    2178, 2179, 2180, 2181, 2182, 2183, 2184,
    1268, 1277, 1270, 1267, 1278, 1273, 1266, 1276,
    1272, 1269, 1280, 1274, 1265, 1275, 1271,
    1525, 1524, 1523, 1522,
    481, 482, 483, 484, 485, 2663,
    573, 572, 571, 570, 569, 568, 567
);

UPDATE material_catalogo_retirado
SET
    activo = 1,
    motivo = 'Artículo tachado en la revisión del catálogo de material',
    documento_origen = 'doc00173020260730161527.pdf'
WHERE id_material IN (
    468, 469, 470, 471, 472, 2600,
    1743, 1744, 1745, 1746, 1747, 1748, 1749, 1750,
    1751, 1752, 1753,
    2932, 476, 477, 478, 480, 1754,
    2170, 2171, 2172, 2173, 2174, 2175, 2176, 2177,
    2178, 2179, 2180, 2181, 2182, 2183, 2184,
    1268, 1277, 1270, 1267, 1278, 1273, 1266, 1276,
    1272, 1269, 1280, 1274, 1265, 1275, 1271,
    1525, 1524, 1523, 1522,
    481, 482, 483, 484, 485, 2663,
    573, 572, 571, 570, 569, 568, 567
);
