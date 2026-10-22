<?php
/**
 * Normalizes two syntaxes removed by PHP 8:
 *   - String/array offsets written as $value{0}
 *   - Assignment by reference when instantiating: $value =& new Class()
 *
 * The conversion is token based and idempotent.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script solo se puede ejecutar desde la linea de comandos.\n");
    exit(1);
}

$root = isset($argv[1]) ? realpath($argv[1]) : false;
if (!$root || !is_dir($root)) {
    fwrite(STDERR, "Uso: php convertir_sintaxis_php8.php <directorio-raiz>\n");
    exit(1);
}

function previous_significant_token($tokens, $index)
{
    for ($position = $index - 1; $position >= 0; $position--) {
        $token = $tokens[$position];
        if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT), true)) {
            continue;
        }
        return $token;
    }

    return null;
}

function previous_significant_index($tokens, $index)
{
    for ($position = $index - 1; $position >= 0; $position--) {
        $token = $tokens[$position];
        if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT), true)) {
            continue;
        }
        return $position;
    }

    return null;
}

function next_significant_token($tokens, $index)
{
    $count = count($tokens);
    for ($position = $index + 1; $position < $count; $position++) {
        $token = $tokens[$position];
        if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT), true)) {
            continue;
        }
        return $token;
    }

    return null;
}

function token_before_brace_is_object_property($tokens, $brace_index)
{
    $property_index = previous_significant_index($tokens, $brace_index);
    if ($property_index === null) {
        return false;
    }

    $current = $tokens[$property_index];
    if (!is_array($current) || $current[0] !== T_STRING) {
        return false;
    }

    $previous = previous_significant_token($tokens, $property_index);
    return is_array($previous)
        && in_array($previous[0], array(T_OBJECT_OPERATOR, T_DOUBLE_COLON), true);
}

$files_scanned = 0;
$files_changed = 0;
$offsets_changed = 0;
$references_changed = 0;
$errors = array();

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }

    $path = $file->getPathname();
    $source = file_get_contents($path);
    if ($source === false) {
        $errors[] = $path . ': no se pudo leer';
        continue;
    }

    $files_scanned++;
    $tokens = token_get_all($source);
    $brace_stack = array();
    $file_offsets = 0;
    $file_references = 0;

    foreach ($tokens as $index => $token) {
        if ($token === '{') {
            $previous = previous_significant_token($tokens, $index);
            $is_offset = (is_array($previous) && $previous[0] === T_VARIABLE)
                || $previous === ']'
                || $previous === '}'
                || token_before_brace_is_object_property($tokens, $index);

            $brace_stack[] = $is_offset ? 'offset' : 'block';
            if ($is_offset) {
                $tokens[$index] = '[';
                $file_offsets++;
            }
            continue;
        }

        if ($token === '}') {
            $brace_type = count($brace_stack) > 0 ? array_pop($brace_stack) : 'block';
            if ($brace_type === 'offset') {
                $tokens[$index] = ']';
            }
            continue;
        }

        if ($token === '&') {
            $previous = previous_significant_token($tokens, $index);
            $next = next_significant_token($tokens, $index);
            if ($previous === '=' && is_array($next) && $next[0] === T_NEW) {
                $tokens[$index] = '';
                $file_references++;
            }
        }
    }

    if ($file_offsets === 0 && $file_references === 0) {
        continue;
    }

    $rebuilt = '';
    foreach ($tokens as $token) {
        $rebuilt .= is_array($token) ? $token[1] : $token;
    }

    if (file_put_contents($path, $rebuilt) === false) {
        $errors[] = $path . ': no se pudo escribir';
        continue;
    }

    $files_changed++;
    $offsets_changed += $file_offsets;
    $references_changed += $file_references;
}

echo json_encode(array(
    'root' => $root,
    'files_scanned' => $files_scanned,
    'files_changed' => $files_changed,
    'offsets_changed' => $offsets_changed,
    'references_changed' => $references_changed,
    'errors' => $errors,
), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit(count($errors) > 0 ? 2 : 0);
