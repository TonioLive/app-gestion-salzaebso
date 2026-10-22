(function ($) {
    'use strict';

    if (!$ || !document.querySelector('.familias-agenda-page')) {
        return;
    }

    var mobileQuery = window.matchMedia('(max-width: 767px)');
    var mobileMonth = moment().startOf('month');
    var mobileRoot = $('[data-familias-agenda-mobile]');
    var daysRoot = $('[data-agenda-days]');
    var emptyRoot = $('[data-agenda-empty]');
    var loadingRoot = $('[data-agenda-loading]');
    var periodRoot = $('[data-agenda-period]');
    var monthNames = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];
    var dayNames = [
        'domingo', 'lunes', 'martes', 'mi\u00e9rcoles',
        'jueves', 'viernes', 's\u00e1bado'
    ];

    function validColor(value) {
        return /^#[0-9a-f]{3,8}$/i.test(String(value || ''))
            ? value
            : '#176255';
    }

    function eventMoment(event) {
        return moment(event.start);
    }

    function eventTime(event) {
        var start = eventMoment(event);
        var rawStart = String(event.start || '');
        var hasTime = rawStart.indexOf('T') !== -1 ||
            /\s[0-9]{1,2}:[0-9]{2}/.test(rawStart);

        if (!hasTime || !start.isValid()) {
            return 'Todo el d\u00eda';
        }

        var label = start.format('HH:mm');
        if (event.end) {
            var end = moment(event.end);
            if (end.isValid()) {
                label += ' - ' + end.format('HH:mm');
            }
        }

        return label;
    }

    function createEventItem(event) {
        var link = $('<a class="familias-agenda-event"></a>');
        var marker = $('<span class="familias-agenda-event__marker"></span>');
        var copy = $('<span class="familias-agenda-event__copy"></span>');
        var time = $('<small></small>').text(eventTime(event));
        var title = $('<strong></strong>').text(
            String(event.title || 'Evento').replace(/\s+/g, ' ').trim()
        );
        var arrow = $('<i class="fa fa-chevron-right" aria-hidden="true"></i>');

        marker.css('background-color', validColor(event.color));
        copy.append(time, title);
        link.append(marker, copy);

        if (event.url) {
            link.attr('href', event.url);
            link.append(arrow);
        } else {
            link.attr('role', 'group');
        }

        return link;
    }

    function renderMobileEvents(events) {
        var groups = {};
        var seen = {};
        var sorted = (events || []).slice().sort(function (left, right) {
            return eventMoment(left).valueOf() - eventMoment(right).valueOf();
        });

        daysRoot.empty();

        $.each(sorted, function (_, event) {
            var start = eventMoment(event);
            if (!start.isValid()) {
                return;
            }

            var dateKey = start.format('YYYY-MM-DD');
            var duplicateKey = [
                dateKey,
                String(event.title || '').replace(/\s+/g, ' ').trim(),
                eventTime(event)
            ].join('|');

            if (seen[duplicateKey]) {
                return;
            }
            seen[duplicateKey] = true;

            if (!groups[dateKey]) {
                groups[dateKey] = [];
            }
            groups[dateKey].push(event);
        });

        var keys = Object.keys(groups).sort();
        emptyRoot.prop('hidden', keys.length !== 0);

        $.each(keys, function (_, dateKey) {
            var date = moment(dateKey, 'YYYY-MM-DD');
            var group = $('<section class="familias-agenda-day"></section>');
            var heading = $('<header class="familias-agenda-day__date"></header>');
            var badge = $('<span></span>').text(date.format('DD'));
            var copy = $('<span></span>');
            var today = date.isSame(moment(), 'day');

            copy.append(
                $('<strong></strong>').text(dayNames[date.day()]),
                $('<small></small>').text(
                    today
                        ? 'Hoy'
                        : date.format('DD/MM/YYYY')
                )
            );
            heading.append(badge, copy);
            if (today) {
                group.addClass('is-today');
            }

            var list = $('<div class="familias-agenda-day__events"></div>');
            $.each(groups[dateKey], function (__, event) {
                list.append(createEventItem(event));
            });

            group.append(heading, list);
            daysRoot.append(group);
        });
    }

    function loadMobileAgenda() {
        var start = mobileMonth.clone().startOf('month');
        var end = mobileMonth.clone().add(1, 'month').startOf('month');

        periodRoot.text(
            monthNames[mobileMonth.month()] + ' ' + mobileMonth.year()
        );
        loadingRoot.show();
        emptyRoot.prop('hidden', true);
        daysRoot.empty();

        $.ajax({
            url: '/app/agenda/get-agenda.php',
            dataType: 'json',
            data: {
                start: start.format('YYYY-MM-DD'),
                end: end.format('YYYY-MM-DD')
            }
        }).done(function (events) {
            renderMobileEvents(events);
        }).fail(function () {
            emptyRoot
                .prop('hidden', false)
                .find('strong')
                .text('No se ha podido cargar la agenda');
            emptyRoot
                .find('p')
                .text('Comprueba tu conexi\u00f3n e int\u00e9ntalo de nuevo.');
        }).always(function () {
            loadingRoot.hide();
        });
    }

    function initialiseMobileAgenda() {
        mobileRoot.on('click', '[data-agenda-action]', function () {
            var action = $(this).data('agenda-action');

            if (action === 'prev') {
                mobileMonth.subtract(1, 'month');
            } else if (action === 'next') {
                mobileMonth.add(1, 'month');
            } else {
                mobileMonth = moment().startOf('month');
            }

            loadMobileAgenda();
        });

        loadMobileAgenda();
    }

    function initialiseDesktopAgenda() {
        $('#calendar').fullCalendar({
            eventStartEditable: false,
            lang: 'es',
            header: {
                left: 'title',
                center: '',
                right: 'prev,next today month,agendaWeek,agendaDay'
            },
            events: {
                url: '/app/agenda/get-agenda.php'
            },
            eventClick: function (event) {
                if (event.url) {
                    window.open(
                        event.url,
                        'info_evento',
                        'toolbar=false,scrollbars=yes,resizable=true,width=800,height=600'
                    );
                    return false;
                }
            },
            eventRender: function (event, element) {
                element.attr('title', String(event.title || ''));
            },
            editable: false,
            droppable: false,
            contentHeight: 'auto'
        });
    }

    $(function () {
        if (mobileQuery.matches) {
            initialiseMobileAgenda();
        } else {
            initialiseDesktopAgenda();
        }
    });
}(window.jQuery));
