(function () {
    'use strict';

    var BODY_CLASS = 'lapices-orientation-required';
    var OVERLAY_CLASS = 'lapices-orientation-guard';
    var refreshTimer = null;

    function widestRow(table) {
        var maximum = 0;
        Array.prototype.forEach.call(table.rows || [], function (row) {
            var columns = 0;
            Array.prototype.forEach.call(row.cells || [], function (cell) {
                columns += Number(cell.colSpan) || 1;
            });
            maximum = Math.max(maximum, columns);
        });
        return maximum;
    }

    function isVisible(element) {
        var style = window.getComputedStyle(element);
        var rect = element.getBoundingClientRect();
        return style.display !== 'none'
            && style.visibility !== 'hidden'
            && rect.width > 0
            && rect.height > 0;
    }

    function isExcluded(table) {
        return table.id === 'AutoNumber1'
            || !!table.closest('[data-orientation-guard="off"]')
            || !!table.closest('.lapices-orientation-guard')
            || !!table.closest('.legacy-admin-header, .sidebar, .topbar')
            || table.classList.contains('legacy-responsive-list')
            || table.classList.contains('legacy-student-roster')
            || table.classList.contains('legacy-module-menu')
            || table.classList.contains('legacy-family-table')
            || !!table.querySelector('table');
    }

    function isInformationTable(table, columns) {
        return table.hasAttribute('data-requires-landscape')
            || table.classList.contains('table')
            || table.classList.contains('legacy-wide-table')
            || table.classList.contains('legacy-data-table')
            || table.classList.contains('results-table')
            || !!table.tHead
            || (columns >= 6 && table.rows.length >= 2);
    }

    function availableWidth(table) {
        var container = table.closest(
            '.table-responsive, .legacy-table-scroll, .legacy-data-table-wrap, '
            + '[data-table-scroll], [role="region"]'
        );
        var width = container ? container.clientWidth : (table.parentElement ? table.parentElement.clientWidth : 0);
        return Math.min(
            Math.max(0, window.innerWidth - 24),
            width > 0 ? width : window.innerWidth
        );
    }

    function tableNeedsLandscape(table) {
        if (!isVisible(table) || isExcluded(table)) {
            return false;
        }

        var columns = widestRow(table);
        if (!isInformationTable(table, columns)) {
            return false;
        }
        if (table.hasAttribute('data-requires-landscape')) {
            return true;
        }

        var width = Math.max(table.scrollWidth, table.getBoundingClientRect().width);
        var available = availableWidth(table);
        var container = table.closest(
            '.table-responsive, .legacy-table-scroll, .legacy-data-table-wrap, '
            + '[data-table-scroll], [role="region"]'
        );
        var containerOverflows = container
            && container.scrollWidth > container.clientWidth + 24;
        var documentOverflows = document.documentElement.scrollWidth > window.innerWidth + 32;

        return columns >= 5
            && available > 0
            && (
                width > available + 24
                || containerOverflows
                || (documentOverflows && width > window.innerWidth - 12)
            );
    }

    function hasProblematicTable() {
        if (document.body.getAttribute('data-orientation-guard') === 'force') {
            return true;
        }

        var tables = document.querySelectorAll('table');
        return Array.prototype.some.call(tables, tableNeedsLandscape);
    }

    function isCompactPortrait() {
        var portrait = window.matchMedia
            ? window.matchMedia('(orientation: portrait)').matches
            : window.innerHeight > window.innerWidth;
        return portrait && window.innerWidth <= 1024;
    }

    function ensureOverlay() {
        var existing = document.querySelector('.' + OVERLAY_CLASS);
        if (existing) {
            return existing;
        }

        var overlay = document.createElement('div');
        overlay.className = OVERLAY_CLASS;
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'lapices-orientation-title');
        overlay.setAttribute('aria-describedby', 'lapices-orientation-description');
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML =
            '<div class="lapices-orientation-guard__card">'
            + '<span class="lapices-orientation-guard__eyebrow">Vista panorámica</span>'
            + '<div class="lapices-orientation-guard__icon" aria-hidden="true">'
            + '<span class="lapices-orientation-guard__arrow"></span>'
            + '<span class="lapices-orientation-guard__phone"></span>'
            + '</div>'
            + '<h2 id="lapices-orientation-title">Gira el dispositivo</h2>'
            + '<p id="lapices-orientation-description">Esta pantalla contiene varias columnas. '
            + 'Pon el móvil o la tablet en <strong>horizontal</strong> para verla completa y trabajar sin perder información.</p>'
            + '</div>';
        document.body.appendChild(overlay);
        return overlay;
    }

    function evaluate() {
        refreshTimer = null;
        if (!document.body || document.body.getAttribute('data-orientation-guard') === 'off') {
            return;
        }

        var required = isCompactPortrait() && hasProblematicTable();
        var overlay = ensureOverlay();
        document.body.classList.toggle(BODY_CLASS, required);
        overlay.setAttribute('aria-hidden', required ? 'false' : 'true');
    }

    function scheduleEvaluation() {
        if (refreshTimer !== null) {
            window.clearTimeout(refreshTimer);
        }
        refreshTimer = window.setTimeout(evaluate, 90);
    }

    function boot() {
        evaluate();
        window.addEventListener('resize', scheduleEvaluation, {passive: true});
        window.addEventListener('orientationchange', scheduleEvaluation, {passive: true});
        window.addEventListener('load', scheduleEvaluation, {once: true});

        if (window.ResizeObserver) {
            new ResizeObserver(scheduleEvaluation).observe(document.documentElement);
        }
        if (window.MutationObserver) {
            new MutationObserver(scheduleEvaluation).observe(document.body, {
                childList: true,
                subtree: true
            });
        }

        window.LapicesOrientationGuard = {
            refresh: scheduleEvaluation
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, {once: true});
    } else {
        boot();
    }
}());
