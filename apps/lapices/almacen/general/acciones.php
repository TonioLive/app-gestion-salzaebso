<?php
require_once __DIR__ . '/bootstrap.php';
app_security_headers(true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Método no permitido.');
}

if (!app_verify_csrf((string) app_request_value($_POST, '_csrf', ''))) {
    http_response_code(403);
    exit('La sesión del formulario ha caducado. Recarga la página e inténtalo de nuevo.');
}

$action = (string) app_request_value($_POST, 'accion', '');
$userId = (int) $idUsuario;

if ($action === 'crear_familia' || $action === 'actualizar_familia') {
    $name = warehouse_text($_POST, 'nombre', 100, true);
    $color = warehouse_safe_color(app_request_value($_POST, 'color', ''));
    $icon = warehouse_safe_icon((string) app_request_value($_POST, 'icono', ''));
    $order = max(0, min(999, app_request_int($_POST, 'orden', 0)));
    $active = app_request_value($_POST, 'activo', '') === '1' ? 1 : 0;

    if ($name === false) {
        warehouse_flash('error', 'La familia necesita un nombre.');
        warehouse_redirect('organizacion');
    }

    if ($action === 'crear_familia') {
        $saved = warehouse_query(
            'INSERT INTO almacen_general_familias (nombre, color, icono, orden, activo, creado_por)
             VALUES (?, ?, ?, ?, 1, ?)',
            'sssii',
            array($name, $color, $icon, $order, $userId)
        );
        warehouse_flash($saved ? 'success' : 'error', $saved
            ? 'Familia creada correctamente.'
            : 'No se ha podido crear la familia. Comprueba que el nombre no esté repetido.');
    } else {
        $familyId = app_request_int($_POST, 'id_familia', 0);
        $saved = $familyId > 0 && warehouse_query(
            'UPDATE almacen_general_familias
             SET nombre=?, color=?, icono=?, orden=?, activo=?
             WHERE id_familia=?',
            'sssiii',
            array($name, $color, $icon, $order, $active, $familyId)
        );
        warehouse_flash($saved ? 'success' : 'error', $saved
            ? 'Familia actualizada.'
            : 'No se ha podido actualizar la familia.');
    }
    warehouse_redirect('organizacion');
}

if ($action === 'crear_zona') {
    $name = warehouse_text($_POST, 'nombre', 100, true);
    $color = warehouse_safe_color(app_request_value($_POST, 'color', ''));
    $icon = warehouse_safe_icon((string) app_request_value($_POST, 'icono', ''));
    if ($name === false) {
        warehouse_flash('error', 'La zona necesita un nombre.');
        warehouse_redirect('organizacion');
    }
    $saved = warehouse_query(
        'INSERT INTO almacen_general_zonas (nombre, color, icono, activo, creado_por)
         VALUES (?, ?, ?, 1, ?)',
        'sssi',
        array($name, $color, $icon, $userId)
    );
    warehouse_flash($saved ? 'success' : 'error', $saved
        ? 'Zona creada correctamente.'
        : 'No se ha podido crear la zona. Comprueba que no esté repetida.');
    warehouse_redirect('organizacion');
}

if ($action === 'crear_ubicacion') {
    $zoneId = app_request_int($_POST, 'id_zona', 0);
    $name = warehouse_text($_POST, 'nombre', 120, true);
    $code = warehouse_text($_POST, 'codigo', 50, true);
    $description = warehouse_text($_POST, 'descripcion', 255, false);
    if ($zoneId <= 0 || $name === false || $code === false) {
        warehouse_flash('error', 'Indica la zona, el nombre y el código de la ubicación.');
        warehouse_redirect('organizacion');
    }
    $saved = warehouse_query(
        'INSERT INTO almacen_general_ubicaciones
            (id_zona, nombre, codigo, descripcion, activo, creado_por)
         SELECT id_zona, ?, ?, ?, 1, ?
         FROM almacen_general_zonas WHERE id_zona=? AND activo=1',
        'sssii',
        array($name, strtoupper($code), $description, $userId, $zoneId)
    );
    warehouse_flash($saved && mysqli_affected_rows($warehouse_database) === 1 ? 'success' : 'error',
        $saved && mysqli_affected_rows($warehouse_database) === 1
            ? 'Ubicación creada correctamente.'
            : 'No se ha podido crear la ubicación. Revisa la zona y el código.');
    warehouse_redirect('organizacion');
}

if ($action === 'crear_articulo' || $action === 'actualizar_articulo') {
    $articleId = app_request_int($_POST, 'id_articulo', 0);
    $familyId = app_request_int($_POST, 'id_familia', 0);
    $locationId = app_request_int($_POST, 'id_ubicacion', 0);
    $name = warehouse_text($_POST, 'nombre', 150, true);
    $reference = warehouse_text($_POST, 'referencia', 80, false);
    $unit = warehouse_safe_unit(app_request_value($_POST, 'unidad', 'unidad'));
    $minimum = warehouse_decimal(app_request_value($_POST, 'stock_minimo', '0'));
    $description = warehouse_text($_POST, 'descripcion', 2000, false);
    $active = app_request_value($_POST, 'activo', '') === '1' ? 1 : 0;

    if ($familyId <= 0 || $locationId <= 0 || $name === false || $minimum === null || $minimum < 0) {
        warehouse_flash('error', 'Revisa el nombre, la familia, la ubicación y el stock mínimo.');
        warehouse_redirect('inventario');
    }

    $validRelation = warehouse_row(warehouse_query(
        'SELECT f.id_familia
         FROM almacen_general_familias f
         INNER JOIN almacen_general_ubicaciones u ON u.id_ubicacion=? AND u.activo=1
         WHERE f.id_familia=? AND f.activo=1 LIMIT 1',
        'ii',
        array($locationId, $familyId)
    ));
    if (!$validRelation) {
        warehouse_flash('error', 'La familia o la ubicación seleccionada ya no está disponible.');
        warehouse_redirect('inventario');
    }

    $referenceValue = $reference === '' ? null : strtoupper($reference);
    if ($action === 'crear_articulo') {
        $saved = warehouse_query(
            'INSERT INTO almacen_general_articulos
                (id_centro, id_familia, id_ubicacion, nombre, referencia, unidad, stock_minimo, descripcion, activo, creado_por)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)',
            'iiisssdsi',
            array(ALMACEN_GENERAL_CENTRO_ID, $familyId, $locationId, $name, $referenceValue, $unit, $minimum, $description, $userId)
        );
        if (!$saved) {
            warehouse_flash('error', 'No se ha podido crear el artículo. Comprueba que la referencia no esté repetida.');
            warehouse_redirect('inventario');
        }
        $articleId = (int) mysqli_insert_id($warehouse_database);
        $initial = warehouse_decimal(app_request_value($_POST, 'stock_inicial', '0'));
        if ($initial !== null && $initial > 0) {
            list($movementSaved, $movementResult) = warehouse_record_movement(
                $articleId,
                'ENTRADA',
                $initial,
                'Stock inicial',
                '',
                $userId
            );
            if (!$movementSaved) {
                warehouse_flash('warning', 'El artículo se creó, pero no se pudo registrar el stock inicial.');
                warehouse_redirect('inventario');
            }
        }
        warehouse_flash('success', 'Artículo creado y listo para gestionar.');
    } else {
        $saved = $articleId > 0 && warehouse_query(
            'UPDATE almacen_general_articulos
             SET id_familia=?, id_ubicacion=?, nombre=?, referencia=?, unidad=?, stock_minimo=?, descripcion=?, activo=?
             WHERE id_articulo=? AND id_centro=?',
            'iisssdsiii',
            array($familyId, $locationId, $name, $referenceValue, $unit, $minimum, $description, $active, $articleId, ALMACEN_GENERAL_CENTRO_ID)
        );
        warehouse_flash($saved ? 'success' : 'error', $saved
            ? 'Artículo actualizado correctamente.'
            : 'No se ha podido actualizar el artículo. Comprueba la referencia.');
    }
    warehouse_redirect('inventario');
}

if ($action === 'movimiento') {
    $articleId = app_request_int($_POST, 'id_articulo', 0);
    $type = strtoupper((string) app_request_value($_POST, 'tipo', ''));
    $amount = warehouse_decimal(app_request_value($_POST, 'cantidad', ''));
    $reason = warehouse_text($_POST, 'motivo', 150, true);
    $notes = warehouse_text($_POST, 'observaciones', 500, false);

    if ($articleId <= 0 || $reason === false) {
        warehouse_flash('error', 'Selecciona un artículo e indica el motivo del movimiento.');
        warehouse_redirect('inventario');
    }
    list($saved, $result) = warehouse_record_movement(
        $articleId,
        $type,
        $amount,
        $reason,
        $notes,
        $userId
    );
    warehouse_flash($saved ? 'success' : 'error', $saved
        ? 'Movimiento guardado. El nuevo stock es ' . number_format((float) $result, 3, ',', '.') . '.'
        : $result);
    warehouse_redirect('inventario');
}

http_response_code(400);
exit('Acción no válida.');
