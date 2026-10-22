(function () {
    'use strict';

    var root = document.querySelector('[data-report-builder]');
    var configNode = document.getElementById('report-builder-config');
    if (!root || !configNode) {
        return;
    }

    var config;
    try {
        config = JSON.parse(configNode.textContent);
    } catch (error) {
        return;
    }

    var schemaEndpoint = root.getAttribute('data-schema-endpoint');
    var previewEndpoint = root.getAttribute('data-preview-endpoint');
    var form = root.querySelector('[data-report-form]');
    var definitionInput = root.querySelector('[data-report-definition]');
    var baseSelect = root.querySelector('[data-base-table]');
    var joinsContainer = root.querySelector('[data-joins]');
    var columnsContainer = root.querySelector('[data-selected-columns]');
    var columnsEmpty = root.querySelector('[data-columns-empty]');
    var availableField = root.querySelector('[data-available-field]');
    var filtersContainer = root.querySelector('[data-filters]');
    var sortsContainer = root.querySelector('[data-sorts]');
    var limitInput = root.querySelector('[data-report-limit]');
    var previewButton = root.querySelector('[data-preview-report]');
    var previewStatus = root.querySelector('[data-preview-status]');
    var previewMessage = root.querySelector('[data-preview-message]');
    var previewWrap = root.querySelector('[data-preview-table-wrap]');
    var previewHead = root.querySelector('[data-preview-head]');
    var previewBody = root.querySelector('[data-preview-body]');

    var initial = config.definition || {};
    var state = {
        baseTable: initial.base_table || '',
        joins: Array.isArray(initial.joins) ? initial.joins.slice() : [],
        columns: Array.isArray(initial.columns) ? initial.columns.slice() : [],
        filters: Array.isArray(initial.filters) ? initial.filters.slice() : [],
        sorts: Array.isArray(initial.sort) ? initial.sort.slice() : [],
        limit: Number(initial.limit) || 1000
    };
    var schemaCache = {};
    var sourceSchemas = {};

    var operationOptions = [
        ['value', 'Mostrar valor'],
        ['count', 'Contar registros'],
        ['count_distinct', 'Contar valores distintos'],
        ['sum', 'Sumar'],
        ['avg', 'Promedio'],
        ['min', 'Valor mínimo'],
        ['max', 'Valor máximo']
    ];
    var operatorOptions = [
        ['eq', 'Es igual a'],
        ['neq', 'Es diferente de'],
        ['contains', 'Contiene'],
        ['starts', 'Empieza por'],
        ['ends', 'Termina en'],
        ['gt', 'Es mayor que'],
        ['gte', 'Es mayor o igual que'],
        ['lt', 'Es menor que'],
        ['lte', 'Es menor o igual que'],
        ['between', 'Está entre'],
        ['in', 'Está en la lista'],
        ['is_null', 'Está vacío / es nulo'],
        ['not_null', 'No está vacío']
    ];
    var valueModeOptions = [
        ['manual', 'Valor escrito'],
        ['today', 'Fecha de hoy'],
        ['centre', 'Centro activo'],
        ['user', 'Usuario actual']
    ];

    function create(tag, className, text) {
        var element = document.createElement(tag);
        if (className) {
            element.className = className;
        }
        if (typeof text !== 'undefined') {
            element.textContent = text;
        }
        return element;
    }

    function addOption(select, value, label, selected) {
        var option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        option.selected = selected === value;
        select.appendChild(option);
        return option;
    }

    function tableLabel(tableName) {
        var tables = config.tables || [];
        for (var index = 0; index < tables.length; index += 1) {
            if (tables[index].name === tableName) {
                return tables[index].label;
            }
        }
        return tableName;
    }

    function loadSchema(tableName) {
        if (!tableName) {
            return Promise.resolve([]);
        }
        if (schemaCache[tableName]) {
            return Promise.resolve(schemaCache[tableName]);
        }

        return fetch(schemaEndpoint + '?tabla=' + encodeURIComponent(tableName), {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {'Accept': 'application/json'}
        })
            .then(function (response) {
                return response.ok ? response.json() : Promise.reject();
            })
            .then(function (payload) {
                if (!payload || !payload.ok || !Array.isArray(payload.columns)) {
                    return Promise.reject();
                }
                schemaCache[tableName] = payload.columns;
                return schemaCache[tableName];
            });
    }

    function refreshSourceSchemas() {
        sourceSchemas = {};
        var tasks = [];
        if (state.baseTable) {
            tasks.push(loadSchema(state.baseTable).then(function (columns) {
                sourceSchemas.t0 = {
                    table: state.baseTable,
                    columns: columns
                };
            }));
        }

        state.joins.forEach(function (join, index) {
            if (!join.table) {
                return;
            }
            tasks.push(loadSchema(join.table).then(function (columns) {
                sourceSchemas['t' + (index + 1)] = {
                    table: join.table,
                    columns: columns
                };
            }));
        });

        return Promise.all(tasks);
    }

    function allFields(maxSourceIndex) {
        var fields = [];
        Object.keys(sourceSchemas).sort().forEach(function (source) {
            var sourceIndex = Number(source.substring(1));
            if (
                typeof maxSourceIndex === 'number'
                && sourceIndex > maxSourceIndex
            ) {
                return;
            }

            var schema = sourceSchemas[source];
            schema.columns.forEach(function (column) {
                fields.push({
                    source: source,
                    column: column.name,
                    category: column.category,
                    label: tableLabel(schema.table) + ' · ' + column.label
                });
            });
        });
        return fields;
    }

    function fieldKey(source, column) {
        return source + '|' + column;
    }

    function parseFieldKey(value) {
        var parts = String(value || '').split('|');
        return {
            source: parts[0] || '',
            column: parts.slice(1).join('|') || ''
        };
    }

    function fieldLabel(source, column) {
        var fields = allFields();
        var key = fieldKey(source, column);
        for (var index = 0; index < fields.length; index += 1) {
            if (fieldKey(fields[index].source, fields[index].column) === key) {
                return fields[index].label;
            }
        }
        return source + ' · ' + column;
    }

    function fillFieldSelect(select, selected, maxSourceIndex) {
        select.textContent = '';
        addOption(select, '', 'Selecciona un campo', selected);
        allFields(maxSourceIndex).forEach(function (field) {
            addOption(
                select,
                fieldKey(field.source, field.column),
                field.label,
                selected
            );
        });
    }

    function fillTableSelect(select, selected) {
        select.textContent = '';
        addOption(select, '', 'Selecciona una tabla', selected);
        (config.tables || []).forEach(function (table) {
            addOption(
                select,
                table.name,
                table.label + (table.type === 'view' ? ' · vista' : ''),
                selected
            );
        });
    }

    function renderAvailableFields() {
        fillFieldSelect(availableField, '');
    }

    function renderJoins() {
        joinsContainer.textContent = '';
        state.joins.forEach(function (join, index) {
            var row = create('div', 'report-join-row');
            var alias = 't' + (index + 1);

            var heading = create('div', 'report-join-row__heading');
            heading.appendChild(create(
                'strong',
                '',
                'Relación ' + (index + 1)
            ));
            heading.appendChild(create('span', '', alias));
            row.appendChild(heading);

            var typeLabel = create('label');
            typeLabel.appendChild(create('span', '', 'Tipo'));
            var typeSelect = document.createElement('select');
            addOption(typeSelect, 'left', 'Mantener todos los datos', join.type || 'left');
            addOption(typeSelect, 'inner', 'Solo coincidencias', join.type || 'left');
            typeSelect.addEventListener('change', function () {
                state.joins[index].type = typeSelect.value;
            });
            typeLabel.appendChild(typeSelect);
            row.appendChild(typeLabel);

            var tableLabelNode = create('label');
            tableLabelNode.appendChild(create('span', '', 'Tabla relacionada'));
            var tableSelect = document.createElement('select');
            fillTableSelect(tableSelect, join.table || '');
            tableSelect.addEventListener('change', function () {
                state.joins[index].table = tableSelect.value;
                state.joins[index].left_source = 't0';
                state.joins[index].left_column = '';
                state.joins[index].right_column = '';
                refreshSourceSchemas().then(function () {
                    suggestJoin(index);
                    cleanInvalidSelections();
                    renderEverything();
                }).catch(showSchemaError);
            });
            tableLabelNode.appendChild(tableSelect);
            row.appendChild(tableLabelNode);

            var leftLabel = create('label');
            leftLabel.appendChild(create('span', '', 'Campo de origen'));
            var leftSelect = document.createElement('select');
            fillFieldSelect(
                leftSelect,
                fieldKey(join.left_source, join.left_column),
                index
            );
            leftSelect.addEventListener('change', function () {
                var parsed = parseFieldKey(leftSelect.value);
                state.joins[index].left_source = parsed.source;
                state.joins[index].left_column = parsed.column;
            });
            leftLabel.appendChild(leftSelect);
            row.appendChild(leftLabel);

            var rightLabel = create('label');
            rightLabel.appendChild(create('span', '', 'Campo relacionado'));
            var rightSelect = document.createElement('select');
            rightSelect.appendChild(new Option('Selecciona un campo', ''));
            if (sourceSchemas[alias]) {
                sourceSchemas[alias].columns.forEach(function (column) {
                    addOption(
                        rightSelect,
                        column.name,
                        column.label,
                        join.right_column || ''
                    );
                });
            }
            rightSelect.addEventListener('change', function () {
                state.joins[index].right_column = rightSelect.value;
            });
            rightLabel.appendChild(rightSelect);
            row.appendChild(rightLabel);

            var remove = create('button', 'report-row-remove', 'Eliminar relación');
            remove.type = 'button';
            remove.addEventListener('click', function () {
                removeJoin(index);
            });
            row.appendChild(remove);
            joinsContainer.appendChild(row);
        });
    }

    function suggestJoin(index) {
        var join = state.joins[index];
        var rightSchema = sourceSchemas['t' + (index + 1)];
        if (!join || !rightSchema || rightSchema.columns.length === 0) {
            return;
        }

        var rightNames = {};
        rightSchema.columns.forEach(function (column) {
            rightNames[column.name] = true;
        });

        var candidates = allFields(index);
        candidates.sort(function (a, b) {
            var aId = a.column.indexOf('id_') === 0 || a.column === 'id';
            var bId = b.column.indexOf('id_') === 0 || b.column === 'id';
            return aId === bId ? 0 : (aId ? -1 : 1);
        });

        for (var candidate = 0; candidate < candidates.length; candidate += 1) {
            if (rightNames[candidates[candidate].column]) {
                join.left_source = candidates[candidate].source;
                join.left_column = candidates[candidate].column;
                join.right_column = candidates[candidate].column;
                return;
            }
        }

        if (candidates.length > 0) {
            join.left_source = candidates[0].source;
            join.left_column = candidates[0].column;
        }
        join.right_column = rightSchema.columns[0].name;
    }

    function shiftSourceAfterRemoval(source, removedIndex) {
        var index = Number(String(source).substring(1));
        if (index === removedIndex + 1) {
            return '';
        }
        return index > removedIndex + 1 ? 't' + (index - 1) : source;
    }

    function removeJoin(index) {
        state.joins.splice(index, 1);
        state.columns = state.columns.filter(function (column) {
            column.source = shiftSourceAfterRemoval(column.source, index);
            return column.source !== '';
        });
        state.filters = state.filters.filter(function (filter) {
            filter.source = shiftSourceAfterRemoval(filter.source, index);
            return filter.source !== '';
        });
        state.joins.forEach(function (join) {
            join.left_source = shiftSourceAfterRemoval(join.left_source, index);
            if (!join.left_source) {
                join.left_source = 't0';
                join.left_column = '';
            }
        });
        refreshSourceSchemas().then(function () {
            cleanInvalidSelections();
            renderEverything();
        }).catch(showSchemaError);
    }

    function renderColumns() {
        columnsContainer.textContent = '';
        columnsEmpty.hidden = state.columns.length !== 0;
        if (state.columns.length === 0) {
            columnsContainer.appendChild(columnsEmpty);
            renderSorts();
            return;
        }

        state.columns.forEach(function (column, index) {
            var row = create('div', 'report-column-row');
            var order = create('div', 'report-column-row__order', String(index + 1));
            row.appendChild(order);

            var field = create('div', 'report-column-row__field');
            field.appendChild(create(
                'strong',
                '',
                fieldLabel(column.source, column.column)
            ));
            field.appendChild(create(
                'small',
                '',
                column.source + '.' + column.column
            ));
            row.appendChild(field);

            var label = create('label');
            label.appendChild(create('span', '', 'Título de columna'));
            var labelInput = document.createElement('input');
            labelInput.type = 'text';
            labelInput.maxLength = 120;
            labelInput.value = column.label || fieldLabel(column.source, column.column);
            labelInput.addEventListener('input', function () {
                state.columns[index].label = labelInput.value;
                renderSorts();
            });
            label.appendChild(labelInput);
            row.appendChild(label);

            var operationLabel = create('label');
            operationLabel.appendChild(create('span', '', 'Operación'));
            var operationSelect = document.createElement('select');
            operationOptions.forEach(function (operation) {
                addOption(
                    operationSelect,
                    operation[0],
                    operation[1],
                    column.operation || 'value'
                );
            });
            operationSelect.addEventListener('change', function () {
                state.columns[index].operation = operationSelect.value;
            });
            operationLabel.appendChild(operationSelect);
            row.appendChild(operationLabel);

            var actions = create('div', 'report-column-row__actions');
            var up = create('button', '', '↑');
            up.type = 'button';
            up.title = 'Subir columna';
            up.disabled = index === 0;
            up.addEventListener('click', function () {
                moveColumn(index, index - 1);
            });
            actions.appendChild(up);
            var down = create('button', '', '↓');
            down.type = 'button';
            down.title = 'Bajar columna';
            down.disabled = index === state.columns.length - 1;
            down.addEventListener('click', function () {
                moveColumn(index, index + 1);
            });
            actions.appendChild(down);
            var remove = create('button', 'is-danger', '×');
            remove.type = 'button';
            remove.title = 'Quitar columna';
            remove.addEventListener('click', function () {
                state.columns.splice(index, 1);
                state.sorts = state.sorts.filter(function (sort) {
                    return sort.column_index !== index;
                }).map(function (sort) {
                    if (sort.column_index > index) {
                        sort.column_index -= 1;
                    }
                    return sort;
                });
                renderColumns();
            });
            actions.appendChild(remove);
            row.appendChild(actions);
            columnsContainer.appendChild(row);
        });
        renderSorts();
    }

    function moveColumn(from, to) {
        if (to < 0 || to >= state.columns.length) {
            return;
        }
        var moved = state.columns.splice(from, 1)[0];
        state.columns.splice(to, 0, moved);
        state.sorts.forEach(function (sort) {
            if (sort.column_index === from) {
                sort.column_index = to;
            } else if (from < to && sort.column_index > from && sort.column_index <= to) {
                sort.column_index -= 1;
            } else if (from > to && sort.column_index >= to && sort.column_index < from) {
                sort.column_index += 1;
            }
        });
        renderColumns();
    }

    function renderFilters() {
        filtersContainer.textContent = '';
        if (state.filters.length === 0) {
            filtersContainer.appendChild(create(
                'div',
                'report-empty report-empty--small',
                'Sin filtros: se incluirán todos los registros.'
            ));
            return;
        }

        state.filters.forEach(function (filter, index) {
            var row = create('div', 'report-filter-row');

            var fieldLabelNode = create('label');
            fieldLabelNode.appendChild(create('span', '', 'Campo'));
            var fieldSelect = document.createElement('select');
            fillFieldSelect(
                fieldSelect,
                fieldKey(filter.source, filter.column)
            );
            fieldSelect.addEventListener('change', function () {
                var parsed = parseFieldKey(fieldSelect.value);
                state.filters[index].source = parsed.source;
                state.filters[index].column = parsed.column;
            });
            fieldLabelNode.appendChild(fieldSelect);
            row.appendChild(fieldLabelNode);

            var operatorLabel = create('label');
            operatorLabel.appendChild(create('span', '', 'Condición'));
            var operatorSelect = document.createElement('select');
            operatorOptions.forEach(function (operator) {
                addOption(
                    operatorSelect,
                    operator[0],
                    operator[1],
                    filter.operator || 'eq'
                );
            });
            operatorSelect.addEventListener('change', function () {
                state.filters[index].operator = operatorSelect.value;
                renderFilters();
            });
            operatorLabel.appendChild(operatorSelect);
            row.appendChild(operatorLabel);

            var modeLabel = create('label');
            modeLabel.appendChild(create('span', '', 'Valor'));
            var modeSelect = document.createElement('select');
            valueModeOptions.forEach(function (mode) {
                addOption(
                    modeSelect,
                    mode[0],
                    mode[1],
                    filter.value_mode || 'manual'
                );
            });
            modeSelect.disabled = filter.operator === 'is_null'
                || filter.operator === 'not_null'
                || filter.operator === 'between'
                || filter.operator === 'in';
            modeSelect.addEventListener('change', function () {
                state.filters[index].value_mode = modeSelect.value;
                renderFilters();
            });
            modeLabel.appendChild(modeSelect);
            row.appendChild(modeLabel);

            var valueLabel = create('label', 'report-filter-value');
            valueLabel.appendChild(create('span', '', filter.operator === 'in'
                ? 'Lista de valores'
                : 'Valor'
            ));
            var valueInput = document.createElement(filter.operator === 'in'
                ? 'textarea'
                : 'input'
            );
            if (valueInput.tagName === 'INPUT') {
                valueInput.type = 'text';
            } else {
                valueInput.rows = 2;
                valueInput.placeholder = 'Uno, dos, tres';
            }
            valueInput.value = filter.value || '';
            valueInput.disabled = (filter.value_mode || 'manual') !== 'manual'
                || filter.operator === 'is_null'
                || filter.operator === 'not_null';
            valueInput.addEventListener('input', function () {
                state.filters[index].value = valueInput.value;
            });
            valueLabel.appendChild(valueInput);
            row.appendChild(valueLabel);

            if (filter.operator === 'between') {
                var value2Label = create('label', 'report-filter-value');
                value2Label.appendChild(create('span', '', 'Hasta'));
                var value2Input = document.createElement('input');
                value2Input.type = 'text';
                value2Input.value = filter.value2 || '';
                value2Input.addEventListener('input', function () {
                    state.filters[index].value2 = value2Input.value;
                });
                value2Label.appendChild(value2Input);
                row.appendChild(value2Label);
            }

            var remove = create('button', 'report-row-remove', 'Quitar');
            remove.type = 'button';
            remove.addEventListener('click', function () {
                state.filters.splice(index, 1);
                renderFilters();
            });
            row.appendChild(remove);
            filtersContainer.appendChild(row);
        });
    }

    function renderSorts() {
        sortsContainer.textContent = '';
        state.sorts = state.sorts.filter(function (sort) {
            return state.columns[sort.column_index];
        });
        if (state.sorts.length === 0) {
            sortsContainer.appendChild(create(
                'div',
                'report-empty report-empty--small',
                'Sin ordenación adicional.'
            ));
            return;
        }

        state.sorts.forEach(function (sort, index) {
            var row = create('div', 'report-sort-row');
            var columnLabel = create('label');
            columnLabel.appendChild(create('span', '', 'Columna'));
            var columnSelect = document.createElement('select');
            state.columns.forEach(function (column, columnIndex) {
                addOption(
                    columnSelect,
                    String(columnIndex),
                    column.label || fieldLabel(column.source, column.column),
                    String(sort.column_index)
                );
            });
            columnSelect.addEventListener('change', function () {
                state.sorts[index].column_index = Number(columnSelect.value);
            });
            columnLabel.appendChild(columnSelect);
            row.appendChild(columnLabel);

            var directionLabel = create('label');
            directionLabel.appendChild(create('span', '', 'Dirección'));
            var directionSelect = document.createElement('select');
            addOption(directionSelect, 'asc', 'Ascendente', sort.direction || 'asc');
            addOption(directionSelect, 'desc', 'Descendente', sort.direction || 'asc');
            directionSelect.addEventListener('change', function () {
                state.sorts[index].direction = directionSelect.value;
            });
            directionLabel.appendChild(directionSelect);
            row.appendChild(directionLabel);

            var remove = create('button', 'report-row-remove', 'Quitar');
            remove.type = 'button';
            remove.addEventListener('click', function () {
                state.sorts.splice(index, 1);
                renderSorts();
            });
            row.appendChild(remove);
            sortsContainer.appendChild(row);
        });
    }

    function cleanInvalidSelections() {
        var keys = {};
        allFields().forEach(function (field) {
            keys[fieldKey(field.source, field.column)] = true;
        });
        state.columns = state.columns.filter(function (column) {
            return keys[fieldKey(column.source, column.column)];
        });
        state.filters = state.filters.filter(function (filter) {
            return keys[fieldKey(filter.source, filter.column)];
        });
        state.sorts = state.sorts.filter(function (sort) {
            return state.columns[sort.column_index];
        });
    }

    function renderEverything() {
        renderJoins();
        renderAvailableFields();
        renderColumns();
        renderFilters();
        renderSorts();
    }

    function serializeDefinition() {
        var errors = [];
        if (!state.baseTable) {
            errors.push('Selecciona una tabla principal.');
        }
        state.joins.forEach(function (join) {
            if (
                !join.table
                || !join.left_source
                || !join.left_column
                || !join.right_column
            ) {
                errors.push('Completa o elimina las relaciones incompletas.');
            }
        });
        if (state.columns.length === 0) {
            errors.push('Añade al menos una columna.');
        }

        state.limit = Math.max(
            1,
            Math.min(50000, Number(limitInput.value) || 1000)
        );

        return {
            errors: errors,
            definition: {
                version: 1,
                base_table: state.baseTable,
                joins: state.joins,
                columns: state.columns,
                filters: state.filters,
                sort: state.sorts,
                limit: state.limit
            }
        };
    }

    function showMessage(message, isError) {
        previewMessage.hidden = false;
        previewMessage.classList.toggle('is-error', Boolean(isError));
        previewMessage.textContent = message;
    }

    function showSchemaError() {
        showMessage(
            'No se han podido cargar los campos de una tabla. Vuelve a intentarlo.',
            true
        );
    }

    function renderPreview(result) {
        previewHead.textContent = '';
        previewBody.textContent = '';
        var headerRow = document.createElement('tr');
        result.headers.forEach(function (header) {
            headerRow.appendChild(create('th', '', header));
        });
        previewHead.appendChild(headerRow);

        if (result.rows.length === 0) {
            var emptyRow = document.createElement('tr');
            var emptyCell = create('td', '', 'No hay datos que cumplan los filtros.');
            emptyCell.colSpan = Math.max(1, result.headers.length);
            emptyRow.appendChild(emptyCell);
            previewBody.appendChild(emptyRow);
        } else {
            result.rows.forEach(function (row) {
                var tableRow = document.createElement('tr');
                row.forEach(function (value, index) {
                    var cell = create(
                        'td',
                        value === null || value === '' ? 'is-empty' : '',
                        value === null || value === '' ? '—' : String(value)
                    );
                    cell.setAttribute('data-label', result.headers[index]);
                    tableRow.appendChild(cell);
                });
                previewBody.appendChild(tableRow);
            });
        }

        previewWrap.hidden = false;
        previewMessage.hidden = true;
        previewStatus.textContent = result.row_count + ' fila(s)'
            + (result.truncated ? ' · vista previa limitada' : '');
    }

    function previewReport() {
        var serialized = serializeDefinition();
        if (serialized.errors.length > 0) {
            showMessage(serialized.errors.join(' '), true);
            previewWrap.hidden = true;
            return;
        }

        previewButton.disabled = true;
        previewStatus.textContent = 'Consultando datos…';
        previewMessage.hidden = true;
        var body = new FormData();
        body.append('_csrf', config.csrf);
        body.append('definicion', JSON.stringify(serialized.definition));

        fetch(previewEndpoint, {
            method: 'POST',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {'Accept': 'application/json'},
            body: body
        })
            .then(function (response) {
                return response.json().then(function (payload) {
                    return {ok: response.ok, payload: payload};
                });
            })
            .then(function (response) {
                if (!response.ok || !response.payload.ok) {
                    var errors = response.payload.errors || ['No se puede ejecutar el informe.'];
                    throw new Error(errors.join(' '));
                }
                renderPreview(response.payload);
            })
            .catch(function (error) {
                previewStatus.textContent = 'No se ha podido generar';
                previewWrap.hidden = true;
                showMessage(error.message || 'No se puede ejecutar el informe.', true);
            })
            .then(function () {
                previewButton.disabled = false;
            });
    }

    baseSelect.value = state.baseTable;
    baseSelect.addEventListener('change', function () {
        state.baseTable = baseSelect.value;
        state.joins = [];
        state.columns = [];
        state.filters = [];
        state.sorts = [];
        refreshSourceSchemas().then(renderEverything).catch(showSchemaError);
    });

    root.querySelector('[data-add-join]').addEventListener('click', function () {
        if (state.joins.length >= Number(config.limits.joins || 4)) {
            showMessage('Puedes relacionar como máximo cuatro tablas.', true);
            return;
        }
        state.joins.push({
            table: '',
            type: 'left',
            left_source: 't0',
            left_column: '',
            right_column: ''
        });
        renderJoins();
    });

    root.querySelector('[data-add-column]').addEventListener('click', function () {
        if (state.columns.length >= Number(config.limits.columns || 80)) {
            showMessage('Has alcanzado el máximo de columnas.', true);
            return;
        }
        var selected = parseFieldKey(availableField.value);
        if (!selected.source || !selected.column) {
            showMessage('Selecciona un campo para añadirlo.', true);
            return;
        }
        state.columns.push({
            source: selected.source,
            column: selected.column,
            operation: 'value',
            label: fieldLabel(selected.source, selected.column)
        });
        availableField.value = '';
        previewMessage.hidden = true;
        renderColumns();
    });

    root.querySelector('[data-add-filter]').addEventListener('click', function () {
        var fields = allFields();
        if (fields.length === 0) {
            showMessage('Selecciona primero una tabla.', true);
            return;
        }
        if (state.filters.length >= Number(config.limits.filters || 20)) {
            showMessage('Has alcanzado el máximo de filtros.', true);
            return;
        }
        state.filters.push({
            source: fields[0].source,
            column: fields[0].column,
            operator: 'eq',
            value_mode: 'manual',
            value: '',
            value2: ''
        });
        renderFilters();
    });

    root.querySelector('[data-add-sort]').addEventListener('click', function () {
        if (state.columns.length === 0) {
            showMessage('Añade columnas antes de definir la ordenación.', true);
            return;
        }
        if (state.sorts.length >= Number(config.limits.sorts || 5)) {
            showMessage('Has alcanzado el máximo de ordenaciones.', true);
            return;
        }
        state.sorts.push({column_index: 0, direction: 'asc'});
        renderSorts();
    });

    previewButton.addEventListener('click', previewReport);
    form.addEventListener('submit', function (event) {
        var serialized = serializeDefinition();
        if (serialized.errors.length > 0) {
            event.preventDefault();
            showMessage(serialized.errors.join(' '), true);
            previewWrap.hidden = true;
            return;
        }
        definitionInput.value = JSON.stringify(serialized.definition);
    });

    limitInput.addEventListener('change', function () {
        state.limit = Number(limitInput.value) || 1000;
    });

    refreshSourceSchemas().then(function () {
        cleanInvalidSelections();
        renderEverything();
    }).catch(showSchemaError);
}());
