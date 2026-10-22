(function () {
    'use strict';

    var pageSizes = [20, 50, 80];
    var storageKey = 'lapices-table-page-size';

    function directRows(table) {
        if (!table.tBodies || table.tBodies.length !== 1) {
            return [];
        }
        return Array.prototype.slice.call(table.tBodies[0].rows);
    }

    function normalise(value) {
        var text = String(value || '').toLowerCase();
        return typeof text.normalize === 'function'
            ? text.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            : text;
    }

    function shouldSkip(table) {
        if (
            table.dataset.pagination === 'server'
            || table.dataset.pagination === 'off'
            || table.closest('[data-no-table-pagination]')
            || table.closest('.legacy-admin-header')
            || table.closest('.message-center')
            || table.closest('.legacy-family-table-scroll')
            || table.classList.contains('legacy-module-menu')
            || table.classList.contains('legacy-student-family-editor')
            || table.classList.contains('legacy-family-table')
        ) {
            return true;
        }

        var tableText = normalise(table.textContent).slice(0, 800);
        return tableText.indexOf('lunes') !== -1
            && tableText.indexOf('martes') !== -1
            && tableText.indexOf('miercoles') !== -1
            && tableText.indexOf('jueves') !== -1;
    }

    function candidateRows(table) {
        var rows = directRows(table);
        if (table.classList.contains('legacy-student-roster')) {
            return rows.filter(function (row) {
                return row.classList.contains('legacy-student-roster__student');
            });
        }
        if (table.tHead || table.classList.contains('legacy-data-table')) {
            return rows.filter(function (row) {
                return !row.querySelector('th');
            });
        }

        var widest = rows.reduce(function (maximum, row) {
            return Math.max(maximum, row.cells.length);
        }, 0);
        if (widest < 2) {
            return [];
        }
        return rows.filter(function (row) {
            if (row.querySelector('th') || row.cells.length < Math.max(2, widest - 1)) {
                return false;
            }
            var wideCell = Array.prototype.some.call(row.cells, function (cell) {
                return (Number(cell.colSpan) || 1) >= widest;
            });
            var boldCells = Array.prototype.filter.call(row.cells, function (cell) {
                return !!cell.querySelector('b, strong');
            }).length;
            var looksLikeHeader = boldCells >= Math.ceil(row.cells.length * 0.6)
                && !row.querySelector('input, select, textarea, a[href]');
            return !wideCell && !looksLikeHeader;
        });
    }

    function storedSize() {
        var value = 20;
        try {
            value = Number(window.localStorage.getItem(storageKey) || 20);
        } catch (error) {
            value = 20;
        }
        return pageSizes.indexOf(value) !== -1 ? value : 20;
    }

    function create(tag, className, text) {
        var element = document.createElement(tag);
        if (className) {
            element.className = className;
        }
        if (text !== undefined) {
            element.textContent = text;
        }
        return element;
    }

    function pageNumbers(current, total) {
        var numbers = [];
        var start = Math.max(1, current - 2);
        var end = Math.min(total, current + 2);
        if (start > 1) {
            numbers.push(1);
            if (start > 2) {
                numbers.push('…');
            }
        }
        for (var number = start; number <= end; number += 1) {
            numbers.push(number);
        }
        if (end < total) {
            if (end < total - 1) {
                numbers.push('…');
            }
            numbers.push(total);
        }
        return numbers;
    }

    function enhanceTable(table) {
        if (table.dataset.paginationReady === 'true' || shouldSkip(table)) {
            return;
        }

        var rows = candidateRows(table);
        if (rows.length <= 20) {
            return;
        }
        table.dataset.paginationReady = 'true';

        var state = {
            page: 1,
            perPage: storedSize()
        };
        var controls = create('div', 'table-pagination table-pagination--client');
        controls.setAttribute('aria-label', 'Paginación de la tabla');
        var sizeGroup = create('label', 'table-pagination__size');
        sizeGroup.appendChild(create('span', '', 'Filas por página'));
        var sizeSelect = create('select');
        sizeSelect.setAttribute('aria-label', 'Filas por página');
        pageSizes.forEach(function (size) {
            var option = create('option', '', String(size));
            option.value = String(size);
            option.selected = size === state.perPage;
            sizeSelect.appendChild(option);
        });
        sizeGroup.appendChild(sizeSelect);
        controls.appendChild(sizeGroup);
        var summary = create('span', 'table-pagination__summary');
        controls.appendChild(summary);
        var pages = create('nav', 'table-pagination__pages');
        pages.setAttribute('aria-label', 'Páginas del listado');
        controls.appendChild(pages);

        var host = table.closest('.legacy-table-scroll, .legacy-data-table-wrap') || table;
        host.parentNode.insertBefore(controls, host.nextSibling);

        function pageButton(label, target, options) {
            var button = create('button', options && options.className ? options.className : '', label);
            button.type = 'button';
            button.setAttribute('aria-label', options && options.ariaLabel ? options.ariaLabel : 'Página ' + label);
            if (options && options.current) {
                button.classList.add('is-current');
                button.setAttribute('aria-current', 'page');
            }
            if (options && options.disabled) {
                button.classList.add('is-disabled');
                button.disabled = true;
            }
            button.addEventListener('click', function () {
                state.page = target;
                render();
                if (options && options.scroll) {
                    table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
            return button;
        }

        function render() {
            var totalPages = Math.max(1, Math.ceil(rows.length / state.perPage));
            state.page = Math.min(state.page, totalPages);
            var start = (state.page - 1) * state.perPage;
            var end = Math.min(rows.length, start + state.perPage);
            rows.forEach(function (row, index) {
                row.classList.toggle('table-pagination-row-hidden', index < start || index >= end);
            });
            summary.textContent = (start + 1) + '–' + end + ' de ' + rows.length + ' filas';
            pages.textContent = '';
            pages.appendChild(pageButton('‹', state.page - 1, {
                ariaLabel: 'Página anterior',
                className: 'table-pagination__edge',
                disabled: state.page === 1,
                scroll: true
            }));
            pageNumbers(state.page, totalPages).forEach(function (number) {
                if (number === '…') {
                    pages.appendChild(create('span', 'table-pagination__ellipsis', number));
                    return;
                }
                pages.appendChild(pageButton(String(number), number, {
                    current: number === state.page,
                    scroll: true
                }));
            });
            pages.appendChild(pageButton('›', state.page + 1, {
                ariaLabel: 'Página siguiente',
                className: 'table-pagination__edge',
                disabled: state.page === totalPages,
                scroll: true
            }));
        }

        sizeSelect.addEventListener('change', function () {
            state.perPage = Number(sizeSelect.value);
            state.page = 1;
            try {
                window.localStorage.setItem(storageKey, String(state.perPage));
            } catch (error) {
                // La paginación sigue funcionando aunque el navegador bloquee el almacenamiento.
            }
            render();
        });
        render();
    }

    function enhance(root) {
        var scope = root && root.querySelectorAll ? root : document;
        if (scope.matches && scope.matches('table')) {
            enhanceTable(scope);
        }
        Array.prototype.forEach.call(scope.querySelectorAll('table'), enhanceTable);
    }

    window.LapicesTablePagination = { enhance: enhance };
    function initialise() {
        enhance(document);
        if (!window.MutationObserver || !document.body) {
            return;
        }
        var timer = null;
        new MutationObserver(function (mutations) {
            var hasNewContent = mutations.some(function (mutation) {
                return mutation.addedNodes && mutation.addedNodes.length > 0;
            });
            if (!hasNewContent || timer !== null) {
                return;
            }
            timer = window.setTimeout(function () {
                timer = null;
                enhance(document);
            }, 120);
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise);
    } else {
        initialise();
    }
}());
