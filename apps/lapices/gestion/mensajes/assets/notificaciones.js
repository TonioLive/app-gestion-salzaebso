(function () {
    'use strict';

    var endpoint = '/gestion/mensajes/api.php?action=notifications';
    var widgets = Array.prototype.slice.call(document.querySelectorAll('[data-message-notifications]'));
    if (!widgets.length) {
        return;
    }

    var lastPayload = null;

    function create(tag, className, text) {
        var node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        if (text !== undefined) {
            node.textContent = text;
        }
        return node;
    }

    function initials(name) {
        return String(name || 'Familia').trim().split(/\s+/).slice(0, 2).map(function (part) {
            return part.charAt(0).toUpperCase();
        }).join('') || 'F';
    }

    function shortTime(value) {
        if (!value) {
            return '';
        }
        var date = new Date(String(value).replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) {
            return '';
        }
        var now = new Date();
        if (date.toDateString() === now.toDateString()) {
            return new Intl.DateTimeFormat('es-ES', { hour: '2-digit', minute: '2-digit' }).format(date);
        }
        return new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: '2-digit' }).format(date);
    }

    function renderWidget(widget, payload) {
        var count = widget.querySelector('[data-message-notification-count]');
        var summary = widget.querySelector('[data-message-notification-summary]');
        var list = widget.querySelector('[data-message-notification-list]');
        var total = Number(payload.total_unread || 0);
        count.textContent = total > 99 ? '99+' : String(total);
        count.hidden = total <= 0;
        summary.textContent = total > 0
            ? total + (total === 1 ? ' mensaje por leer' : ' mensajes por leer')
            : 'Todo al día';
        list.textContent = '';

        if (!Array.isArray(payload.items) || payload.items.length === 0) {
            list.appendChild(create('div', 'message-notifications__empty', 'No tienes mensajes nuevos de las familias.'));
            return;
        }

        payload.items.forEach(function (item) {
            var link = create('a', 'message-notifications__item');
            link.href = item.url;
            link.appendChild(create('span', 'message-notifications__avatar', initials(item.student_name)));
            var copy = create('span', 'message-notifications__copy');
            copy.appendChild(create('strong', '', item.student_name));
            copy.appendChild(create('span', '', item.preview));
            copy.appendChild(create('small', '', item.classroom_name + ' · ' + shortTime(item.last_at)));
            link.appendChild(copy);
            link.appendChild(create('span', 'message-notifications__quantity', String(item.unread_count)));
            list.appendChild(link);
        });
    }

    function showToast(payload) {
        var total = Number(payload.total_unread || 0);
        if (total <= 0 || !payload.items || !payload.items.length) {
            return;
        }
        var userId = widgets[0].dataset.userId || '0';
        var first = payload.items[0];
        var key = 'lapices-message-notification-' + userId;
        var marker = first.id + '|' + total;
        try {
            if (window.sessionStorage.getItem(key) === marker) {
                return;
            }
            window.sessionStorage.setItem(key, marker);
        } catch (error) {
            // El aviso sigue funcionando aunque el navegador bloquee sessionStorage.
        }

        var oldToast = document.querySelector('.message-notification-toast');
        if (oldToast) {
            oldToast.remove();
        }
        var toast = create('aside', 'message-notification-toast');
        toast.setAttribute('role', 'status');
        toast.appendChild(create('span', 'message-notification-toast__icon', String(total)));
        var copy = create('span', 'message-notification-toast__copy');
        copy.appendChild(create('strong', '', total === 1 ? 'Nuevo mensaje de una familia' : 'Mensajes de familias pendientes'));
        copy.appendChild(create('span', '', first.student_name + ': ' + first.preview));
        toast.appendChild(copy);
        var link = create('a', '', 'Abrir');
        link.href = first.url;
        toast.appendChild(link);
        document.body.appendChild(toast);
        window.setTimeout(function () {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 7000);
    }

    function loadNotifications(showNewToast) {
        return fetch(endpoint, { credentials: 'same-origin', cache: 'no-store' })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('No disponible');
                }
                return response.json();
            })
            .then(function (payload) {
                if (!payload.ok) {
                    return;
                }
                widgets.forEach(function (widget) { renderWidget(widget, payload); });
                if (showNewToast) {
                    showToast(payload);
                }
                lastPayload = payload;
            })
            .catch(function () {
                return null;
            });
    }

    widgets.forEach(function (widget) {
        var toggle = widget.querySelector('[data-message-notification-toggle]');
        var panel = widget.querySelector('[data-message-notification-panel]');
        toggle.addEventListener('click', function () {
            var willOpen = panel.hidden;
            widgets.forEach(function (other) {
                var otherToggle = other.querySelector('[data-message-notification-toggle]');
                var otherPanel = other.querySelector('[data-message-notification-panel]');
                otherPanel.hidden = true;
                otherToggle.setAttribute('aria-expanded', 'false');
            });
            panel.hidden = !willOpen;
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            if (willOpen && lastPayload) {
                renderWidget(widget, lastPayload);
            }
        });
    });

    document.addEventListener('click', function (event) {
        widgets.forEach(function (widget) {
            if (!widget.contains(event.target)) {
                widget.querySelector('[data-message-notification-panel]').hidden = true;
                widget.querySelector('[data-message-notification-toggle]').setAttribute('aria-expanded', 'false');
            }
        });
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            widgets.forEach(function (widget) {
                widget.querySelector('[data-message-notification-panel]').hidden = true;
                widget.querySelector('[data-message-notification-toggle]').setAttribute('aria-expanded', 'false');
            });
        }
    });
    document.addEventListener('lapices:messages-changed', function () {
        loadNotifications(false);
    });
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            loadNotifications(false);
        }
    });

    loadNotifications(true);
    window.setInterval(function () {
        if (document.visibilityState === 'visible') {
            loadNotifications(true);
        }
    }, 45000);
})();

