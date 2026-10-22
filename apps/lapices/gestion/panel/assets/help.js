(function () {
    'use strict';

    if (window.LapicesHelp && window.LapicesHelp.ready) {
        return;
    }

    var state = {
        dialog: null,
        lastTrigger: null,
        context: null,
        contextRoute: ''
    };

    function element(name, className, text) {
        var node = document.createElement(name);
        if (className) {
            node.className = className;
        }
        if (typeof text === 'string') {
            node.textContent = text;
        }
        return node;
    }

    function ensureStylesheet() {
        if (document.querySelector('link[data-lapices-help-style]')) {
            return;
        }
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '/gestion/panel/assets/help.css?v=20260903.2';
        link.setAttribute('data-lapices-help-style', 'true');
        document.head.appendChild(link);
    }

    function closeDialog() {
        if (!state.dialog) {
            return;
        }
        if (typeof state.dialog.close === 'function') {
            state.dialog.close();
        } else {
            state.dialog.removeAttribute('open');
        }
        document.body.classList.remove('help-dialog-open');
        if (state.lastTrigger && typeof state.lastTrigger.focus === 'function') {
            state.lastTrigger.focus();
        }
    }

    function buildDialog() {
        if (state.dialog) {
            return state.dialog;
        }

        var dialog = document.createElement('dialog');
        dialog.className = 'help-dialog';
        dialog.setAttribute('aria-labelledby', 'lapices-help-title');

        var header = element('header', 'help-dialog__header');
        var headingCopy = element('div', 'help-dialog__heading');
        headingCopy.appendChild(element('span', 'help-dialog__eyebrow', 'Ayuda de esta pantalla'));
        var title = element('h2', '', 'Cargando ayuda…');
        title.id = 'lapices-help-title';
        title.setAttribute('data-help-dialog-title', '');
        headingCopy.appendChild(title);
        header.appendChild(headingCopy);

        var close = element('button', 'help-dialog__close', '×');
        close.type = 'button';
        close.setAttribute('aria-label', 'Cerrar ayuda');
        close.addEventListener('click', closeDialog);
        header.appendChild(close);
        dialog.appendChild(header);

        var body = element('div', 'help-dialog__body');
        body.setAttribute('data-help-dialog-body', '');
        body.appendChild(element('p', 'help-dialog__loading', 'Preparando los pasos…'));
        dialog.appendChild(body);

        var footer = element('footer', 'help-dialog__footer');
        var fullManual = element('a', 'help-dialog__manual', 'Abrir manual completo');
        fullManual.href = '/gestion/ayuda/?desde=' + encodeURIComponent(window.location.pathname + window.location.search);
        footer.appendChild(fullManual);
        var understood = element('button', 'help-dialog__understood', 'Entendido');
        understood.type = 'button';
        understood.addEventListener('click', closeDialog);
        footer.appendChild(understood);
        dialog.appendChild(footer);

        dialog.addEventListener('cancel', function (event) {
            event.preventDefault();
            closeDialog();
        });
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) {
                closeDialog();
            }
        });

        document.body.appendChild(dialog);
        state.dialog = dialog;
        return dialog;
    }

    function renderContext(context) {
        var dialog = buildDialog();
        var title = dialog.querySelector('[data-help-dialog-title]');
        var body = dialog.querySelector('[data-help-dialog-body]');
        title.textContent = context.title || 'Ayuda general';
        body.textContent = '';

        body.appendChild(element('p', 'help-dialog__summary', context.summary || 'Sigue estos pasos para trabajar en esta pantalla.'));

        var audience = element('p', 'help-dialog__audience');
        audience.appendChild(element('strong', '', 'Pensado para: '));
        audience.appendChild(document.createTextNode(context.audience || 'Todo el personal'));
        body.appendChild(audience);

        body.appendChild(element('h3', '', 'Hazlo paso a paso'));
        var steps = element('ol', 'help-dialog__steps');
        (context.steps || []).forEach(function (step) {
            steps.appendChild(element('li', '', step));
        });
        body.appendChild(steps);

        if (context.tips && context.tips.length) {
            var tips = element('div', 'help-dialog__tips');
            tips.appendChild(element('strong', '', 'Ten en cuenta'));
            var list = element('ul', '');
            context.tips.forEach(function (tip) {
                list.appendChild(element('li', '', tip));
            });
            tips.appendChild(list);
            body.appendChild(tips);
        }

        if (context.links && context.links.length) {
            var links = element('div', 'help-dialog__links');
            context.links.forEach(function (linkData) {
                if (String(linkData.href || '').indexOf('/gestion/ayuda/') === 0) {
                    return;
                }
                var link = element('a', 'help-dialog__link', linkData.label);
                link.href = linkData.href;
                links.appendChild(link);
            });
            if (links.children.length) {
                body.appendChild(links);
            }
        }
    }

    function showDialog(trigger) {
        state.lastTrigger = trigger || document.activeElement;
        var route = trigger && trigger.getAttribute
            ? trigger.getAttribute('data-help-route')
            : '';
        route = route || (window.location.pathname + window.location.search);
        var dialog = buildDialog();
        document.body.classList.add('help-dialog-open');
        if (typeof dialog.showModal === 'function') {
            if (!dialog.open) {
                dialog.showModal();
            }
        } else {
            dialog.setAttribute('open', 'open');
        }

        if (state.context && state.contextRoute === route) {
            renderContext(state.context);
            return;
        }

        fetch('/gestion/ayuda/contexto.php?ruta=' + encodeURIComponent(route), {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {'Accept': 'application/json'}
        })
            .then(function (response) {
                if (response.status === 401) {
                    window.location.href = '/index.php';
                    return null;
                }
                if (!response.ok) {
                    throw new Error('No se ha podido cargar la ayuda.');
                }
                return response.json();
            })
            .then(function (payload) {
                if (!payload) {
                    return;
                }
                if (!payload.ok || !payload.context) {
                    throw new Error(payload.message || 'No se ha podido cargar la ayuda.');
                }
                state.context = payload.context;
                state.contextRoute = route;
                renderContext(state.context);
            })
            .catch(function () {
                renderContext({
                    title: 'Ayuda general',
                    summary: 'Comprueba el centro, el curso y la fecha antes de trabajar.',
                    audience: 'Todo el personal',
                    steps: [
                        'Lee el título de la pantalla.',
                        'Completa los campos necesarios.',
                        'Pulsa Guardar una sola vez y espera la confirmación.',
                        'Abre el manual completo si necesitas instrucciones del módulo.'
                    ],
                    tips: ['No cierres la pantalla mientras se está guardando.'],
                    links: [{label: 'Abrir manual completo', href: '/gestion/ayuda/'}]
                });
            });
    }

    function createFloatingTrigger() {
        if (
            document.querySelector('[data-help-open]')
            || document.body.classList.contains('panel-login')
            || document.querySelector('.legacy-admin-login')
        ) {
            return;
        }
        var trigger = element('button', 'help-floating-trigger', '? Ayuda');
        trigger.type = 'button';
        trigger.setAttribute('data-help-open', '');
        trigger.setAttribute('aria-label', 'Abrir ayuda de esta pantalla');
        document.body.appendChild(trigger);
    }

    function bindTriggers() {
        createFloatingTrigger();
        Array.prototype.forEach.call(document.querySelectorAll('[data-help-open]'), function (trigger) {
            if (trigger.getAttribute('data-help-bound') === 'true') {
                return;
            }
            trigger.setAttribute('data-help-bound', 'true');
            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                showDialog(trigger);
            });
        });
    }

    function normalizeSearch(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function bindManualSearch() {
        var input = document.querySelector('[data-help-search]');
        var cards = Array.prototype.slice.call(document.querySelectorAll('[data-help-card]'));
        var count = document.querySelector('[data-help-count]');
        var empty = document.querySelector('[data-help-empty]');
        if (!input || !cards.length) {
            return;
        }

        input.addEventListener('input', function () {
            var query = normalizeSearch(input.value);
            var visible = 0;
            cards.forEach(function (card) {
                var matches = !query || normalizeSearch(card.getAttribute('data-help-search-text')).indexOf(query) !== -1;
                card.hidden = !matches;
                if (matches) {
                    visible += 1;
                    if (query) {
                        var details = card.querySelector('details');
                        if (details) {
                            details.open = true;
                        }
                    }
                }
            });
            if (count) {
                count.textContent = visible + (visible === 1 ? ' apartado encontrado' : ' apartados encontrados');
            }
            if (empty) {
                empty.hidden = visible !== 0;
            }
        });
    }

    function init() {
        ensureStylesheet();
        bindTriggers();
        bindManualSearch();
        document.addEventListener('keydown', function (event) {
            if (event.key === 'F1') {
                event.preventDefault();
                showDialog(document.querySelector('[data-help-open]'));
            }
        });
        window.LapicesHelp.ready = true;
    }

    window.LapicesHelp = {
        ready: false,
        open: showDialog,
        close: closeDialog
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
