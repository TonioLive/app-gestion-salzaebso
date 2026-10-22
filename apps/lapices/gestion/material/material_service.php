<?php

function material_fetch_all($result)
{
    $rows = array();
    if (!($result instanceof mysqli_result)) {
        return $rows;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    return $rows;
}

function material_default_course($database)
{
    $today = date('Y-m-d');
    $statement = mysqli_prepare(
        $database,
        'SELECT id_curso FROM cursos_academicos '
        . 'WHERE fecha_inicio<=? AND fecha_fin>=? '
        . 'ORDER BY fecha_inicio DESC LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'ss', $today, $today);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $course_id);
    $found = mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    if ($found) {
        return (int) $course_id;
    }

    $result = mysqli_query(
        $database,
        "SELECT id_curso FROM cursos_academicos "
        . "WHERE fecha_inicio>'" . mysqli_real_escape_string($database, $today) . "' "
        . 'ORDER BY fecha_inicio LIMIT 1'
    );
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    return $row ? (int) $row['id_curso'] : 0;
}

function material_courses($database)
{
    return material_fetch_all(mysqli_query(
        $database,
        'SELECT id_curso, nombre, fecha_inicio, fecha_fin '
        . 'FROM cursos_academicos '
        . 'WHERE fecha_fin>=DATE_SUB(CURDATE(), INTERVAL 2 YEAR) '
        . 'ORDER BY fecha_inicio DESC'
    ));
}

function material_course($database, $course_id)
{
    $course_id = (int) $course_id;
    $result = mysqli_query(
        $database,
        'SELECT id_curso, nombre, fecha_inicio, fecha_fin '
        . 'FROM cursos_academicos WHERE id_curso=' . $course_id . ' LIMIT 1'
    );
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    return $row;
}

function material_pupils($database, $centre_id, $course_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT a.id_alumno, a.id_usuario, a.aula, '
        . 'u.Nombre, u.Apellido1, u.Apellido2, u.sexo, u.fecha_nacimiento, '
        . "COALESCE(ca.nombre, 'Sin aula') AS nombre_aula "
        . 'FROM alumnos a '
        . 'INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
        . 'LEFT JOIN centro_aulas ca ON ca.id_aula=a.aula '
        . 'WHERE a.id_centro=? AND a.id_curso=? '
        . "AND (a.fecha_baja IS NULL OR a.fecha_baja='0000-00-00' "
        . 'OR a.fecha_baja>=CURDATE()) '
        . 'ORDER BY ca.nombre, u.Apellido1, u.Apellido2, u.Nombre'
    );
    mysqli_stmt_bind_param($statement, 'ii', $centre_id, $course_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = material_fetch_all($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function material_normalized_text($value)
{
    $value = function_exists('mb_strtolower')
        ? mb_strtolower((string) $value, 'UTF-8')
        : strtolower((string) $value);
    $value = strtr($value, array(
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ñ' => 'n',
    ));
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($converted !== false) {
            $value = $converted;
        }
    }
    return $value;
}

function material_product_meta($name, $category)
{
    $normalized = material_normalized_text($name);
    $normalized_category = material_normalized_text($category);
    $is_uniform = strpos($normalized, 'uniform') !== false
        || preg_match(
            '/\b(babero|bata|camiseta|polo|pantalon|falda|chandal|'
            . 'sudadera|chaqueta|jersey|vestido)\b/',
            $normalized
        );
    $is_staff = strpos($normalized, 'educador') !== false;
    $gender = 'neutral';
    if (
        preg_match('/\b(chico|nino|varon|boy)\b/', $normalized)
        || strpos($normalized, 'mascul') !== false
    ) {
        $gender = 'H';
    } elseif (
        preg_match('/\b(nena|nina|chica|girl)\b/', $normalized)
        || strpos($normalized, 'femen') !== false
    ) {
        $gender = 'M';
    }

    $size = 0;
    if (preg_match('/(?:talla|t\.|t:)\s*([0-9]{1,2})\b/i', $normalized, $match)) {
        $size = (int) $match[1];
    }

    return array(
        'is_uniform' => $is_uniform,
        'is_staff' => $is_staff,
        'gender' => $gender,
        'size' => $size,
    );
}

function material_product_image($name, $category)
{
    $normalized = material_normalized_text($name);
    $normalized_category = material_normalized_text($category);
    $base_path = '/gestion/material/assets/images/';
    $file = 'material-didactico-lapices.webp';
    $kind = 'brand';
    $alt = 'Imagen corporativa de Lápices';
    $meta = material_product_meta($name, $category);

    if (strpos($normalized_category, 'material didactico alumnos') !== false) {
        $alt = 'Lápices · material didáctico para alumnos';
    } elseif (strpos($normalized, 'babero') !== false) {
        $file = 'babero-bebe-amarillo.webp';
        $kind = 'product';
        $alt = 'Babero infantil amarillo de Lápices';
    } elseif (strpos($normalized, 'chandal') !== false) {
        $file = 'chandal-azul-invierno.webp';
        $kind = 'product';
        $alt = 'Chándal azul de invierno de Lápices';
    } elseif (
        strpos($normalized, 'macuto lapices') !== false
        || strpos($normalized, 'mochila') !== false
    ) {
        $file = 'mochila-azul-lapices.webp';
        $kind = 'product';
        $alt = 'Mochila azul de Lápices';
    } elseif (
        $meta['gender'] === 'M'
        || preg_match('/\b(pichi|falda|vestido)\b/', $normalized)
    ) {
        $file = 'uniforme-azul-verano-nina.webp';
        $kind = 'product';
        $alt = 'Uniforme azul de verano para niña de Lápices';
    } elseif (
        $meta['gender'] === 'H'
        || strpos($normalized, 'uniform') !== false
    ) {
        $file = 'uniforme-azul-verano-nino.webp';
        $kind = 'product';
        $alt = 'Uniforme azul de verano para niño de Lápices';
    }

    return array(
        'src' => $base_path . $file,
        'kind' => $kind,
        'alt' => $alt,
    );
}

function material_pupil_gender($value)
{
    $normalized = material_normalized_text($value);
    if (
        $normalized === 'v'
        || strpos($normalized, 'var') === 0
        || strpos($normalized, 'masc') === 0
        || strpos($normalized, 'nino') === 0
        || strpos($normalized, 'chico') === 0
    ) {
        return 'H';
    }
    if (
        $normalized === 'h'
        || $normalized === 'm'
        || strpos($normalized, 'hemb') === 0
        || strpos($normalized, 'fem') === 0
        || strpos($normalized, 'nina') === 0
        || strpos($normalized, 'nena') === 0
        || strpos($normalized, 'chica') === 0
    ) {
        return 'M';
    }
    return '';
}

function material_pupil_size($birth_date, $reference_date)
{
    if (
        !$birth_date
        || $birth_date === '0000-00-00'
        || strtotime($birth_date) === false
    ) {
        return 0;
    }
    $birth = new DateTime($birth_date);
    $reference = new DateTime($reference_date);
    if ($birth > $reference) {
        return 1;
    }
    $age = (int) $birth->diff($reference)->y;
    return max(1, min(12, $age + 1));
}

function material_estimate($product, $pupils, $course)
{
    $meta = material_product_meta(
        $product['ARTICULO'],
        isset($product['categoria']) ? $product['categoria'] : ''
    );
    $matching = 0;
    if (
        $meta['is_uniform']
        && !$meta['is_staff']
        && $meta['size'] > 0
    ) {
        foreach ($pupils as $pupil) {
            $pupil_gender = material_pupil_gender($pupil['sexo']);
            $pupil_size = material_pupil_size(
                $pupil['fecha_nacimiento'],
                $course['fecha_inicio']
            );
            if (
                $pupil_size === $meta['size']
                && (
                    $meta['gender'] === 'neutral'
                    || $meta['gender'] === $pupil_gender
                )
            ) {
                $matching++;
            }
        }
    }

    return array(
        'base' => $matching,
        'recommended' => $matching > 0 ? (int) ceil($matching * 1.10) : 0,
        'meta' => $meta,
    );
}

function material_products($database, $centre_id, $course_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT t.ID AS id_material, t.ARTICULO, t.PRECIO_VENTA, '
        . 't.PRECIO_COSTE, t.id_curso, t.STOCK_MINIMO, '
        . 'iva.categoria, iva.porcentaje_iva, '
        . 'COALESCE(m.stock_escuela,0)-COALESCE(e.stock_entregado,0) AS stock_actual, '
        . 'COALESCE(p.cantidad_planificada,0) AS cantidad_planificada '
        . 'FROM tarifa t '
        . 'INNER JOIN tipos_iva_servicios iva '
        . 'ON iva.id_tipo_iva=t.CATEGORIA '
        . 'LEFT JOIN ('
        . ' SELECT id_material, SUM(cantidad) AS stock_escuela '
        . ' FROM movimientos_material_escuelas '
        . ' WHERE id_escuela=? GROUP BY id_material'
        . ') m ON m.id_material=t.ID '
        . 'LEFT JOIN ('
        . ' SELECT id_material, SUM(movimiento_stock) AS stock_entregado '
        . ' FROM material_entregas '
        . ' WHERE id_centro=? GROUP BY id_material'
        . ') e ON e.id_material=t.ID '
        . 'LEFT JOIN material_previsiones p '
        . 'ON p.id_material=t.ID AND p.id_centro=? AND p.id_curso=? '
        . "WHERE iva.categoria LIKE 'Material%' "
        . 'AND iva.tipo_servicios=0 '
        . 'AND NOT EXISTS ('
        . ' SELECT 1 FROM material_catalogo_retirado retirado '
        . ' WHERE retirado.id_material=t.ID AND retirado.activo=1'
        . ') '
        . 'AND (t.id_curso=0 OR t.id_curso=?) '
        . 'ORDER BY iva.categoria, t.ARTICULO'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iiiii',
        $centre_id,
        $centre_id,
        $centre_id,
        $course_id,
        $course_id
    );
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = material_fetch_all($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function material_product($database, $product_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT t.ID AS id_material, t.ARTICULO, t.PRECIO_VENTA, '
        . 't.PRECIO_COSTE, t.id_curso, t.desglosar_como, '
        . 't.SUBVENCION, iva.categoria, iva.porcentaje_iva '
        . 'FROM tarifa t INNER JOIN tipos_iva_servicios iva '
        . 'ON iva.id_tipo_iva=t.CATEGORIA '
        . "WHERE t.ID=? AND iva.categoria LIKE 'Material%' "
        . 'AND NOT EXISTS ('
        . ' SELECT 1 FROM material_catalogo_retirado retirado '
        . ' WHERE retirado.id_material=t.ID AND retirado.activo=1'
        . ') '
        . 'AND iva.tipo_servicios=0 LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'i', $product_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);
    return $row;
}

function material_stock($database, $centre_id, $product_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT '
        . 'COALESCE((SELECT SUM(cantidad) FROM movimientos_material_escuelas '
        . 'WHERE id_escuela=? AND id_material=?),0) '
        . '- COALESCE((SELECT SUM(movimiento_stock) FROM material_entregas '
        . 'WHERE id_centro=? AND id_material=?),0) AS stock_actual'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iiii',
        $centre_id,
        $product_id,
        $centre_id,
        $product_id
    );
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $stock);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    return (float) $stock;
}

function material_save_forecast(
    $database,
    $centre_id,
    $course_id,
    $product_id,
    $recommended,
    $planned,
    $user_id
) {
    $updated = date('Y-m-d H:i:s');
    $statement = mysqli_prepare(
        $database,
        'INSERT INTO material_previsiones '
        . '(id_centro,id_curso,id_material,cantidad_recomendada,'
        . 'cantidad_planificada,actualizado_por,fecha_actualizacion) '
        . 'VALUES (?,?,?,?,?,?,?) '
        . 'ON DUPLICATE KEY UPDATE '
        . 'cantidad_recomendada=VALUES(cantidad_recomendada), '
        . 'cantidad_planificada=VALUES(cantidad_planificada), '
        . 'actualizado_por=VALUES(actualizado_por), '
        . 'fecha_actualizacion=VALUES(fecha_actualizacion)'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iiiddis',
        $centre_id,
        $course_id,
        $product_id,
        $recommended,
        $planned,
        $user_id,
        $updated
    );
    $ok = mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    return $ok;
}

function material_centre($database, $centre_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT id_centro, nombre_centro, nombre_completo '
        . 'FROM centros WHERE id_centro=? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'i', $centre_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);
    return $row;
}

function material_random_token()
{
    if (function_exists('random_bytes')) {
        try {
            return bin2hex(random_bytes(32));
        } catch (Exception $exception) {
            // Continue with the OpenSSL fallback used by PHP 5.6.
        }
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        $bytes = openssl_random_pseudo_bytes(32);
        if ($bytes !== false) {
            return bin2hex($bytes);
        }
    }
    return hash('sha256', uniqid((string) mt_rand(), true));
}

function material_order_request_token($centre_id, $course_id)
{
    $key = 'material_order_token_' . (int) $centre_id . '_' . (int) $course_id;
    if (
        !isset($_SESSION[$key])
        || !preg_match('/^[a-f0-9]{64}$/', (string) $_SESSION[$key])
    ) {
        $_SESSION[$key] = material_random_token();
    }
    return (string) $_SESSION[$key];
}

function material_clear_order_request_token($centre_id, $course_id)
{
    $key = 'material_order_token_' . (int) $centre_id . '_' . (int) $course_id;
    unset($_SESSION[$key]);
}

function material_order_recipient($name, $category)
{
    $meta = material_product_meta($name, $category);
    if ($meta['gender'] === 'H') {
        return 'Nino';
    }
    if ($meta['gender'] === 'M') {
        return 'Nina';
    }
    return 'Unisex';
}

function material_pending_order_lines($database, $centre_id, $course_id, $lock)
{
    $sql = 'SELECT p.id_prevision, p.id_material, p.cantidad_planificada, '
        . 't.ARTICULO, t.PRECIO_COSTE, t.PRECIO_VENTA, '
        . 'iva.categoria '
        . 'FROM material_previsiones p '
        . 'INNER JOIN tarifa t ON t.ID=p.id_material '
        . 'INNER JOIN tipos_iva_servicios iva ON iva.id_tipo_iva=t.CATEGORIA '
        . 'WHERE p.id_centro=? AND p.id_curso=? '
        . 'AND p.cantidad_planificada>0 '
        . "AND iva.categoria LIKE 'Material%' "
        . 'AND iva.tipo_servicios=0 '
        . 'AND NOT EXISTS ('
        . ' SELECT 1 FROM material_catalogo_retirado retirado '
        . ' WHERE retirado.id_material=p.id_material AND retirado.activo=1'
        . ') '
        . 'ORDER BY iva.categoria, t.ARTICULO';
    if ($lock) {
        $sql .= ' FOR UPDATE';
    }
    $statement = mysqli_prepare($database, $sql);
    mysqli_stmt_bind_param($statement, 'ii', $centre_id, $course_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = material_fetch_all($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function material_order_by_token($database, $token)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT * FROM material_pedidos WHERE token_peticion=? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 's', $token);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);
    return $row;
}

function material_pending_order($database, $centre_id, $course_id)
{
    $status = 'pendiente_stock';
    $statement = mysqli_prepare(
        $database,
        'SELECT * FROM material_pedidos '
        . 'WHERE id_centro=? AND id_curso=? AND estado=? '
        . 'ORDER BY id_pedido LIMIT 1'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iis',
        $centre_id,
        $course_id,
        $status
    );
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);
    return $row;
}

function material_order($database, $order_id, $centre_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT * FROM material_pedidos '
        . 'WHERE id_pedido=? AND id_centro=? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'ii', $order_id, $centre_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);
    return $row;
}

function material_order_lines($database, $order_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT * FROM material_pedido_lineas '
        . 'WHERE id_pedido=? ORDER BY categoria, articulo'
    );
    mysqli_stmt_bind_param($statement, 'i', $order_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = material_fetch_all($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function material_recent_orders($database, $centre_id, $limit)
{
    $limit = max(1, min(20, (int) $limit));
    $statement = mysqli_prepare(
        $database,
        'SELECT id_pedido, codigo, curso_nombre, estado, total_lineas, '
        . 'total_unidades, total_importe_coste, creado_por_nombre, '
        . 'fecha_creacion, fecha_stock '
        . 'FROM material_pedidos WHERE id_centro=? '
        . 'ORDER BY id_pedido DESC LIMIT ' . $limit
    );
    mysqli_stmt_bind_param($statement, 'i', $centre_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = material_fetch_all($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function material_apply_order_stock($database, $order_id, $user_id)
{
    $order = material_order($database, $order_id, (int) material_order_centre_id(
        $database,
        $order_id
    ));
    if (!$order) {
        return array('ok' => false, 'error' => 'No se encuentra el pedido.');
    }
    if ($order['estado'] === 'stock_actualizado') {
        return array('ok' => true, 'order' => $order);
    }

    $lines = material_order_lines($database, $order_id);
    if (!$lines) {
        return array('ok' => false, 'error' => 'El pedido no contiene líneas.');
    }

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $concept = 'Pedido material ' . $order['codigo'];

    foreach ($lines as $line) {
        $line_id = (int) $line['id_linea'];
        $movement_statement = mysqli_prepare(
            $database,
            'SELECT id_mov FROM movimientos_material_escuelas '
            . 'WHERE id_linea_pedido=? LIMIT 1'
        );
        mysqli_stmt_bind_param($movement_statement, 'i', $line_id);
        mysqli_stmt_execute($movement_statement);
        mysqli_stmt_bind_result($movement_statement, $movement_id);
        $movement_exists = mysqli_stmt_fetch($movement_statement);
        mysqli_stmt_close($movement_statement);

        if (!$movement_exists) {
            $product_id = (int) $line['id_material'];
            $centre_id = (int) $order['id_centro'];
            $quantity = (float) $line['cantidad'];
            $insert_statement = mysqli_prepare(
                $database,
                'INSERT INTO movimientos_material_escuelas '
                . '(fecha,hora,id_material,id_escuela,cantidad,concepto,'
                . 'id_linea_pedido) VALUES (?,?,?,?,?,?,?)'
            );
            mysqli_stmt_bind_param(
                $insert_statement,
                'ssiidsi',
                $date,
                $time,
                $product_id,
                $centre_id,
                $quantity,
                $concept,
                $line_id
            );
            $inserted = mysqli_stmt_execute($insert_statement);
            $movement_id = $inserted ? (int) mysqli_insert_id($database) : 0;
            mysqli_stmt_close($insert_statement);
            if (!$inserted || $movement_id <= 0) {
                return array(
                    'ok' => false,
                    'error' => 'No se ha podido sumar al stock la línea '
                        . $line['articulo'] . '.',
                );
            }
        }

        $line_statement = mysqli_prepare(
            $database,
            'UPDATE material_pedido_lineas SET id_movimiento_stock=? '
            . 'WHERE id_linea=?'
        );
        mysqli_stmt_bind_param(
            $line_statement,
            'ii',
            $movement_id,
            $line_id
        );
        mysqli_stmt_execute($line_statement);
        mysqli_stmt_close($line_statement);
    }

    mysqli_begin_transaction($database);
    try {
        $updated = date('Y-m-d H:i:s');
        foreach ($lines as $line) {
            $centre_id = (int) $order['id_centro'];
            $course_id = (int) $order['id_curso'];
            $product_id = (int) $line['id_material'];
            $quantity = (float) $line['cantidad'];
            $forecast_statement = mysqli_prepare(
                $database,
                'UPDATE material_previsiones '
                . 'SET cantidad_planificada=0, actualizado_por=?, '
                . 'fecha_actualizacion=? '
                . 'WHERE id_centro=? AND id_curso=? AND id_material=? '
                . 'AND ABS(cantidad_planificada-?)<0.001'
            );
            mysqli_stmt_bind_param(
                $forecast_statement,
                'isiiid',
                $user_id,
                $updated,
                $centre_id,
                $course_id,
                $product_id,
                $quantity
            );
            if (!mysqli_stmt_execute($forecast_statement)) {
                mysqli_stmt_close($forecast_statement);
                throw new Exception('No se ha podido cerrar la previsión.');
            }
            mysqli_stmt_close($forecast_statement);
        }

        $status = 'stock_actualizado';
        $order_statement = mysqli_prepare(
            $database,
            'UPDATE material_pedidos '
            . 'SET estado=?, fecha_stock=? WHERE id_pedido=?'
        );
        mysqli_stmt_bind_param(
            $order_statement,
            'ssi',
            $status,
            $updated,
            $order_id
        );
        if (!mysqli_stmt_execute($order_statement)) {
            mysqli_stmt_close($order_statement);
            throw new Exception('No se ha podido completar el pedido.');
        }
        mysqli_stmt_close($order_statement);
        mysqli_commit($database);
    } catch (Exception $exception) {
        mysqli_rollback($database);
        return array('ok' => false, 'error' => $exception->getMessage());
    }

    return array(
        'ok' => true,
        'order' => material_order($database, $order_id, (int) $order['id_centro']),
    );
}

function material_order_centre_id($database, $order_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT id_centro FROM material_pedidos WHERE id_pedido=? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'i', $order_id);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $centre_id);
    $found = mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    return $found ? (int) $centre_id : 0;
}

function material_create_order(
    $database,
    $centre_id,
    $course_id,
    $user_id,
    $user_name,
    $token
) {
    if (!preg_match('/^[a-f0-9]{64}$/', (string) $token)) {
        return array('ok' => false, 'error' => 'La petición del pedido no es válida.');
    }

    $existing = material_order_by_token($database, $token);
    if ($existing) {
        if (
            (int) $existing['id_centro'] !== (int) $centre_id
            || (int) $existing['id_curso'] !== (int) $course_id
        ) {
            return array('ok' => false, 'error' => 'La petición no corresponde a este centro.');
        }
        return material_apply_order_stock(
            $database,
            (int) $existing['id_pedido'],
            $user_id
        );
    }

    $pending = material_pending_order($database, $centre_id, $course_id);
    if ($pending) {
        return material_apply_order_stock(
            $database,
            (int) $pending['id_pedido'],
            $user_id
        );
    }

    $centre = material_centre($database, $centre_id);
    $course = material_course($database, $course_id);
    if (!$centre || !$course) {
        return array('ok' => false, 'error' => 'No se encuentra el centro o el curso.');
    }

    mysqli_begin_transaction($database);
    try {
        $lines = material_pending_order_lines(
            $database,
            $centre_id,
            $course_id,
            true
        );
        if (!$lines) {
            throw new Exception('No hay cantidades planificadas para generar el pedido.');
        }

        $total_units = 0;
        $total_cost = 0;
        foreach ($lines as $line) {
            $quantity = (float) $line['cantidad_planificada'];
            $cost = (float) $line['PRECIO_COSTE'];
            $total_units += $quantity;
            $total_cost += $quantity * $cost;
        }

        $centre_name = trim(
            (string) (
                $centre['nombre_completo']
                    ? $centre['nombre_completo']
                    : $centre['nombre_centro']
            )
        );
        $course_name = (string) $course['nombre'];
        $status = 'pendiente_stock';
        $line_count = count($lines);
        $created = date('Y-m-d H:i:s');
        $header_statement = mysqli_prepare(
            $database,
            'INSERT INTO material_pedidos '
            . '(id_centro,id_curso,centro_nombre,curso_nombre,token_peticion,'
            . 'estado,total_lineas,total_unidades,total_importe_coste,'
            . 'creado_por,creado_por_nombre,fecha_creacion) '
            . 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        mysqli_stmt_bind_param(
            $header_statement,
            'iissssiddiss',
            $centre_id,
            $course_id,
            $centre_name,
            $course_name,
            $token,
            $status,
            $line_count,
            $total_units,
            $total_cost,
            $user_id,
            $user_name,
            $created
        );
        if (!mysqli_stmt_execute($header_statement)) {
            mysqli_stmt_close($header_statement);
            throw new Exception('No se ha podido crear la cabecera del pedido.');
        }
        $order_id = (int) mysqli_insert_id($database);
        mysqli_stmt_close($header_statement);

        $code = 'PM-' . date('Y') . '-' . str_pad($order_id, 6, '0', STR_PAD_LEFT);
        $code_statement = mysqli_prepare(
            $database,
            'UPDATE material_pedidos SET codigo=? WHERE id_pedido=?'
        );
        mysqli_stmt_bind_param($code_statement, 'si', $code, $order_id);
        if (!mysqli_stmt_execute($code_statement)) {
            mysqli_stmt_close($code_statement);
            throw new Exception('No se ha podido numerar el pedido.');
        }
        mysqli_stmt_close($code_statement);

        $line_statement = mysqli_prepare(
            $database,
            'INSERT INTO material_pedido_lineas '
            . '(id_pedido,id_material,articulo,categoria,destinatario,talla,'
            . 'cantidad,precio_coste,precio_venta,importe_coste) '
            . 'VALUES (?,?,?,?,?,?,?,?,?,?)'
        );
        foreach ($lines as $line) {
            $product_id = (int) $line['id_material'];
            $article = (string) $line['ARTICULO'];
            $category = (string) $line['categoria'];
            $recipient = material_order_recipient($article, $category);
            $meta = material_product_meta($article, $category);
            $size = (int) $meta['size'];
            $quantity = (float) $line['cantidad_planificada'];
            $cost = (float) $line['PRECIO_COSTE'];
            $sale = (float) $line['PRECIO_VENTA'];
            $line_cost = round($quantity * $cost, 2);
            mysqli_stmt_bind_param(
                $line_statement,
                'iisssidddd',
                $order_id,
                $product_id,
                $article,
                $category,
                $recipient,
                $size,
                $quantity,
                $cost,
                $sale,
                $line_cost
            );
            if (!mysqli_stmt_execute($line_statement)) {
                mysqli_stmt_close($line_statement);
                throw new Exception('No se ha podido guardar el desglose del pedido.');
            }
        }
        mysqli_stmt_close($line_statement);
        mysqli_commit($database);
    } catch (Exception $exception) {
        mysqli_rollback($database);
        return array('ok' => false, 'error' => $exception->getMessage());
    }

    return material_apply_order_stock($database, $order_id, $user_id);
}

function material_create_service(
    $database,
    $student_id,
    $product_id,
    $quantity,
    $date
) {
    $statement = mysqli_prepare(
        $database,
        'INSERT INTO servicios_contratados '
        . '(id_alumno,id_servicio,fecha_inicio,periodicidad,cantidad) '
        . 'VALUES (?,?,?,2,?)'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iisd',
        $student_id,
        $product_id,
        $date,
        $quantity
    );
    $ok = mysqli_stmt_execute($statement);
    $service_id = $ok ? (int) mysqli_insert_id($database) : 0;
    mysqli_stmt_close($statement);
    return $service_id;
}

function material_insert_delivery($database, $data)
{
    $centre_id = (int) $data['id_centro'];
    $course_id = (int) $data['id_curso'];
    $student_id = (int) $data['id_alumno'];
    $product_id = (int) $data['id_material'];
    $quantity = (float) $data['cantidad'];
    $stock_movement = (float) $data['movimiento_stock'];
    $movement_type = (string) $data['tipo_movimiento'];
    $status = (string) $data['estado'];
    $delivery_date = (string) $data['fecha_entrega'];
    $manager_id = (int) $data['entregado_por'];
    $service_id = (int) $data['id_servicio_contratado'];
    $invoice_id = (int) $data['id_factura'];
    $invoice_detail_id = (int) $data['id_detalle_factura'];
    $payment_id = (int) $data['id_pago'];
    $origin_delivery_id = (int) $data['id_entrega_origen'];
    $notes = (string) $data['notas'];
    $statement = mysqli_prepare(
        $database,
        'INSERT INTO material_entregas '
        . '(id_centro,id_curso,id_alumno,id_material,cantidad,movimiento_stock,'
        . 'tipo_movimiento,estado,fecha_entrega,entregado_por,'
        . 'id_servicio_contratado,id_factura,id_detalle_factura,id_pago,'
        . 'id_entrega_origen,notas) '
        . 'VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iiiiddsssiiiiiis',
        $centre_id,
        $course_id,
        $student_id,
        $product_id,
        $quantity,
        $stock_movement,
        $movement_type,
        $status,
        $delivery_date,
        $manager_id,
        $service_id,
        $invoice_id,
        $invoice_detail_id,
        $payment_id,
        $origin_delivery_id,
        $notes
    );
    $ok = mysqli_stmt_execute($statement);
    $delivery_id = $ok ? (int) mysqli_insert_id($database) : 0;
    mysqli_stmt_close($statement);
    return $delivery_id;
}

function material_create_delivery(
    $database,
    $centre_id,
    $course_id,
    $student_id,
    $product_id,
    $quantity,
    $user_id,
    $notes
) {
    $quantity = round((float) $quantity, 2);
    if ($quantity <= 0) {
        return array('ok' => false, 'error' => 'La cantidad debe ser mayor que cero.');
    }
    $product = material_product($database, $product_id);
    if (!$product) {
        return array('ok' => false, 'error' => 'El artículo seleccionado no existe.');
    }

    $student_statement = mysqli_prepare(
        $database,
        'SELECT id_alumno FROM alumnos '
        . 'WHERE id_alumno=? AND id_centro=? AND id_curso=? LIMIT 1'
    );
    mysqli_stmt_bind_param(
        $student_statement,
        'iii',
        $student_id,
        $centre_id,
        $course_id
    );
    mysqli_stmt_execute($student_statement);
    mysqli_stmt_store_result($student_statement);
    $valid_student = mysqli_stmt_num_rows($student_statement) > 0;
    mysqli_stmt_close($student_statement);
    if (!$valid_student) {
        return array('ok' => false, 'error' => 'El alumno no pertenece al centro y curso seleccionados.');
    }

    $stock = material_stock($database, $centre_id, $product_id);
    if ($stock < $quantity) {
        return array(
            'ok' => false,
            'error' => 'No hay stock suficiente. Disponible: ' . $stock . '.',
        );
    }

    $delivery_date = date('Y-m-d');
    $service_id = material_create_service(
        $database,
        $student_id,
        $product_id,
        $quantity,
        $delivery_date
    );
    if ($service_id <= 0) {
        return array('ok' => false, 'error' => 'No se ha podido registrar el artículo pendiente de cobro.');
    }

    $delivery_id = material_insert_delivery($database, array(
        'id_centro' => $centre_id,
        'id_curso' => $course_id,
        'id_alumno' => $student_id,
        'id_material' => $product_id,
        'cantidad' => $quantity,
        'movimiento_stock' => $quantity,
        'tipo_movimiento' => 'entrega',
        'estado' => 'entregado',
        'fecha_entrega' => date('Y-m-d H:i:s'),
        'entregado_por' => $user_id,
        'id_servicio_contratado' => $service_id,
        'id_factura' => 0,
        'id_detalle_factura' => 0,
        'id_pago' => 0,
        'id_entrega_origen' => 0,
        'notas' => reports_string_limit_compat($notes, 500),
    ));
    if ($delivery_id <= 0) {
        mysqli_query(
            $database,
            'DELETE FROM servicios_contratados WHERE id_servicio_contratado='
            . $service_id
        );
        return array('ok' => false, 'error' => 'No se ha podido registrar la salida de stock.');
    }

    return array('ok' => true, 'id_entrega' => $delivery_id);
}

function reports_string_limit_compat($value, $limit)
{
    $value = trim((string) $value);
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $limit, 'UTF-8')
        : substr($value, 0, $limit);
}

function material_delivery($database, $delivery_id, $centre_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT me.*, t.ARTICULO, t.PRECIO_VENTA, t.SUBVENCION, '
        . 't.desglosar_como, iva.porcentaje_iva, '
        . 'u.Nombre, u.Apellido1, u.Apellido2 '
        . 'FROM material_entregas me '
        . 'INNER JOIN tarifa t ON t.ID=me.id_material '
        . 'INNER JOIN tipos_iva_servicios iva ON iva.id_tipo_iva=t.CATEGORIA '
        . 'INNER JOIN alumnos a ON a.id_alumno=me.id_alumno '
        . 'INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
        . 'WHERE me.id_entrega=? AND me.id_centro=? LIMIT 1'
    );
    mysqli_stmt_bind_param($statement, 'ii', $delivery_id, $centre_id);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $row = $result ? mysqli_fetch_assoc($result) : false;
    if ($result) {
        mysqli_free_result($result);
    }
    mysqli_stmt_close($statement);
    return $row;
}

function material_next_invoice_number($database, $year)
{
    $statement = mysqli_prepare(
        $database,
        "SELECT MAX(CAST(num_factura AS UNSIGNED)) AS ultimo "
        . "FROM facturas_emitidas WHERE ejercicio_factura=? "
        . "AND (serie IS NULL OR serie<>'EDU')"
    );
    mysqli_stmt_bind_param($statement, 's', $year);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $last_number);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);
    return str_pad(((int) $last_number) + 1, 5, '0', STR_PAD_LEFT);
}

function material_create_invoice($database, $student_id, $lines, $user_id, $note)
{
    $student_query = mysqli_query(
        $database,
        'SELECT a.id_alumno, a.id_centro FROM alumnos a '
        . 'WHERE a.id_alumno=' . (int) $student_id . ' LIMIT 1'
    );
    $student = $student_query ? mysqli_fetch_assoc($student_query) : false;
    if ($student_query) {
        mysqli_free_result($student_query);
    }
    if (!$student) {
        return array('ok' => false, 'error' => 'No se encuentra la matrícula del alumno.');
    }

    $total = 0.0;
    foreach ($lines as $line) {
        $total += round(
            ((float) $line['price'])
            * ((float) $line['quantity'])
            * (1 - ((float) $line['discount'] / 100)),
            2
        );
    }
    $total = round($total, 2);
    $year = date('Y');
    $number = material_next_invoice_number($database, $year);
    $today = date('Y-m-d');
    $series = 'MAT';
    $credit = $total < 0 ? 1 : 0;

    $invoice_statement = mysqli_prepare(
        $database,
        'INSERT INTO facturas_emitidas '
        . '(serie,num_factura,ejercicio_factura,id_cliente,fecha_emision,'
        . 'OBSERVACIONES,abono,total_factura) VALUES (?,?,?,?,?,?,?,?)'
    );
    mysqli_stmt_bind_param(
        $invoice_statement,
        'sssissid',
        $series,
        $number,
        $year,
        $student_id,
        $today,
        $note,
        $credit,
        $total
    );
    if (!mysqli_stmt_execute($invoice_statement)) {
        $error = mysqli_stmt_error($invoice_statement);
        mysqli_stmt_close($invoice_statement);
        return array('ok' => false, 'error' => 'No se ha podido crear la factura. ' . $error);
    }
    $invoice_id = (int) mysqli_insert_id($database);
    mysqli_stmt_close($invoice_statement);

    $detail_ids = array();
    foreach ($lines as $line) {
        $line_service_id = (int) $line['service_id'];
        $line_price = (float) $line['price'];
        $line_quantity = (float) $line['quantity'];
        $line_discount = (float) $line['discount'];
        $line_vat = (float) $line['vat'];
        $line_concept = (string) $line['concept'];
        $line_subsidy = (int) $line['subsidy'];
        $line_breakdown = (int) $line['breakdown'];
        $detail_statement = mysqli_prepare(
            $database,
            'INSERT INTO detalles_factura '
            . '(id_factura,articulo,precio,cantidad,dto,tipo_iva,concepto,'
            . 'subvencion,concepto_desglosar) VALUES (?,?,?,?,?,?,?,?,?)'
        );
        mysqli_stmt_bind_param(
            $detail_statement,
            'iiddddsii',
            $invoice_id,
            $line_service_id,
            $line_price,
            $line_quantity,
            $line_discount,
            $line_vat,
            $line_concept,
            $line_subsidy,
            $line_breakdown
        );
        if (!mysqli_stmt_execute($detail_statement)) {
            mysqli_stmt_close($detail_statement);
            mysqli_query($database, 'DELETE FROM detalles_factura WHERE id_factura=' . $invoice_id);
            mysqli_query($database, 'DELETE FROM facturas_emitidas WHERE id_factura=' . $invoice_id);
            return array('ok' => false, 'error' => 'No se han podido añadir los artículos a la factura.');
        }
        $detail_ids[] = (int) mysqli_insert_id($database);
        mysqli_stmt_close($detail_statement);
    }

    $payment_id = 0;
    if (abs($total) >= 0.01) {
        $centre_id = (int) $student['id_centro'];
        $time = date('H:i:s');
        $observation = 'Cobrado desde entregas de material';
        $comment = reports_string_limit_compat($note, 255);
        $sign = 1;
        $assistance_id = 0;
        $payment_statement = mysqli_prepare(
            $database,
            'INSERT INTO pagos_a_cuenta '
            . '(id_factura,id_centro,id_centro_cobro,fecha_pago,importe_pago,'
            . 'forma_pago,observaciones_pago,comentario,hora_pago,signo,'
            . 'anotado_por,id_asistencia) VALUES (?,?,?,?,?,0,?,?,?,?,?,?)'
        );
        mysqli_stmt_bind_param(
            $payment_statement,
            'iiisdsssiii',
            $invoice_id,
            $centre_id,
            $centre_id,
            $today,
            $total,
            $observation,
            $comment,
            $time,
            $sign,
            $user_id,
            $assistance_id
        );
        if (mysqli_stmt_execute($payment_statement)) {
            $payment_id = (int) mysqli_insert_id($database);
        }
        mysqli_stmt_close($payment_statement);
    }

    return array(
        'ok' => true,
        'id_factura' => $invoice_id,
        'id_detalles' => $detail_ids,
        'id_pago' => $payment_id,
        'total' => $total,
    );
}

function material_charge_delivery($database, $delivery_id, $centre_id, $user_id)
{
    $delivery = material_delivery($database, $delivery_id, $centre_id);
    if (!$delivery) {
        return array('ok' => false, 'error' => 'No se encuentra la entrega.');
    }
    if ((int) $delivery['id_factura'] > 0) {
        return array('ok' => true, 'id_factura' => (int) $delivery['id_factura'], 'existing' => true);
    }
    if ($delivery['estado'] !== 'entregado') {
        return array('ok' => false, 'error' => 'Esta entrega ya no está pendiente de cobro.');
    }

    $line = array(
        'service_id' => (int) $delivery['id_servicio_contratado'],
        'price' => (float) $delivery['PRECIO_VENTA'],
        'quantity' => (float) $delivery['cantidad'],
        'discount' => 0.0,
        'vat' => (float) $delivery['porcentaje_iva'],
        'concept' => $delivery['ARTICULO'],
        'subsidy' => (int) $delivery['SUBVENCION'],
        'breakdown' => (int) $delivery['desglosar_como'],
    );
    $invoice = material_create_invoice(
        $database,
        (int) $delivery['id_alumno'],
        array($line),
        $user_id,
        'Entrega de material #' . (int) $delivery_id
    );
    if (empty($invoice['ok'])) {
        return $invoice;
    }

    $detail_id = isset($invoice['id_detalles'][0])
        ? (int) $invoice['id_detalles'][0]
        : 0;
    $invoice_id = (int) $invoice['id_factura'];
    $payment_id = (int) $invoice['id_pago'];
    $status = 'cobrado';
    $statement = mysqli_prepare(
        $database,
        'UPDATE material_entregas SET estado=?,id_factura=?,'
        . 'id_detalle_factura=?,id_pago=? WHERE id_entrega=? AND id_factura=0'
    );
    mysqli_stmt_bind_param(
        $statement,
        'siiii',
        $status,
        $invoice_id,
        $detail_id,
        $payment_id,
        $delivery_id
    );
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);
    return $invoice;
}

function material_exchange_delivery(
    $database,
    $delivery_id,
    $centre_id,
    $new_product_id,
    $new_quantity,
    $user_id,
    $notes
) {
    $delivery = material_delivery($database, $delivery_id, $centre_id);
    if (!$delivery || (int) $delivery['id_factura'] <= 0) {
        return array('ok' => false, 'error' => 'Solo se pueden cambiar artículos ya cobrados.');
    }
    if ($delivery['estado'] !== 'cobrado') {
        return array('ok' => false, 'error' => 'Esta entrega ya ha sido cambiada anteriormente.');
    }
    $new_quantity = round((float) $new_quantity, 2);
    if ($new_quantity <= 0) {
        return array('ok' => false, 'error' => 'La cantidad del nuevo artículo no es válida.');
    }
    $new_product = material_product($database, $new_product_id);
    if (!$new_product) {
        return array('ok' => false, 'error' => 'El nuevo artículo no existe.');
    }
    if (material_stock($database, $centre_id, $new_product_id) < $new_quantity) {
        return array('ok' => false, 'error' => 'No hay stock suficiente del nuevo artículo.');
    }

    $old_detail_query = mysqli_query(
        $database,
        'SELECT precio,dto,tipo_iva,subvencion,concepto_desglosar '
        . 'FROM detalles_factura WHERE id_detalle='
        . (int) $delivery['id_detalle_factura'] . ' LIMIT 1'
    );
    $old_detail = $old_detail_query ? mysqli_fetch_assoc($old_detail_query) : false;
    if ($old_detail_query) {
        mysqli_free_result($old_detail_query);
    }
    if (!$old_detail) {
        return array('ok' => false, 'error' => 'No se encuentra la línea original de facturación.');
    }

    $new_service_id = material_create_service(
        $database,
        (int) $delivery['id_alumno'],
        $new_product_id,
        $new_quantity,
        date('Y-m-d')
    );
    if ($new_service_id <= 0) {
        return array('ok' => false, 'error' => 'No se puede registrar el nuevo artículo.');
    }

    $lines = array(
        array(
            'service_id' => (int) $delivery['id_servicio_contratado'],
            'price' => (float) $old_detail['precio'],
            'quantity' => -1 * (float) $delivery['cantidad'],
            'discount' => (float) $old_detail['dto'],
            'vat' => (float) $old_detail['tipo_iva'],
            'concept' => 'DEVOLUCIÓN · ' . $delivery['ARTICULO'],
            'subsidy' => (int) $old_detail['subvencion'],
            'breakdown' => (int) $old_detail['concepto_desglosar'],
        ),
        array(
            'service_id' => $new_service_id,
            'price' => (float) $new_product['PRECIO_VENTA'],
            'quantity' => $new_quantity,
            'discount' => 0.0,
            'vat' => (float) $new_product['porcentaje_iva'],
            'concept' => $new_product['ARTICULO'],
            'subsidy' => (int) $new_product['SUBVENCION'],
            'breakdown' => (int) $new_product['desglosar_como'],
        ),
    );
    $invoice = material_create_invoice(
        $database,
        (int) $delivery['id_alumno'],
        $lines,
        $user_id,
        'Cambio de material de la entrega #' . (int) $delivery_id
    );
    if (empty($invoice['ok'])) {
        mysqli_query(
            $database,
            'DELETE FROM servicios_contratados WHERE id_servicio_contratado='
            . $new_service_id
        );
        return $invoice;
    }

    $common = array(
        'id_centro' => (int) $delivery['id_centro'],
        'id_curso' => (int) $delivery['id_curso'],
        'id_alumno' => (int) $delivery['id_alumno'],
        'fecha_entrega' => date('Y-m-d H:i:s'),
        'entregado_por' => $user_id,
        'id_factura' => (int) $invoice['id_factura'],
        'id_pago' => (int) $invoice['id_pago'],
        'id_entrega_origen' => (int) $delivery_id,
        'notas' => reports_string_limit_compat($notes, 500),
    );
    $return_data = $common;
    $return_data['id_material'] = (int) $delivery['id_material'];
    $return_data['cantidad'] = (float) $delivery['cantidad'];
    $return_data['movimiento_stock'] = -1 * (float) $delivery['cantidad'];
    $return_data['tipo_movimiento'] = 'devolucion';
    $return_data['estado'] = 'devuelto';
    $return_data['id_servicio_contratado'] = (int) $delivery['id_servicio_contratado'];
    $return_data['id_detalle_factura'] = isset($invoice['id_detalles'][0])
        ? (int) $invoice['id_detalles'][0]
        : 0;

    $new_data = $common;
    $new_data['id_material'] = $new_product_id;
    $new_data['cantidad'] = $new_quantity;
    $new_data['movimiento_stock'] = $new_quantity;
    $new_data['tipo_movimiento'] = 'cambio';
    $new_data['estado'] = 'cobrado';
    $new_data['id_servicio_contratado'] = $new_service_id;
    $new_data['id_detalle_factura'] = isset($invoice['id_detalles'][1])
        ? (int) $invoice['id_detalles'][1]
        : 0;

    $return_id = material_insert_delivery($database, $return_data);
    $new_delivery_id = material_insert_delivery($database, $new_data);
    if ($return_id <= 0 || $new_delivery_id <= 0) {
        return array(
            'ok' => false,
            'error' => 'La factura se creó, pero no se pudo completar el movimiento de stock. Revisa la factura #' . (int) $invoice['id_factura'] . '.',
        );
    }

    $status = 'cambiado';
    $statement = mysqli_prepare(
        $database,
        'UPDATE material_entregas SET estado=? WHERE id_entrega=?'
    );
    mysqli_stmt_bind_param($statement, 'si', $status, $delivery_id);
    mysqli_stmt_execute($statement);
    mysqli_stmt_close($statement);

    $invoice['id_entrega_nueva'] = $new_delivery_id;
    return $invoice;
}

function material_deliveries($database, $centre_id, $course_id, $product_id)
{
    $statement = mysqli_prepare(
        $database,
        'SELECT me.*, t.ARTICULO, u.Nombre, u.Apellido1, u.Apellido2, '
        . "COALESCE(ca.nombre,'Sin aula') AS nombre_aula "
        . 'FROM material_entregas me '
        . 'INNER JOIN tarifa t ON t.ID=me.id_material '
        . 'INNER JOIN alumnos a ON a.id_alumno=me.id_alumno '
        . 'INNER JOIN usuarios u ON u.id_usuario=a.id_usuario '
        . 'LEFT JOIN centro_aulas ca ON ca.id_aula=a.aula '
        . "WHERE me.id_centro=? AND me.id_curso=? AND me.id_material=? "
        . "AND me.tipo_movimiento IN ('entrega','cambio') "
        . 'ORDER BY me.fecha_entrega DESC, me.id_entrega DESC LIMIT 250'
    );
    mysqli_stmt_bind_param(
        $statement,
        'iii',
        $centre_id,
        $course_id,
        $product_id
    );
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $rows = material_fetch_all($result);
    mysqli_stmt_close($statement);
    return $rows;
}

function material_set_flash($type, $message)
{
    $_SESSION['material_flash'] = array(
        'type' => $type,
        'message' => $message,
    );
}

function material_take_flash()
{
    if (empty($_SESSION['material_flash'])) {
        return false;
    }
    $flash = $_SESSION['material_flash'];
    unset($_SESSION['material_flash']);
    return $flash;
}
