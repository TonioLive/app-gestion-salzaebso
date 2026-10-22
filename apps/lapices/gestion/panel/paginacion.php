<?php
/**
 * Utilidades comunes de paginacion para los listados de gestion.
 * Compatible con PHP 5.6 y con los parametros usados por las pantallas antiguas.
 */

if (!function_exists('app_pagination_sizes')) {
    function app_pagination_sizes()
    {
        return array(20, 50, 80);
    }
}

if (!function_exists('app_pagination_per_page')) {
    function app_pagination_per_page($source, $key)
    {
        $value = app_request_int($source, $key, 20);
        return in_array($value, app_pagination_sizes(), true) ? $value : 20;
    }
}

if (!function_exists('app_pagination_page')) {
    function app_pagination_page($source, $key)
    {
        return max(1, app_request_int($source, $key, 1));
    }
}

if (!function_exists('app_pagination_url')) {
    function app_pagination_url($page, $perPage, $pageKey, $perPageKey)
    {
        $query = array();
        foreach ($_GET as $key => $value) {
            if (is_scalar($value) && $key !== $pageKey && $key !== $perPageKey) {
                $query[$key] = (string) $value;
            }
        }
        $query[$perPageKey] = (int) $perPage;
        $query[$pageKey] = max(1, (int) $page);
        return '?' . http_build_query($query, '', '&');
    }
}

if (!function_exists('app_render_pagination')) {
    function app_render_pagination($total, $page, $perPage, $options)
    {
        $total = max(0, (int) $total);
        $perPage = in_array((int) $perPage, app_pagination_sizes(), true)
            ? (int) $perPage
            : 20;
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) $page), $totalPages);
        if ($total <= 20) {
            return;
        }

        $pageKey = isset($options['page_key']) ? (string) $options['page_key'] : 'pagina';
        $perPageKey = isset($options['per_page_key'])
            ? (string) $options['per_page_key']
            : 'por_pagina';
        $label = isset($options['label']) ? (string) $options['label'] : 'resultados';
        $startItem = (($page - 1) * $perPage) + 1;
        $endItem = min($total, $page * $perPage);
        $windowStart = max(1, $page - 2);
        $windowEnd = min($totalPages, $page + 2);

        echo '<div class="table-pagination table-pagination--server" data-server-pagination>';
        echo '<form class="table-pagination__size" method="get">';
        foreach ($_GET as $key => $value) {
            if (!is_scalar($value) || $key === $pageKey || $key === $perPageKey) {
                continue;
            }
            echo '<input type="hidden" name="' . app_escape($key) . '" value="'
                . app_escape((string) $value) . '">';
        }
        echo '<input type="hidden" name="' . app_escape($pageKey) . '" value="1">';
        echo '<label><span>Filas por p&aacute;gina</span><select name="'
            . app_escape($perPageKey) . '" onchange="this.form.submit()">';
        foreach (app_pagination_sizes() as $size) {
            echo '<option value="' . $size . '"'
                . ($size === $perPage ? ' selected' : '') . '>' . $size . '</option>';
        }
        echo '</select></label></form>';
        echo '<span class="table-pagination__summary">' . $startItem . '&ndash;' . $endItem
            . ' de ' . $total . ' ' . app_escape($label) . '</span>';
        echo '<nav class="table-pagination__pages" aria-label="P&aacute;ginas del listado">';
        echo '<a class="table-pagination__edge' . ($page <= 1 ? ' is-disabled' : '')
            . '" href="' . ($page <= 1 ? '#' : app_escape(app_pagination_url($page - 1, $perPage, $pageKey, $perPageKey)))
            . '" aria-label="P&aacute;gina anterior"' . ($page <= 1 ? ' aria-disabled="true" tabindex="-1"' : '')
            . '>&lsaquo;</a>';

        if ($windowStart > 1) {
            echo '<a href="' . app_escape(app_pagination_url(1, $perPage, $pageKey, $perPageKey)) . '">1</a>';
            if ($windowStart > 2) {
                echo '<span class="table-pagination__ellipsis" aria-hidden="true">&hellip;</span>';
            }
        }
        for ($number = $windowStart; $number <= $windowEnd; $number++) {
            echo '<a href="' . app_escape(app_pagination_url($number, $perPage, $pageKey, $perPageKey)) . '"'
                . ($number === $page ? ' class="is-current" aria-current="page"' : '')
                . '>' . $number . '</a>';
        }
        if ($windowEnd < $totalPages) {
            if ($windowEnd < $totalPages - 1) {
                echo '<span class="table-pagination__ellipsis" aria-hidden="true">&hellip;</span>';
            }
            echo '<a href="' . app_escape(app_pagination_url($totalPages, $perPage, $pageKey, $perPageKey))
                . '">' . $totalPages . '</a>';
        }

        echo '<a class="table-pagination__edge' . ($page >= $totalPages ? ' is-disabled' : '')
            . '" href="' . ($page >= $totalPages ? '#' : app_escape(app_pagination_url($page + 1, $perPage, $pageKey, $perPageKey)))
            . '" aria-label="P&aacute;gina siguiente"'
            . ($page >= $totalPages ? ' aria-disabled="true" tabindex="-1"' : '')
            . '>&rsaquo;</a>';
        echo '</nav></div>';
    }
}
