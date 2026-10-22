(function () {
    'use strict';

    var toggle = document.querySelector('[data-password-toggle]');
    var password = document.getElementById('pass');
    var form = document.querySelector('[data-familias-login-form]');
    var submit = document.querySelector('[data-login-submit]');

    if (toggle && password) {
        toggle.addEventListener('click', function () {
            var willShow = password.type === 'password';
            password.type = willShow ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', willShow ? 'true' : 'false');
            toggle.setAttribute(
                'aria-label',
                willShow ? 'Ocultar contraseña' : 'Mostrar contraseña'
            );
            password.focus();
        });
    }

    if (form && submit) {
        form.addEventListener('submit', function () {
            submit.disabled = true;
            submit.querySelector('span:first-child').textContent =
                'Comprobando acceso…';
        });
    }
}());
