(function () {
    'use strict';

    function directRows(table) {
        if (!table.tBodies || !table.tBodies.length) {
            return [];
        }

        return Array.prototype.slice.call(table.tBodies[0].rows);
    }

    function ensureResponsiveViewport() {
        document.body.classList.add('legacy-admin');

        var viewport = document.querySelector('meta[name="viewport"]');
        if (!viewport) {
            viewport = document.createElement('meta');
            viewport.name = 'viewport';
            document.head.appendChild(viewport);
        }

        viewport.content = 'width=device-width, initial-scale=1, viewport-fit=cover';
    }

    function loadHelpSystem() {
        if (!document.querySelector('link[data-lapices-help-style]')) {
            var stylesheet = document.createElement('link');
            stylesheet.rel = 'stylesheet';
            stylesheet.href = '/gestion/panel/assets/help.css?v=20260903.2';
            stylesheet.setAttribute('data-lapices-help-style', 'true');
            document.head.appendChild(stylesheet);
        }

        if (
            !window.LapicesHelp
            && !document.querySelector('script[src*="/gestion/panel/assets/help.js"]')
        ) {
            var script = document.createElement('script');
            script.src = '/gestion/panel/assets/help.js?v=20260903.2';
            script.defer = true;
            script.setAttribute('data-lapices-help-script', 'true');
            document.head.appendChild(script);
        }
    }

    function loadTablePaginationSystem() {
        if (!document.querySelector('link[href*="/gestion/panel/assets/table-pagination.css"]')) {
            var stylesheet = document.createElement('link');
            stylesheet.rel = 'stylesheet';
            stylesheet.href = '/gestion/panel/assets/table-pagination.css?v=20260908.2';
            document.head.appendChild(stylesheet);
        }

        if (
            !window.LapicesTablePagination
            && !document.querySelector('script[src*="/gestion/panel/assets/table-pagination.js"]')
        ) {
            var script = document.createElement('script');
            script.src = '/gestion/panel/assets/table-pagination.js?v=20260908.1';
            script.defer = true;
            document.head.appendChild(script);
        }
    }

    function loadSessionTimeoutSystem() {
        if (!document.querySelector('[data-session-timeout-style]')) {
            var stylesheet = document.createElement('link');
            stylesheet.rel = 'stylesheet';
            stylesheet.href = '/gestion/panel/assets/session-timeout.css?v=20260909.1';
            stylesheet.setAttribute('data-session-timeout-style', 'true');
            document.head.appendChild(stylesheet);
        }

        if (
            !window.LapicesSessionTimeoutBooted
            && !document.querySelector('script[src*="/gestion/panel/assets/session-timeout.js"]')
        ) {
            var script = document.createElement('script');
            script.src = '/gestion/panel/assets/session-timeout.js?v=20260909.1';
            script.defer = true;
            document.head.appendChild(script);
        }
    }

    function loadOrientationGuardSystem() {
        if (!document.querySelector('[data-orientation-guard-style]')) {
            var stylesheet = document.createElement('link');
            stylesheet.rel = 'stylesheet';
            stylesheet.href = '/gestion/panel/assets/orientation-guard.css?v=20260911.1';
            stylesheet.setAttribute('data-orientation-guard-style', 'true');
            document.head.appendChild(stylesheet);
        }

        if (
            !window.LapicesOrientationGuard
            && !document.querySelector('[data-orientation-guard-script]')
        ) {
            var script = document.createElement('script');
            script.src = '/gestion/panel/assets/orientation-guard.js?v=20260911.1';
            script.defer = true;
            script.setAttribute('data-orientation-guard-script', 'true');
            document.head.appendChild(script);
        }
    }

    function enhanceLegacyShell() {
        if (!document.body.classList.contains('legacy-admin')) {
            return;
        }

        var shell = document.getElementById('AutoNumber1');
        if (!shell) {
            return;
        }

        var rows = directRows(shell);
        if (!rows.length) {
            return;
        }

        var headerRow = null;
        var headerCell = null;
        var contentRow = null;
        var contentCell = null;

        shell.classList.add('legacy-admin-shell');

        rows.forEach(function (row) {
            Array.prototype.forEach.call(row.cells, function (cell) {
                if (!headerCell && cell.querySelector('.legacy-admin-header')) {
                    headerRow = row;
                    headerCell = cell;
                }

                if (!contentCell && cell.querySelector(':scope > div[align="center"]')) {
                    contentRow = row;
                    contentCell = cell;
                }
            });
        });

        if (headerRow && headerCell) {
            headerRow.classList.add('legacy-admin-shell__header-row');
            headerCell.classList.add('legacy-admin-shell__header-cell');
            Array.prototype.forEach.call(headerRow.cells, function (cell) {
                if (cell !== headerCell) {
                    cell.classList.add('legacy-admin-shell__decorative-cell');
                }
            });
        }

        if (contentRow && contentCell) {
            contentRow.classList.add('legacy-admin-shell__content-row');
            contentCell.classList.add('legacy-admin-shell__content-cell');
            Array.prototype.forEach.call(contentRow.cells, function (cell) {
                if (cell !== contentCell) {
                    cell.classList.add('legacy-admin-shell__decorative-cell');
                }
            });
        }

        rows.forEach(function (row) {
            if (row !== headerRow && row !== contentRow) {
                row.classList.add('legacy-admin-shell__spacer');
            }
        });
    }

    function enhanceStudentFamilyEditor() {
        var familyList = document.getElementById('tablaformulario');
        if (
            !familyList ||
            !familyList.querySelector('input[name^="nombre"]') ||
            familyList.closest('.legacy-family-table-scroll')
        ) {
            return;
        }

        var regionCell = familyList.parentElement;
        var headerTable = familyList.previousElementSibling;
        if (!regionCell || !headerTable || headerTable.tagName !== 'TABLE') {
            return;
        }

        var editorTable = regionCell.closest('table');
        if (!editorTable) {
            return;
        }

        editorTable.classList.add('legacy-student-family-editor');
        regionCell.classList.add('legacy-family-table-region');
        headerTable.classList.add('legacy-family-table', 'legacy-family-table--header');

        var defaultLabels = [
            'Seleccionar',
            'Nombre',
            'DNI',
            'Relación de parentesco',
            'Usuario webcam',
            'Contraseña',
            'Editar',
            'Eliminar',
            'Carta'
        ];
        var headerCells = headerTable.rows.length
            ? Array.prototype.slice.call(headerTable.rows[0].cells)
            : [];
        var labels = defaultLabels.map(function (fallback, index) {
            var cell = headerCells[index];
            var text = cell
                ? (cell.textContent || '').replace(/\s+/g, ' ').trim()
                : '';
            return text || fallback;
        });

        function labelFamilyTables() {
            Array.prototype.forEach.call(
                familyList.querySelectorAll(':scope > table'),
                function (table) {
                    table.classList.add('legacy-family-table');
                    Array.prototype.forEach.call(
                        table.querySelectorAll(':scope > tbody > tr > td, :scope > tr > td'),
                        function (cell, index) {
                            var label = labels[index] || 'Acción';
                            var nameInput = cell.querySelector(
                                'input[name^="nombre"]'
                            );
                            cell.setAttribute('data-label', label);
                            if (
                                index === 1 ||
                                nameInput
                            ) {
                                cell.classList.add('legacy-family-table__name');
                                if (nameInput) {
                                    cell.setAttribute(
                                        'data-value',
                                        nameInput.value || 'Sin familiar seleccionado'
                                    );
                                    nameInput.setAttribute(
                                        'title',
                                        nameInput.value || 'Sin familiar seleccionado'
                                    );
                                }
                            }
                            if (cell.querySelector('a[href^="javascript:"]')) {
                                cell.classList.add('legacy-family-table__action');
                            }
                        }
                    );
                }
            );
        }

        labelFamilyTables();

        var scroll = document.createElement('div');
        scroll.className = 'legacy-family-table-scroll';
        scroll.setAttribute('role', 'region');
        scroll.setAttribute('aria-label', 'Familiares y personas autorizadas');
        scroll.setAttribute('tabindex', '0');
        regionCell.insertBefore(scroll, headerTable);
        scroll.appendChild(headerTable);
        scroll.appendChild(familyList);

        if (window.MutationObserver) {
            new MutationObserver(labelFamilyTables).observe(familyList, {
                childList: true
            });
        }
        window.addEventListener('focus', labelFamilyTables);
    }

    function enhanceStudentRosters() {
        var tables = document.querySelectorAll('table');
        Array.prototype.forEach.call(tables, function (table) {
            var rows = directRows(table);
            var widestRow = rows.reduce(function (maximum, row) {
                return Math.max(maximum, row.cells.length);
            }, 0);
            var text = (table.textContent || '')
                .replace(/\s+/g, ' ')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
            if (
                widestRow < 10 ||
                text.indexOf('fecha nac./edad') === -1 ||
                text.indexOf('nacionalidad') === -1 ||
                text.indexOf('dias asistidos') === -1 ||
                text.indexOf('tipo educacion') === -1
            ) {
                return;
            }

            table.classList.add('legacy-student-roster');

            var headerRow = rows.find(function (row) {
                var rowText = (row.textContent || '')
                    .replace(/\s+/g, ' ')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '');
                return (
                    row.cells.length >= 10 &&
                    rowText.indexOf('fecha nac./edad') !== -1 &&
                    rowText.indexOf('nacionalidad') !== -1
                );
            });

            if (!headerRow) {
                return;
            }

            headerRow.classList.add('legacy-student-roster__head');
            var labels = [];
            var headerColumn = 0;
            Array.prototype.forEach.call(headerRow.cells, function (cell) {
                var label = (cell.textContent || '').replace(/\s+/g, ' ').trim();
                var span = Number(cell.colSpan) || 1;
                for (var offset = 0; offset < span; offset += 1) {
                    labels[headerColumn + offset] = label;
                }
                headerColumn += span;
            });

            var headerIndex = rows.indexOf(headerRow);
            rows.forEach(function (row, rowIndex) {
                if (rowIndex < headerIndex) {
                    row.classList.add('legacy-student-roster__info');
                    return;
                }

                if (row === headerRow) {
                    return;
                }

                var isStudent = !!row.querySelector(
                    'a[href*="nuevo_alumno1.php"], a[href*="nuevo_alumno"]'
                );
                row.classList.add(
                    isStudent
                        ? 'legacy-student-roster__student'
                        : 'legacy-student-roster__totals'
                );

                if (!isStudent) {
                    return;
                }

                var logicalColumn = 0;
                Array.prototype.forEach.call(row.cells, function (cell) {
                    var label = labels[logicalColumn] || 'Información';
                    var normalizedLabel = label
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '');
                    cell.setAttribute('data-label', label);

                    if (logicalColumn === 0) {
                        cell.classList.add('legacy-student-roster__order');
                    }
                    if (logicalColumn === 1 || normalizedLabel === 'nombre') {
                        cell.classList.add('legacy-student-roster__name');
                    }
                    if (
                        normalizedLabel.indexOf('detallado') !== -1 ||
                        normalizedLabel.indexOf('accion') !== -1 ||
                        cell.querySelectorAll('a[href], input, select').length >= 3
                    ) {
                        cell.classList.add('legacy-student-roster__actions');
                    }

                    logicalColumn += Number(cell.colSpan) || 1;
                });
            });
        });
    }

    function enhanceModuleMenus() {
        if (!document.body.classList.contains('legacy-admin')) {
            return;
        }

        var tables = document.querySelectorAll('table[border="1"]');
        Array.prototype.forEach.call(tables, function (table) {
            var rows = directRows(table);
            if (rows.length < 2) {
                return;
            }

            var widestRow = rows.reduce(function (maximum, row) {
                return Math.max(maximum, row.cells.length);
            }, 0);

            if (
                widestRow > 3 ||
                table.classList.contains('legacy-student-family-editor') ||
                table.classList.contains('legacy-student-roster') ||
                table.closest('.legacy-family-table-scroll')
            ) {
                return;
            }

            var actionRows = rows.filter(function (row) {
                return row.querySelector(
                    'input[type="button"], button, a[href] img'
                );
            });

            if (actionRows.length < 2 || actionRows.length / rows.length < 0.6) {
                return;
            }

            table.classList.add('legacy-module-menu');
            actionRows.forEach(function (row) {
                row.classList.add('legacy-module-menu__row');
            });

            if (table.parentElement && table.parentElement.tagName === 'TD') {
                table.parentElement.classList.add('legacy-module-section');
            }
        });
    }

    function enhanceResponsiveDataLists() {
        if (!document.body.classList.contains('legacy-admin')) {
            return;
        }

        var tables = document.querySelectorAll('table');
        Array.prototype.forEach.call(tables, function (table) {
            if (
                table.getAttribute('data-responsive-table') === 'off' ||
                table.id === 'AutoNumber1' ||
                table.closest('.legacy-admin-header') ||
                table.closest('.legacy-family-table-scroll') ||
                table.classList.contains('legacy-users-table') ||
                table.classList.contains('legacy-student-roster') ||
                table.classList.contains('legacy-module-menu') ||
                table.classList.contains('legacy-student-family-editor') ||
                table.classList.contains('legacy-family-table') ||
                table.querySelector('table') ||
                table.querySelector('td[rowspan], th[rowspan]') ||
                !table.tHead ||
                !table.tBodies ||
                !table.tBodies.length
            ) {
                return;
            }

            var headerRows = table.tHead.rows;
            var bodyRows = table.tBodies[0].rows;
            if (!headerRows.length || !bodyRows.length) {
                return;
            }

            var headerRow = headerRows[headerRows.length - 1];
            var headerText = (headerRow.textContent || '')
                .replace(/\s+/g, ' ')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
            var weekDayMatches = headerText.match(/lunes|martes|miercoles|jueves|viernes|sabado|domingo/g);
            if (weekDayMatches && weekDayMatches.length >= 3) {
                return;
            }

            var labels = [];
            var column = 0;
            Array.prototype.forEach.call(headerRow.cells, function (cell) {
                var label = (cell.textContent || '').replace(/\s+/g, ' ').trim();
                var span = Number(cell.colSpan) || 1;
                var offset;
                for (offset = 0; offset < span; offset += 1) {
                    labels[column + offset] = label || 'Informacion';
                }
                column += span;
            });

            if (labels.length < 3 || labels.length > 12) {
                return;
            }

            var compatibleRows = Array.prototype.filter.call(bodyRows, function (row) {
                return row.cells.length > 0 && row.cells.length <= labels.length;
            });
            if (!compatibleRows.length || compatibleRows.length / bodyRows.length < 0.8) {
                return;
            }

            table.classList.add('legacy-responsive-list');
            if (table.parentElement) {
                table.parentElement.classList.add('legacy-responsive-list-wrap');
            }

            Array.prototype.forEach.call(bodyRows, function (row) {
                var logicalColumn = 0;
                Array.prototype.forEach.call(row.cells, function (cell, index) {
                    if (!cell.hasAttribute('data-label')) {
                        cell.setAttribute(
                            'data-label',
                            labels[logicalColumn] || labels[index] || 'Informacion'
                        );
                    }
                    if (index === 0) {
                        cell.classList.add('legacy-responsive-list__primary');
                    }
                    logicalColumn += Number(cell.colSpan) || 1;
                });
            });
        });
    }

    function enhanceLegacyTables() {
        if (!document.body.classList.contains('legacy-admin')) {
            return;
        }

        var tables = document.querySelectorAll('table');
        Array.prototype.forEach.call(tables, function (table) {
            if (
                table.id === 'AutoNumber1' ||
                table.closest('.legacy-admin-header') ||
                table.closest('.legacy-table-scroll') ||
                table.closest('.legacy-family-table-scroll') ||
                table.classList.contains('legacy-module-menu') ||
                table.classList.contains('legacy-student-family-editor') ||
                table.classList.contains('legacy-family-table') ||
                table.classList.contains('legacy-data-table') ||
                table.classList.contains('legacy-responsive-list')
            ) {
                return;
            }

            var rows = directRows(table);
            if (!rows.length) {
                return;
            }

            var widestRow = rows.reduce(function (maximum, row) {
                return Math.max(maximum, row.cells.length);
            }, 0);

            var hasControls = !!table.querySelector('input, select, textarea');
            var hasRowspans = !!table.querySelector('td[rowspan], th[rowspan]');
            if (hasControls && widestRow <= 3 && rows.length >= 3 && !hasRowspans) {
                table.classList.add('legacy-responsive-form');
            }

            if (widestRow < 4) {
                return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'legacy-table-scroll';
            wrapper.setAttribute('role', 'region');
            wrapper.setAttribute('aria-label', 'Tabla desplazable');
            wrapper.setAttribute('tabindex', '0');
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
            table.classList.add('legacy-wide-table');
            if (table.classList.contains('legacy-student-roster')) {
                wrapper.classList.add('legacy-table-scroll--student-roster');
                wrapper.setAttribute(
                    'aria-label',
                    'Listado de alumnos y acciones'
                );
            }
        });
    }

    function enhanceClassroomSnackControl() {
        if (window.location.pathname !== '/gestion/agenda/dia_aula.php') {
            return;
        }

        var pageParams = new URLSearchParams(window.location.search);
        var classroomId = pageParams.get('id_aula');
        var day = pageParams.get('dia') || '';
        var foodLayer = document.getElementById('capa_1');
        if (!classroomId || !foodLayer || foodLayer.querySelector('.legacy-class-snack')) {
            return;
        }

        fetch('/gestion/agenda/estado_merienda_aula.php?id_aula=' + encodeURIComponent(classroomId) + '&dia=' + encodeURIComponent(day), {
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('No se ha podido consultar la merienda.');
                }
                return response.json();
            })
            .then(function (state) {
                if (!state.ok || !state.visible) {
                    return;
                }

                var headingCell = null;
                Array.prototype.some.call(foodLayer.querySelectorAll('td'), function (cell) {
                    if (cell.textContent.indexOf('Control de comidas') !== -1) {
                        headingCell = cell;
                        return true;
                    }
                    return false;
                });
                if (!headingCell) {
                    return;
                }

                var headingRow = headingCell.closest('tr');
                if (!headingRow || !headingRow.parentNode) {
                    return;
                }

                var controlRow = document.createElement('tr');
                var controlCell = document.createElement('td');
                controlCell.colSpan = 2;
                controlCell.className = 'legacy-class-snack-cell';
                controlRow.appendChild(controlCell);
                headingRow.parentNode.insertBefore(controlRow, headingRow.nextSibling);

                function render(active) {
                    var action = active ? 'restaurar' : 'quitar';
                    var title = active
                        ? 'Merienda quitada para toda el aula'
                        : 'Merienda incluida para toda el aula';
                    var description = active
                        ? 'Cocina no la contara y todos los alumnos figuran con merienda “Nada”.'
                        : 'Usa este boton si hoy el grupo no va a merendar por una actividad o evento.';
                    var buttonText = active
                        ? 'Volver a incluir la merienda'
                        : 'Quitar merienda a toda el aula';

                    controlCell.innerHTML = '';
                    var panel = document.createElement('div');
                    panel.className = 'legacy-class-snack' + (active ? ' is-disabled' : '');
                    panel.innerHTML =
                        '<div class="legacy-class-snack__icon" aria-hidden="true">M</div>' +
                        '<div class="legacy-class-snack__content">' +
                            '<strong>' + title + '</strong>' +
                            '<span>' + description + '</span>' +
                            '<small>Dia ' + state.date + '</small>' +
                        '</div>';

                    var button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'legacy-class-snack__button';
                    button.textContent = buttonText;
                    button.addEventListener('click', function () {
                        var confirmation = active
                            ? '¿Quieres volver a incluir la merienda para todo el aula? Los controles se restableceran como “Toda”.'
                            : '¿Quieres quitar la merienda a todos los alumnos del aula? Cocina dejara de contarla para este dia.';
                        if (!window.confirm(confirmation)) {
                            return;
                        }

                        button.disabled = true;
                        button.textContent = 'Guardando...';
                        var body = new URLSearchParams();
                        body.set('_csrf', state.csrf);
                        body.set('id_aula', classroomId);
                        body.set('fecha', state.date.split('/').reverse().join('-'));
                        body.set('accion', action);

                        fetch('/gestion/agenda/actualizar_merienda_aula.php', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                            body: body.toString()
                        })
                            .then(function (response) {
                                return response.json().then(function (data) {
                                    if (!response.ok || !data.ok) {
                                        throw new Error(data.message || 'No se ha podido guardar el cambio.');
                                    }
                                    return data;
                                });
                            })
                            .then(function () {
                                window.location.reload();
                            })
                            .catch(function (error) {
                                window.alert(error.message);
                                button.disabled = false;
                                button.textContent = buttonText;
                            });
                    });

                    panel.appendChild(button);
                    controlCell.appendChild(panel);
                }

                render(!!state.active);
            })
            .catch(function () {
                // El control es una mejora progresiva: el resto de la agenda
                // debe seguir funcionando aunque falle esta consulta.
            });
    }

    function enhanceLegacyAdmin() {
        ensureResponsiveViewport();
        loadHelpSystem();
        loadTablePaginationSystem();
        loadSessionTimeoutSystem();
        loadOrientationGuardSystem();
        enhanceLegacyShell();
        enhanceStudentFamilyEditor();
        enhanceStudentRosters();
        enhanceModuleMenus();
        enhanceResponsiveDataLists();
        enhanceLegacyTables();
        enhanceClassroomSnackControl();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', enhanceLegacyAdmin);
    } else {
        enhanceLegacyAdmin();
    }
}());
