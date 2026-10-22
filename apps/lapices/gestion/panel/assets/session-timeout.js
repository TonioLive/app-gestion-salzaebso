(function () {
    'use strict';

    if (window.LapicesSessionTimeoutBooted) {
        return;
    }
    window.LapicesSessionTimeoutBooted = true;

    var state = null;
    var expiresAt = 0;
    var lastHeartbeat = Date.now();
    var heartbeatTimer = null;
    var dialog = null;
    var countdown = null;
    var continueButton = null;
    var expired = false;

    function loadStyles() {
        if (document.querySelector('[data-session-timeout-style]')) {
            return;
        }
        var stylesheet = document.createElement('link');
        stylesheet.rel = 'stylesheet';
        stylesheet.href = '/gestion/panel/assets/session-timeout.css?v=20260909.1';
        stylesheet.setAttribute('data-session-timeout-style', 'true');
        document.head.appendChild(stylesheet);
    }

    function formatTime(seconds) {
        var safeSeconds = Math.max(0, Math.ceil(seconds));
        var minutes = Math.floor(safeSeconds / 60);
        var remainder = safeSeconds % 60;
        return String(minutes).padStart(2, '0') + ':' + String(remainder).padStart(2, '0');
    }

    function buildDialog() {
        if (dialog) {
            return;
        }

        dialog = document.createElement('div');
        dialog.className = 'session-timeout-dialog';
        dialog.hidden = true;
        dialog.setAttribute('role', 'dialog');
        dialog.setAttribute('aria-modal', 'true');
        dialog.setAttribute('aria-labelledby', 'session-timeout-title');
        dialog.innerHTML =
            '<section class="session-timeout-dialog__card">' +
                '<span class="session-timeout-dialog__icon" aria-hidden="true">!</span>' +
                '<h2 id="session-timeout-title">La sesión está a punto de cerrarse</h2>' +
                '<p>Por seguridad, las sesiones se cierran después de 30 minutos sin actividad.</p>' +
                '<strong class="session-timeout-dialog__countdown" aria-live="polite">02:00</strong>' +
                '<div class="session-timeout-dialog__actions">' +
                    '<a href="#" data-session-logout>Cerrar sesión</a>' +
                    '<button type="button" data-session-continue>Seguir trabajando</button>' +
                '</div>' +
            '</section>';
        document.body.appendChild(dialog);

        countdown = dialog.querySelector('.session-timeout-dialog__countdown');
        continueButton = dialog.querySelector('[data-session-continue]');
        dialog.querySelector('[data-session-logout]').href = state.logoutUrl;
        continueButton.addEventListener('click', function () {
            sendActivity(true);
        });
    }

    function redirectTo(url) {
        if (expired) {
            return;
        }
        expired = true;
        window.location.replace(url || (state && state.loginUrl) || '/index.php?sesion=expirada');
    }

    function applyServerState(data) {
        state = data;
        expiresAt = Date.now() + (Math.max(0, Number(data.remaining)) * 1000);
        lastHeartbeat = Date.now();
        buildDialog();
        checkRemaining();
    }

    function sendActivity(force) {
        if (!state || expired) {
            return;
        }

        var now = Date.now();
        if (!force && (now - lastHeartbeat) < 60000) {
            return;
        }
        lastHeartbeat = now;

        if (heartbeatTimer) {
            window.clearTimeout(heartbeatTimer);
        }
        heartbeatTimer = window.setTimeout(function () {
            var body = new URLSearchParams();
            body.set('_csrf', state.csrf);
            if (continueButton) {
                continueButton.disabled = true;
                continueButton.textContent = 'Actualizando…';
            }

            window.fetch(state.activityUrl, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: true,
                headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                body: body.toString()
            }).then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok || !data.ok) {
                        redirectTo(data.redirect || state.loginUrl);
                        return;
                    }
                    expiresAt = Date.now() + (Number(data.remaining) * 1000);
                    dialog.hidden = true;
                    continueButton.disabled = false;
                    continueButton.textContent = 'Seguir trabajando';
                });
            }).catch(function () {
                if (continueButton) {
                    continueButton.disabled = false;
                    continueButton.textContent = 'Reintentar';
                }
            });
        }, force ? 0 : 500);
    }

    function checkRemaining() {
        if (!state || expired) {
            return;
        }
        var remaining = Math.ceil((expiresAt - Date.now()) / 1000);
        if (remaining <= 0) {
            redirectTo(state.logoutUrl);
            return;
        }
        if (remaining <= Number(state.warning)) {
            dialog.hidden = false;
            countdown.textContent = formatTime(remaining);
        } else if (dialog) {
            dialog.hidden = true;
        }
    }

    function registerActivityListeners() {
        ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (eventName) {
            window.addEventListener(eventName, function () {
                sendActivity(false);
            }, {passive: true});
        });
        document.addEventListener('visibilitychange', function () {
            checkRemaining();
            if (!document.hidden) {
                sendActivity(true);
            }
        });
        window.setInterval(checkRemaining, 1000);
    }

    loadStyles();
    window.fetch('/configuracion/estado_sesion.php', {
        credentials: 'same-origin',
        headers: {'Accept': 'application/json'}
    }).then(function (response) {
        return response.json().then(function (data) {
            if (!response.ok || !data.authenticated) {
                if (data.expired) {
                    redirectTo(data.redirect);
                }
                return;
            }
            applyServerState(data);
            registerActivityListeners();
        });
    }).catch(function () {
        // El servidor sigue aplicando el limite aunque falle esta mejora visual.
    });
}());
