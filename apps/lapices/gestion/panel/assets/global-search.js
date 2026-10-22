(function (window, document) {
    'use strict';

    if (window.LapicesGlobalSearch) {
        window.LapicesGlobalSearch.initAll();
        return;
    }

    function createElement(tag, className, text) {
        var element = document.createElement(tag);
        if (className) {
            element.className = className;
        }
        if (typeof text === 'string') {
            element.textContent = text;
        }
        return element;
    }

    function init(root) {
        if (!root || root.getAttribute('data-global-search-ready') === '1') {
            return;
        }

        var endpoint = root.getAttribute('data-global-search-endpoint');
        var toggle = root.querySelector('[data-global-search-toggle]');
        var closeButton = root.querySelector('[data-global-search-close]');
        var input = root.querySelector('[data-global-search-input]');
        var clearButton = root.querySelector('[data-global-search-clear]');
        var results = root.querySelector('[data-global-search-results]');
        var status = root.querySelector('[data-global-search-status]');
        var timer = null;
        var request = null;
        var activeIndex = -1;
        var resultLinks = [];
        var lastPayload = null;

        if (!endpoint || !input || !results) {
            return;
        }

        root.setAttribute('data-global-search-ready', '1');

        function isCompact() {
            return toggle && window.getComputedStyle(toggle).display !== 'none';
        }

        function setExpanded(expanded) {
            input.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            if (toggle) {
                toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }
        }

        function openSearch() {
            root.classList.add('global-search--open');
            if (isCompact()) {
                document.body.classList.add('global-search-open');
            }
            window.setTimeout(function () {
                input.focus();
                if (lastPayload && input.value.trim().length >= 2) {
                    results.hidden = false;
                    setExpanded(true);
                }
            }, 20);
        }

        function closeSearch() {
            root.classList.remove('global-search--open');
            document.body.classList.remove('global-search-open');
            results.hidden = true;
            activeIndex = -1;
            setExpanded(false);
        }

        function updateClearButton() {
            if (clearButton) {
                clearButton.hidden = input.value.length === 0;
            }
        }

        function announce(message) {
            if (status) {
                status.textContent = message;
            }
        }

        function resetActive() {
            resultLinks.forEach(function (link) {
                link.classList.remove('is-active');
                link.removeAttribute('aria-selected');
            });
            activeIndex = -1;
            input.removeAttribute('aria-activedescendant');
        }

        function setActive(index) {
            if (!resultLinks.length) {
                return;
            }

            if (index < 0) {
                index = resultLinks.length - 1;
            }
            if (index >= resultLinks.length) {
                index = 0;
            }

            resetActive();
            activeIndex = index;
            resultLinks[activeIndex].classList.add('is-active');
            resultLinks[activeIndex].setAttribute('aria-selected', 'true');
            input.setAttribute('aria-activedescendant', resultLinks[activeIndex].id);
            resultLinks[activeIndex].scrollIntoView({block: 'nearest'});
        }

        function renderState(title, copy, loading) {
            var state = createElement('div', 'global-search__state');
            var content = createElement('div');
            if (loading) {
                content.appendChild(createElement('div', 'global-search__loader'));
            }
            content.appendChild(createElement('strong', '', title));
            if (copy) {
                content.appendChild(createElement('span', '', copy));
            }
            state.appendChild(content);
            results.replaceChildren(state);
            results.hidden = false;
            resultLinks = [];
            resetActive();
            setExpanded(true);
        }

        function renderPayload(payload) {
            var fragment = document.createDocumentFragment();
            var total = 0;
            var itemNumber = 0;

            resultLinks = [];
            activeIndex = -1;

            (payload.groups || []).forEach(function (group) {
                if (!group.items || !group.items.length) {
                    return;
                }

                var section = createElement('section', 'global-search__group');
                var heading = createElement('div', 'global-search__group-heading');
                heading.appendChild(createElement('span', '', group.label));
                heading.appendChild(createElement('span', '', String(group.items.length)));
                section.appendChild(heading);

                group.items.forEach(function (item) {
                    var link = createElement('a', 'global-search__result');
                    var mark = createElement('span', 'global-search__result-mark', item.mark || 'IR');
                    var copy = createElement('span', 'global-search__result-copy');
                    var title = createElement('strong', '', item.title || 'Abrir');
                    var meta = createElement('span', '', item.meta || group.label);
                    var arrow = createElement('span', 'global-search__result-arrow', '›');

                    itemNumber += 1;
                    link.id = results.id + '-option-' + itemNumber;
                    link.href = item.href;
                    link.setAttribute('role', 'option');
                    link.setAttribute('data-kind', item.kind || 'opcion');
                    copy.appendChild(title);
                    copy.appendChild(meta);
                    link.appendChild(mark);
                    link.appendChild(copy);
                    link.appendChild(arrow);
                    section.appendChild(link);
                    resultLinks.push(link);
                    total += 1;
                });

                fragment.appendChild(section);
            });

            if (!total) {
                renderState(
                    'No hay coincidencias',
                    'Prueba con otro nombre, teléfono, servicio u opción del programa.',
                    false
                );
                announce('No se han encontrado resultados');
                return;
            }

            var footer = createElement('div', 'global-search__footer');
            footer.appendChild(createElement('span', '', total + (total === 1 ? ' resultado' : ' resultados')));
            if (payload.more_href) {
                var more = createElement('a', '', 'Búsqueda avanzada');
                more.href = payload.more_href;
                footer.appendChild(more);
            } else {
                footer.appendChild(createElement('span', '', '↑ ↓ para moverte'));
            }
            fragment.appendChild(footer);

            results.replaceChildren(fragment);
            results.hidden = false;
            setExpanded(true);
            announce(total + (total === 1 ? ' resultado encontrado' : ' resultados encontrados'));
        }

        function runSearch() {
            var query = input.value.trim();
            updateClearButton();

            if (query.length < 2) {
                lastPayload = null;
                if (query.length) {
                    renderState('Sigue escribiendo', 'Introduce al menos dos caracteres.', false);
                } else {
                    results.hidden = true;
                    setExpanded(false);
                }
                return;
            }

            if (request && typeof request.abort === 'function') {
                request.abort();
            }
            request = window.AbortController ? new window.AbortController() : null;
            renderState('Buscando…', 'Alumnos, servicios y opciones disponibles.', true);

            window.fetch(endpoint + '?q=' + encodeURIComponent(query), {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {'Accept': 'application/json'},
                signal: request ? request.signal : undefined
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('search-error');
                    }
                    return response.json();
                })
                .then(function (payload) {
                    if (input.value.trim() !== query) {
                        return;
                    }
                    lastPayload = payload;
                    renderPayload(payload);
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') {
                        return;
                    }
                    renderState(
                        'No se ha podido buscar',
                        'Comprueba la conexión e inténtalo de nuevo.',
                        false
                    );
                    announce('La búsqueda no está disponible en este momento');
                });
        }

        input.addEventListener('input', function () {
            window.clearTimeout(timer);
            updateClearButton();
            timer = window.setTimeout(runSearch, 220);
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 2) {
                if (lastPayload) {
                    renderPayload(lastPayload);
                } else {
                    runSearch();
                }
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActive(activeIndex + 1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActive(activeIndex - 1);
            } else if (event.key === 'Enter' && resultLinks.length) {
                event.preventDefault();
                window.location.href = resultLinks[activeIndex >= 0 ? activeIndex : 0].href;
            } else if (event.key === 'Escape') {
                event.preventDefault();
                if (isCompact()) {
                    closeSearch();
                } else {
                    results.hidden = true;
                    resetActive();
                    setExpanded(false);
                    input.blur();
                }
            }
        });

        if (toggle) {
            toggle.addEventListener('click', openSearch);
        }
        if (closeButton) {
            closeButton.addEventListener('click', closeSearch);
        }
        if (clearButton) {
            clearButton.addEventListener('click', function () {
                input.value = '';
                lastPayload = null;
                updateClearButton();
                results.hidden = true;
                setExpanded(false);
                input.focus();
            });
        }

        document.addEventListener('click', function (event) {
            if (!isCompact() && !root.contains(event.target)) {
                results.hidden = true;
                resetActive();
                setExpanded(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            var target = event.target;
            var isTyping = target && (
                target.tagName === 'INPUT'
                || target.tagName === 'TEXTAREA'
                || target.tagName === 'SELECT'
                || target.isContentEditable
            );

            if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
                event.preventDefault();
                openSearch();
            } else if (event.key === '/' && !isTyping) {
                event.preventDefault();
                openSearch();
            }
        });

        window.addEventListener('resize', function () {
            if (!isCompact()) {
                root.classList.remove('global-search--open');
                document.body.classList.remove('global-search-open');
            }
        });
    }

    function initAll() {
        Array.prototype.forEach.call(
            document.querySelectorAll('[data-global-search]'),
            init
        );
    }

    window.LapicesGlobalSearch = {
        initAll: initAll
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
}(window, document));
