param(
    [string]$Root = (Split-Path -Parent $PSScriptRoot)
)

$ErrorActionPreference = 'Stop'

if (-not (Get-Command rg -ErrorAction SilentlyContinue)) {
    throw 'La auditoria necesita ripgrep (rg).'
}

$resolvedRoot = (Resolve-Path $Root).Path
$phpFiles = @(
    Get-ChildItem -LiteralPath $resolvedRoot -Recurse -File |
        Where-Object { $_.Name.ToLowerInvariant().EndsWith('.php') }
)
$totalLines = 0

foreach ($phpFile in $phpFiles) {
    $totalLines += (Get-Content -LiteralPath $phpFile.FullName | Measure-Object -Line).Lines
}

$patterns = [ordered]@{
    short_open_tags = '(?<![''"])(?<![?])<\?(?!php|=|xml|xpacket)'
    mysql_api = 'mysql_[A-Za-z0-9_]+'
    direct_get = '\$_GET\s*\['
    direct_post = '\$_POST\s*\['
    removed_split = '\bsplit\s*\('
    removed_each = '\beach\s*\('
    removed_ereg = '\bereg(i)?\s*\('
    removed_magic_quotes = '\b(get|set)_magic_quotes_[a-z_]*\s*\('
    deprecated_strftime = '\bstrftime\s*\('
    zero_dates = '0000-00-00'
}

$report = [ordered]@{
    generated_at = (Get-Date).ToString('s')
    root = $resolvedRoot
    php_files = $phpFiles.Count
    php_lines = $totalLines
    checks = [ordered]@{}
}

foreach ($entry in $patterns.GetEnumerator()) {
    $matches = @(rg --pcre2 -o --no-filename --glob '*.php' $entry.Value $resolvedRoot 2>$null)
    $files = @(rg --pcre2 -l --glob '*.php' $entry.Value $resolvedRoot 2>$null)

    $report.checks[$entry.Key] = [ordered]@{
        occurrences = $matches.Count
        files = $files.Count
    }
}

$report | ConvertTo-Json -Depth 5
