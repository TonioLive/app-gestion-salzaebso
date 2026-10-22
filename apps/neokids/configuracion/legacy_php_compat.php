<?php
/**
 * Small compatibility layer for PHP functions removed after PHP 5.
 *
 * It keeps legacy modules available while they are replaced with native PHP
 * 8 code. New application code must not call these functions.
 */

function legacy_posix_regex($pattern, $case_insensitive)
{
    $delimiter = '~';
    $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);

    return $delimiter . $escaped . $delimiter . ($case_insensitive ? 'i' : '');
}

if (!function_exists('split')) {
    function split($pattern, $string, $limit = -1)
    {
        return preg_split(legacy_posix_regex($pattern, false), $string, $limit);
    }
}

if (!function_exists('spliti')) {
    function spliti($pattern, $string, $limit = -1)
    {
        return preg_split(legacy_posix_regex($pattern, true), $string, $limit);
    }
}

if (!function_exists('ereg')) {
    function ereg($pattern, $string, &$registers = null)
    {
        $matched = preg_match(
            legacy_posix_regex($pattern, false),
            $string,
            $matches
        );

        if ($matched !== 1) {
            return false;
        }

        $registers = $matches;
        return max(1, strlen($matches[0]));
    }
}

if (!function_exists('eregi')) {
    function eregi($pattern, $string, &$registers = null)
    {
        $matched = preg_match(
            legacy_posix_regex($pattern, true),
            $string,
            $matches
        );

        if ($matched !== 1) {
            return false;
        }

        $registers = $matches;
        return max(1, strlen($matches[0]));
    }
}

if (!function_exists('ereg_replace')) {
    function ereg_replace($pattern, $replacement, $string)
    {
        return preg_replace(
            legacy_posix_regex($pattern, false),
            $replacement,
            $string
        );
    }
}

if (!function_exists('eregi_replace')) {
    function eregi_replace($pattern, $replacement, $string)
    {
        return preg_replace(
            legacy_posix_regex($pattern, true),
            $replacement,
            $string
        );
    }
}

if (!function_exists('each')) {
    function each(&$array)
    {
        $key = key($array);
        if ($key === null) {
            return false;
        }

        $value = current($array);
        next($array);

        return array(
            1 => $value,
            'value' => $value,
            0 => $key,
            'key' => $key,
        );
    }
}

if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc()
    {
        return false;
    }
}

if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime()
    {
        return false;
    }
}

if (!function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime($new_setting)
    {
        return false;
    }
}
