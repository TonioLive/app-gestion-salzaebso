<?php
/** Deployment settings. Values and private storage are never included in Git. */
function lapices_app_path(string $relative = ''): string
{
    return dirname(__DIR__) . ($relative !== '' ? '/' . ltrim($relative, '/\\') : '');
}

function lapices_storage_path(string $relative = ''): string
{
    $root = getenv('LAPICES_STORAGE_PATH') ?: lapices_app_path('.private/storage');
    return rtrim($root, '/\\') . ($relative !== '' ? '/' . ltrim($relative, '/\\') : '');
}

function lapices_bank_setting(string $key, int $length): string
{
    $value = trim((string) getenv($key));
    $valid = $length === 24
        ? preg_match('/^ES[0-9]{22}$/D', $value)
        : preg_match('/^[0-9]{' . $length . '}$/D', $value);
    if (!$valid) {
        throw new RuntimeException('Configure la cuenta bancaria del modulo antes de generar la remesa: ' . $key);
    }
    return $value;
}
