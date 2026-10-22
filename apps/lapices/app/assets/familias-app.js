(function () {
    'use strict';

    function normalisePath(path) {
        var value = String(path || '').split('?')[0].replace(/\/+$/, '');
        return value || '/';
    }

    function markCurrentNavigation() {
        var currentPath = normalisePath(window.location.pathname);
        var links = document.querySelectorAll(
            '.sidebar-menu a[href], .familias-bottom-nav a[href]'
        );

        Array.prototype.forEach.call(links, function (link) {
            var linkPath = normalisePath(link.pathname);
            var isCurrent = linkPath === currentPath;

            if (currentPath.indexOf('/app/ver_mensaje') === 0 ||
                currentPath.indexOf('/app/nuevo_mensaje') === 0 ||
                currentPath.indexOf('/app/responder_mensaje') === 0) {
                isCurrent = linkPath === '/app/mensajeria.php';
            }

            if (currentPath.indexOf('/app/ver_recibos') === 0) {
                isCurrent = linkPath === '/app/recibos.php';
            }

            if (currentPath.indexOf('/app/ver_carpeta_aula') === 0 ||
                currentPath.indexOf('/app/galeria_comun') === 0 ||
                currentPath.indexOf('/app/evaluacion_piscina') === 0) {
                isCurrent = linkPath === '/app/documentos.php';
            }

            if (isCurrent) {
                link.classList.add('active');
                if (link.parentElement && link.parentElement.tagName === 'LI') {
                    link.parentElement.classList.add('active');
                }
                link.setAttribute('aria-current', 'page');
            }
        });
    }

    function makeTablesScrollable() {
        var tables = document.querySelectorAll(
            '.content table.table, .content table.tabla_datos'
        );

        Array.prototype.forEach.call(tables, function (table) {
            var parent = table.parentElement;
            if (!parent ||
                parent.classList.contains('table-responsive') ||
                parent.classList.contains('familias-table-scroll')) {
                return;
            }

            var wrapper = document.createElement('div');
            wrapper.className = 'familias-table-scroll';
            parent.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    }

    function initialiseMenuButton() {
        var buttons = document.querySelectorAll('[data-familias-menu]');

        Array.prototype.forEach.call(buttons, function (button) {
            button.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-open');
            });
        });
    }

    function labelHeaderActions() {
        var messages = document.querySelector('.messages-menu > a');
        var documents = document.querySelector('.notifications-menu > a');

        if (messages) {
            messages.setAttribute('aria-label', 'Mensajes');
            messages.setAttribute('title', 'Mensajes');
        }

        if (documents) {
            documents.setAttribute('aria-label', 'Documentos');
            documents.setAttribute('title', 'Documentos');
        }
    }

    function initialiseChatComposer() {
        var fields = document.querySelectorAll(
            '.familias-chat-composer textarea'
        );

        Array.prototype.forEach.call(fields, function (field) {
            function resizeField() {
                field.style.height = 'auto';
                field.style.height = Math.min(field.scrollHeight, 150) + 'px';
                field.style.overflowY =
                    field.scrollHeight > 150 ? 'auto' : 'hidden';
            }

            field.addEventListener('input', resizeField);
            resizeField();
        });
    }

    function initialiseRecipientPickers() {
        var options = document.querySelectorAll(
            '.familias-recipient-option'
        );

        function updateSelection() {
            Array.prototype.forEach.call(options, function (option) {
                var input = option.querySelector('input[type="radio"]');
                option.classList.toggle(
                    'is-selected',
                    Boolean(input && input.checked)
                );
            });
        }

        Array.prototype.forEach.call(options, function (option) {
            var input = option.querySelector('input[type="radio"]');
            if (input) {
                input.addEventListener('change', updateSelection);
            }
        });

        updateSelection();
    }

    function initialise() {
        document.body.classList.add('familias-app-ready');
        markCurrentNavigation();
        makeTablesScrollable();
        initialiseMenuButton();
        labelHeaderActions();
        initialiseChatComposer();
        initialiseRecipientPickers();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise);
    } else {
        initialise();
    }
}());
