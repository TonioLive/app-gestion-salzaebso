(function () {
    'use strict';

    var shell = document.querySelector('[data-app-shell]');
    var toggle = document.querySelector('[data-menu-toggle]');
    var backdrop = document.querySelector('[data-sidebar-backdrop]');

    function setMenu(open) {
        if (!shell || !toggle) {
            return;
        }

        shell.classList.toggle('app-shell--menu-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('menu-open', open);
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            setMenu(!shell.classList.contains('app-shell--menu-open'));
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            setMenu(false);
        });
    }

    document.querySelectorAll('.sidebar__nav a').forEach(function (link) {
        link.addEventListener('click', function () {
            setMenu(false);
        });
    });

    var passwordToggle = document.querySelector('[data-password-toggle]');
    var passwordInput = document.querySelector('[data-password-input]');
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function () {
            var visible = passwordInput.type === 'text';
            passwordInput.type = visible ? 'password' : 'text';
            passwordToggle.textContent = visible ? 'Mostrar' : 'Ocultar';
            passwordToggle.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    }

    var centreSelect = document.querySelector('[data-centre-select]');
    if (centreSelect) {
        centreSelect.addEventListener('change', function () {
            if (centreSelect.value !== '') {
                centreSelect.form.submit();
            }
        });
    }

    var personalizationPage = document.querySelector('[data-personalization-page]');
    if (personalizationPage) {
        var themeColor = personalizationPage.querySelector('[data-theme-color]');
        var themeColorValue = personalizationPage.querySelector('[data-theme-color-value]');
        var themePreview = personalizationPage.querySelector('[data-theme-preview]');
        var themeSwatches = Array.prototype.slice.call(
            personalizationPage.querySelectorAll('[data-theme-swatch]')
        );

        function updateThemeChoice(color) {
            if (!/^#[0-9a-f]{6}$/i.test(color)) {
                return;
            }

            if (themeColor) {
                themeColor.value = color;
            }
            if (themeColorValue) {
                themeColorValue.textContent = color.toUpperCase();
            }
            if (themePreview) {
                themePreview.style.setProperty('--preview-color', color);
            }

            themeSwatches.forEach(function (swatch) {
                swatch.classList.toggle(
                    'theme-swatch--active',
                    swatch.getAttribute('data-theme-swatch').toUpperCase() === color.toUpperCase()
                );
            });
        }

        if (themeColor) {
            updateThemeChoice(themeColor.value);
            themeColor.addEventListener('input', function () {
                updateThemeChoice(themeColor.value);
            });
        }

        themeSwatches.forEach(function (swatch) {
            swatch.addEventListener('click', function () {
                updateThemeChoice(swatch.getAttribute('data-theme-swatch'));
            });
        });
    }

    var centreSummary = document.querySelector('[data-centre-summary]');
    if (centreSummary && window.fetch) {
        var summaryEndpoint = centreSummary.getAttribute('data-summary-endpoint');
        var summaryTimer = null;

        function setSummaryValue(key, value) {
            var target = centreSummary.querySelector('[data-stat="' + key + '"]');
            if (target) {
                target.textContent = String(value);
            }
        }

        function refreshCentreSummary() {
            if (document.hidden || !summaryEndpoint) {
                return;
            }

            fetch(summaryEndpoint, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {'Accept': 'application/json'}
            })
                .then(function (response) {
                    return response.ok ? response.json() : null;
                })
                .then(function (payload) {
                    if (!payload || !payload.ok || !payload.stats) {
                        return;
                    }

                    setSummaryValue('alumnos', payload.stats.alumnos);
                    setSummaryValue('asistencia', payload.stats.asistencia);
                    setSummaryValue('comedor', payload.stats.comedor);
                    setSummaryValue('aulas', payload.stats.aulas);
                    setSummaryValue('porcentaje_asistencia', payload.stats.porcentaje_asistencia);

                    var progress = centreSummary.querySelector('[data-stat-progress]');
                    if (progress) {
                        progress.style.width = Math.max(
                            0,
                            Math.min(100, Number(payload.stats.porcentaje_asistencia) || 0)
                        ) + '%';
                    }

                    var updated = centreSummary.querySelector('[data-stat-updated]');
                    if (updated) {
                        updated.textContent = payload.stats.actualizado;
                    }
                })
                .catch(function () {
                    // Se mantiene el último resumen visible si falla una actualización.
                });
        }

        summaryTimer = window.setInterval(refreshCentreSummary, 30000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                refreshCentreSummary();
            }
        });

        window.addEventListener('beforeunload', function () {
            if (summaryTimer) {
                window.clearInterval(summaryTimer);
            }
        });
    }
}());
