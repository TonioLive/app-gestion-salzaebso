<?php

if (!function_exists('app_user_preferences_defaults')) {
    function app_user_preferences_defaults()
    {
        return array(
            'color_principal' => '#21675A',
            'vista_inicio' => 'clasica',
        );
    }
}

if (!function_exists('app_normalize_hex_color')) {
    function app_normalize_hex_color($color, $fallback)
    {
        $color = trim((string) $color);
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            return strtoupper((string) $fallback);
        }

        return strtoupper($color);
    }
}

if (!function_exists('app_hex_to_rgb')) {
    function app_hex_to_rgb($color)
    {
        $color = ltrim(app_normalize_hex_color($color, '#21675A'), '#');
        return array(
            hexdec(substr($color, 0, 2)),
            hexdec(substr($color, 2, 2)),
            hexdec(substr($color, 4, 2)),
        );
    }
}

if (!function_exists('app_mix_hex_colors')) {
    function app_mix_hex_colors($color, $base, $color_weight)
    {
        $color_rgb = app_hex_to_rgb($color);
        $base_rgb = app_hex_to_rgb($base);
        $weight = max(0, min(1, (float) $color_weight));
        $mixed = array();

        for ($index = 0; $index < 3; $index++) {
            $mixed[] = (int) round(
                ($color_rgb[$index] * $weight) + ($base_rgb[$index] * (1 - $weight))
            );
        }

        return sprintf('#%02X%02X%02X', $mixed[0], $mixed[1], $mixed[2]);
    }
}

if (!function_exists('app_load_user_preferences')) {
    function app_load_user_preferences($database, $user_id)
    {
        $preferences = app_user_preferences_defaults();
        $user_id = (int) $user_id;

        if (!($database instanceof mysqli) || $user_id <= 0) {
            return $preferences;
        }

        $result = db_query_params(
            'SELECT color_principal, vista_inicio '
            . 'FROM usuarios_preferencias WHERE id_usuario=? LIMIT 1',
            'i',
            array($user_id),
            $database
        );

        if ($result && ($row = db_fetch_assoc($result))) {
            $preferences['color_principal'] = app_normalize_hex_color(
                $row['color_principal'],
                $preferences['color_principal']
            );
            $preferences['vista_inicio'] = in_array(
                $row['vista_inicio'],
                array('clasica', 'analisis'),
                true
            ) ? $row['vista_inicio'] : 'clasica';
        }

        if ($result) {
            db_free_result($result);
        }

        return $preferences;
    }
}

if (!function_exists('app_save_user_preferences')) {
    function app_save_user_preferences($database, $user_id, $color, $view)
    {
        $user_id = (int) $user_id;
        $color = app_normalize_hex_color($color, '#21675A');
        $view = in_array($view, array('clasica', 'analisis'), true)
            ? $view
            : 'clasica';

        if (!($database instanceof mysqli) || $user_id <= 0) {
            return false;
        }

        $statement = db_prepare(
            'INSERT INTO usuarios_preferencias '
            . '(id_usuario, color_principal, vista_inicio) VALUES (?, ?, ?) '
            . 'ON DUPLICATE KEY UPDATE '
            . 'color_principal=VALUES(color_principal), '
            . 'vista_inicio=VALUES(vista_inicio)',
            $database
        );

        if (!$statement) {
            return false;
        }

        mysqli_stmt_bind_param($statement, 'iss', $user_id, $color, $view);
        $saved = db_execute($statement);
        mysqli_stmt_close($statement);

        return $saved;
    }
}

if (!function_exists('app_theme_palette')) {
    function app_theme_palette($color)
    {
        $color = app_normalize_hex_color($color, '#21675A');
        $rgb = app_hex_to_rgb($color);
        $brightness = (($rgb[0] * 299) + ($rgb[1] * 587) + ($rgb[2] * 114)) / 1000;

        return array(
            'primary' => $color,
            'primary_dark' => app_mix_hex_colors($color, '#000000', 0.76),
            'brand' => app_mix_hex_colors($color, '#102C28', 0.54),
            'brand_dark' => app_mix_hex_colors($color, '#081A16', 0.34),
            'soft' => app_mix_hex_colors($color, '#FFFFFF', 0.12),
            'rgb' => implode(', ', $rgb),
            'contrast' => $brightness > 165 ? '#142B27' : '#FFFFFF',
        );
    }
}
