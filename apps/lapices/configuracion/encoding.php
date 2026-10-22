<?php
/** UTF-8 application text; Windows-1252 is supported only at legacy boundaries. */
function app_unicode_character($code)
{
    if ($code < 128) { return chr($code); }
    if ($code < 2048) { return chr(192 | ($code >> 6)) . chr(128 | ($code & 63)); }
    return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
}

function app_windows1252_to_utf8($value)
{
    static $map = null;
    if ($map === null) {
        $map = array();
        $special = array(128=>8364,130=>8218,131=>402,132=>8222,133=>8230,134=>8224,135=>8225,136=>710,137=>8240,138=>352,139=>8249,140=>338,142=>381,145=>8216,146=>8217,147=>8220,148=>8221,149=>8226,150=>8211,151=>8212,152=>732,153=>8482,154=>353,155=>8250,156=>339,158=>382,159=>376);
        for ($byte = 128; $byte <= 255; $byte++) {
            $map[chr($byte)] = app_unicode_character(isset($special[$byte]) ? $special[$byte] : $byte);
        }
    }
    return strtr((string) $value, $map);
}

function app_text_utf8($value)
{
    $value = (string) $value;
    if ($value === '' || !preg_match('/[\x80-\xFF]/', $value)) { return $value; }
    if (!preg_match('//u', $value)) {
        // Preserve valid sequences when historical values contain mixed bytes.
        $value = preg_replace_callback(
            '/[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE-\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}|[\x80-\xFF]/',
            function ($match) { return strlen($match[0]) > 1 ? $match[0] : app_windows1252_to_utf8($match[0]); },
            $value
        );
    }
    if (strpos($value, 'Ã') === false && strpos($value, 'Â') === false && strpos($value, 'â') === false && strpos($value, 'Å') === false && strpos($value, 'Ä') === false) { return $value; }
    static $repairs = null;
    if ($repairs === null) {
        $repairs = array();
        foreach (array(array(160,383),array(8192,8303),array(8364,8364)) as $range) {
            for ($code = $range[0]; $code <= $range[1]; $code++) {
                $correct = app_unicode_character($code);
                $repairs[app_windows1252_to_utf8($correct)] = $correct;
            }
        }
    }
    // Exact, reversible sequences only; leave other Unicode text untouched.
    for ($pass = 0; $pass < 3; $pass++) {
        $repaired = strtr($value, $repairs);
        if ($repaired === $value) { break; }
        $value = $repaired;
    }
    return $value;
}

function app_legacy_font_text($value)
{
    $value = app_text_utf8($value);
    $encoded = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $value);
    return $encoded === false ? $value : $encoded;
}

function app_url_text_utf8($value)
{
    return app_text_utf8(urldecode((string) $value));
}

function app_legacy_fixed_text($value, $length)
{
    // Fixed-width legacy exports count bytes in Windows-1252, not UTF-8 bytes.
    return str_pad(substr(app_legacy_font_text($value), 0, (int) $length), (int) $length, ' ');
}
