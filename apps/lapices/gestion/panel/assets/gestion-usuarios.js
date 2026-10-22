(function () {
    'use strict';

    function normalise(value) {
        var text = String(value || '').toLocaleLowerCase();
        return typeof text.normalize === 'function'
            ? text.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            : text;
    }

    function initialise() {
        var page = document.querySelector('.legacy-users-page');
        if (!page) {
            return;
        }

        var search = page.querySelector('[data-users-search]');
        var rows = Array.prototype.slice.call(page.querySelectorAll('[data-user-row]'));
        var empty = page.querySelector('[data-users-empty]');
        var passwordHelp = page.querySelector('[data-password-help-toggle]');
        var help = page.querySelector('#gestion-password-help');
        var revealForm = page.querySelector('[data-password-reveal-form]');
        var confirmation = page.querySelector('#confirmacion-password-admin');
        var feedback = page.querySelector('[data-password-feedback]');
        var hideButton = page.querySelector('[data-password-hide]');
        var hideTimer;
        var requestVersion = 0;
        var pending = false;

        function clearPasswords(message) {
            requestVersion += 1;
            window.clearTimeout(hideTimer);
            rows.forEach(function (row) {
                var field = row.querySelector('[data-password-value]');
                if (field) { field.textContent = ''; field.hidden = true; }
            });
            if (confirmation) { confirmation.value = ''; }
            if (message && feedback) { feedback.textContent = message; }
        }

        if (passwordHelp && help) {
            passwordHelp.addEventListener('click', function () {
                help.hidden = !help.hidden;
                passwordHelp.setAttribute('aria-expanded', help.hidden ? 'false' : 'true');
                passwordHelp.textContent = help.hidden ? 'Mostrar contraseñas' : 'Ocultar contraseñas';
                if (help.hidden) { clearPasswords('Contraseñas ocultas.'); }
                else if (confirmation) { confirmation.focus(); }
            });
        }

        if (hideButton) {
            hideButton.addEventListener('click', function () { clearPasswords('Contraseñas ocultas.'); });
        }
        if (revealForm) {
            revealForm.addEventListener('submit', function (event) {
                event.preventDefault();
                if (pending) { return; }
                var data = new FormData(revealForm);
                rows.forEach(function (row) { data.append('usuarios[]', row.getAttribute('data-user-id')); });
                clearPasswords();
                var version = requestVersion;
                var submit = revealForm.querySelector('[type="submit"]');
                pending = true;
                submit.disabled = true;
                feedback.textContent = 'Comprobando acceso…';
                fetch(revealForm.action, {method:'POST',body:data,credentials:'same-origin',cache:'no-store'})
                    .then(function (response) { return response.json(); })
                    .then(function (result) {
                        if (version !== requestVersion) { return; }
                        if (!result.ok) { throw new Error(result.mensaje || 'No se ha podido realizar la consulta.'); }
                        var values = {};
                        result.usuarios.forEach(function (item) { values[String(item.id)] = item; });
                        var available = 0;
                        rows.forEach(function (row) {
                            var field = row.querySelector('[data-password-value]');
                            var item = values[row.getAttribute('data-user-id')];
                            if (!field || !item) { return; }
                            field.textContent = item.password !== null ? item.password : (item.estado === 'sin_configurar' ? 'Sin contraseña configurada' : 'No disponible para consulta');
                            field.hidden = false;
                            if (item.password !== null) { available += 1; }
                        });
                        feedback.textContent = available + ' contraseña(s) disponible(s). Se ocultarán en un minuto.';
                        hideTimer = window.setTimeout(function () { clearPasswords('Tiempo de consulta agotado. Confirma tu contraseña para volver a mostrar.'); },60000);
                    })
                    .catch(function (error) {
                        if (version === requestVersion) { feedback.textContent = error instanceof SyntaxError ? 'La sesión ha caducado. Recarga la página.' : error.message; }
                    })
                    .then(function () { pending = false; submit.disabled = false; data = null; });
            });
        }
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) { clearPasswords('Contraseñas ocultas. Confirma de nuevo para consultar.'); }
        });
        window.addEventListener('pagehide', function () { clearPasswords(); });

        if (search) {
            search.addEventListener('input', function () {
                var term = normalise(search.value.trim());
                var visibleRows = 0;

                rows.forEach(function (row) {
                    var matches = term === ''
                        || normalise(row.getAttribute('data-search')).indexOf(term) !== -1;
                    row.hidden = !matches;
                    if (matches) {
                        visibleRows += 1;
                    }
                });

                if (empty) {
                    empty.hidden = visibleRows !== 0;
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialise);
    } else {
        initialise();
    }
}());
