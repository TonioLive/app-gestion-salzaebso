<?php
if (!isset($global_search_instance)) {
    $global_search_instance = 0;
}
$global_search_instance++;
$global_search_id = 'global-search-results-' . $global_search_instance;
$global_search_context_class = isset($global_search_context) && $global_search_context === 'legacy'
    ? ' global-search--legacy'
    : ' global-search--panel';
?>
<div
    class="global-search<?= $global_search_context_class; ?>"
    data-global-search
    data-global-search-endpoint="/gestion/panel/buscar.php"
>
    <button
        class="global-search__toggle"
        type="button"
        aria-label="Abrir buscador global"
        aria-expanded="false"
        aria-controls="<?= app_escape($global_search_id); ?>"
        data-global-search-toggle
    >
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <circle cx="11" cy="11" r="6.5"></circle>
            <path d="m16 16 4 4"></path>
        </svg>
    </button>

    <div class="global-search__surface" data-global-search-surface>
        <div class="global-search__mobile-heading">
            <div>
                <span>Buscador global</span>
                <strong>¿Qué necesitas abrir?</strong>
            </div>
            <button type="button" aria-label="Cerrar buscador" data-global-search-close>&times;</button>
        </div>

        <div class="global-search__field" role="search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="m16 16 4 4"></path>
            </svg>
            <label class="sr-only" for="global-search-input-<?= $global_search_instance; ?>">
                Buscar alumnos, servicios u opciones del programa
            </label>
            <input
                id="global-search-input-<?= $global_search_instance; ?>"
                type="search"
                placeholder="Buscar alumno, servicio u opción…"
                autocomplete="off"
                spellcheck="false"
                role="combobox"
                aria-autocomplete="list"
                aria-haspopup="listbox"
                aria-expanded="false"
                aria-controls="<?= app_escape($global_search_id); ?>"
                data-global-search-input
            >
            <kbd class="global-search__shortcut" aria-hidden="true">Ctrl K</kbd>
            <button
                class="global-search__clear"
                type="button"
                aria-label="Borrar búsqueda"
                hidden
                data-global-search-clear
            >&times;</button>
        </div>

        <div
            class="global-search__results"
            id="<?= app_escape($global_search_id); ?>"
            role="listbox"
            aria-label="Resultados de búsqueda"
            hidden
            data-global-search-results
        ></div>
        <p class="sr-only" aria-live="polite" data-global-search-status></p>
    </div>
</div>
