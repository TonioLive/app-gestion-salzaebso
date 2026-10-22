(function () {
    'use strict';

    var root = document.querySelector('[data-message-center]');
    if (!root) {
        return;
    }

    var configNode = root.querySelector('[data-message-config]');
    var config = {};
    try {
        config = JSON.parse(configNode ? configNode.textContent : '{}');
    } catch (error) {
        config = {};
    }

    var elements = {
        classroom: root.querySelector('[data-classroom-select]'),
        search: root.querySelector('[data-conversation-search]'),
        list: root.querySelector('[data-conversation-list]'),
        summary: root.querySelector('[data-conversation-summary]'),
        unread: root.querySelector('[data-total-unread]'),
        refresh: root.querySelector('[data-message-refresh]'),
        layout: root.querySelector('[data-message-layout]'),
        empty: root.querySelector('[data-chat-empty]'),
        active: root.querySelector('[data-chat-active]'),
        back: root.querySelector('[data-chat-back]'),
        history: root.querySelector('[data-chat-history]'),
        student: root.querySelector('[data-chat-student]'),
        family: root.querySelector('[data-chat-family]'),
        classroomName: root.querySelector('[data-chat-classroom]'),
        avatar: root.querySelector('[data-chat-avatar]'),
        state: root.querySelector('[data-chat-state]'),
        form: root.querySelector('[data-message-composer]'),
        text: root.querySelector('[data-message-text]'),
        send: root.querySelector('[data-send-message]'),
        composerStudent: root.querySelector('[data-composer-student]'),
        composerThread: root.querySelector('[data-composer-thread]'),
        composerStatus: root.querySelector('[data-composer-status]')
    };

    var state = {
        conversations: [],
        currentStudent: Number(config.student_id || 0),
        currentConversation: null,
        currentThread: 0,
        chatSignature: '',
        loadingChatToken: 0,
        initialStudent: Number(config.student_id || 0)
    };

    function request(url, options) {
        var settings = options || {};
        settings.credentials = 'same-origin';
        settings.headers = settings.headers || {};
        if (settings.body instanceof URLSearchParams) {
            settings.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
        }
        return fetch(url, settings).then(function (response) {
            return response.json().catch(function () {
                return { ok: false, message: 'La respuesta del servidor no es válida.' };
            }).then(function (payload) {
                if (!response.ok || !payload.ok) {
                    throw new Error(payload.message || 'No se ha podido completar la operación.');
                }
                return payload;
            });
        });
    }

    function normalize(value) {
        var text = String(value || '').toLowerCase();
        return typeof text.normalize === 'function'
            ? text.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            : text;
    }

    function initials(name) {
        var parts = String(name || 'Familia').trim().split(/\s+/).filter(Boolean);
        return parts.slice(0, 2).map(function (part) {
            return part.charAt(0).toUpperCase();
        }).join('') || 'F';
    }

    function parseDate(value) {
        if (!value) {
            return null;
        }
        var parsed = new Date(String(value).replace(' ', 'T'));
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function sameDay(left, right) {
        return left && right
            && left.getFullYear() === right.getFullYear()
            && left.getMonth() === right.getMonth()
            && left.getDate() === right.getDate();
    }

    function shortTime(value) {
        var date = parseDate(value);
        if (!date) {
            return '';
        }
        var now = new Date();
        var yesterday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
        if (sameDay(date, now)) {
            return new Intl.DateTimeFormat('es-ES', { hour: '2-digit', minute: '2-digit' }).format(date);
        }
        if (sameDay(date, yesterday)) {
            return 'Ayer';
        }
        var elapsed = now.getTime() - date.getTime();
        if (elapsed >= 0 && elapsed < 6 * 86400000) {
            return new Intl.DateTimeFormat('es-ES', { weekday: 'short' }).format(date).replace('.', '');
        }
        return new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: '2-digit', year: '2-digit' }).format(date);
    }

    function dayLabel(value) {
        var date = parseDate(value);
        if (!date) {
            return '';
        }
        var now = new Date();
        var yesterday = new Date(now.getFullYear(), now.getMonth(), now.getDate() - 1);
        if (sameDay(date, now)) {
            return 'Hoy';
        }
        if (sameDay(date, yesterday)) {
            return 'Ayer';
        }
        return new Intl.DateTimeFormat('es-ES', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        }).format(date);
    }

    function exactTime(value) {
        var date = parseDate(value);
        return date
            ? new Intl.DateTimeFormat('es-ES', { hour: '2-digit', minute: '2-digit' }).format(date)
            : '';
    }

    function create(tag, className, text) {
        var node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        if (text !== undefined && text !== null) {
            node.textContent = text;
        }
        return node;
    }

    function filteredConversations() {
        var query = normalize(elements.search ? elements.search.value : '');
        if (!query) {
            return state.conversations;
        }
        return state.conversations.filter(function (conversation) {
            return normalize(
                conversation.student_name + ' ' + conversation.family_name + ' '
                + conversation.classroom_name + ' ' + conversation.last_preview
            ).indexOf(query) !== -1;
        });
    }

    function renderConversationList() {
        var conversations = filteredConversations();
        elements.list.textContent = '';
        if (conversations.length === 0) {
            elements.list.appendChild(create(
                'div',
                'message-list-empty',
                state.conversations.length === 0
                    ? 'No hay alumnos activos en esta aula.'
                    : 'No hay coincidencias con esa búsqueda.'
            ));
            return;
        }

        conversations.forEach(function (conversation) {
            var button = create('button', 'message-conversation');
            button.type = 'button';
            button.dataset.studentId = String(conversation.student_id);
            if (Number(conversation.student_id) === state.currentStudent) {
                button.classList.add('is-active');
            }
            if (Number(conversation.unread_count) > 0) {
                button.classList.add('has-unread');
            }

            button.appendChild(create('span', 'message-avatar', initials(conversation.student_name)));
            var copy = create('span', 'message-conversation__copy');
            copy.appendChild(create('span', 'message-conversation__title', conversation.student_name));
            copy.appendChild(create('span', 'message-conversation__family', conversation.family_name));
            var preview = create('span', 'message-conversation__preview');
            if (conversation.has_messages && conversation.last_author) {
                preview.appendChild(create('strong', '', conversation.last_author + ': '));
            }
            preview.appendChild(document.createTextNode(conversation.last_preview));
            copy.appendChild(preview);
            button.appendChild(copy);

            var meta = create('span', 'message-conversation__meta');
            meta.appendChild(create('span', 'message-conversation__time', shortTime(conversation.last_at)));
            if (Number(conversation.unread_count) > 0) {
                meta.appendChild(create('span', 'message-conversation__unread', String(conversation.unread_count)));
            } else {
                meta.appendChild(create('span', 'message-conversation__classroom', conversation.classroom_name));
            }
            button.appendChild(meta);
            button.addEventListener('click', function () {
                openConversation(conversation, false);
            });
            elements.list.appendChild(button);
        });
    }

    function renderState(payload) {
        state.conversations = Array.isArray(payload.conversations) ? payload.conversations : [];
        elements.unread.textContent = String(Number(payload.total_unread || 0));
        elements.summary.textContent = state.conversations.length + (state.conversations.length === 1 ? ' familia' : ' familias');
        renderConversationList();

        if (state.initialStudent > 0) {
            var initial = state.conversations.find(function (conversation) {
                return Number(conversation.student_id) === state.initialStudent;
            });
            state.initialStudent = 0;
            if (initial) {
                openConversation(initial, true);
            }
        } else if (state.currentStudent > 0) {
            state.currentConversation = state.conversations.find(function (conversation) {
                return Number(conversation.student_id) === state.currentStudent;
            }) || state.currentConversation;
        }
    }

    function loadConversations(showLoading) {
        var classroomId = Number(elements.classroom.value || 0);
        if (showLoading) {
            elements.refresh.classList.add('is-loading');
            elements.refresh.disabled = true;
        }
        return request(root.dataset.stateEndpoint + '&id_aula=' + encodeURIComponent(classroomId))
            .then(renderState)
            .catch(function (error) {
                if (state.conversations.length === 0) {
                    elements.list.textContent = '';
                    elements.list.appendChild(create('div', 'message-list-error', error.message));
                }
            })
            .finally(function () {
                elements.refresh.classList.remove('is-loading');
                elements.refresh.disabled = false;
            });
    }

    function updateUrl(conversation) {
        if (!window.history || !window.history.replaceState) {
            return;
        }
        var url = new URL(window.location.href);
        url.searchParams.set('id_aula', String(conversation.classroom_id));
        url.searchParams.set('id_alumno', String(conversation.student_id));
        window.history.replaceState({}, '', url.pathname + url.search);
    }

    function setChatHeader(student) {
        elements.student.textContent = student.student_name;
        elements.family.textContent = student.family_name;
        elements.classroomName.textContent = 'Aula ' + student.classroom_name;
        elements.avatar.textContent = initials(student.student_name);
        elements.composerStudent.value = String(student.student_id);
        var canSend = Number(student.parent_count) > 0;
        elements.text.disabled = !canSend;
        elements.send.disabled = !canSend;
        elements.text.placeholder = canSend
            ? 'Escribe un mensaje…'
            : 'Este alumno no tiene un familiar vinculado';
        elements.state.textContent = canSend
            ? (Number(student.parent_count) === 1 ? '1 familiar vinculado' : student.parent_count + ' familiares vinculados')
            : 'Sin familiar vinculado';
    }

    function appendMedicine(bubble, event) {
        if (!event.is_medicine) {
            return;
        }
        var medicine = create('span', 'message-bubble__medicine');
        medicine.appendChild(create('strong', '', 'Indicaciones de medicación'));
        if (event.medicine) {
            medicine.appendChild(create('span', '', event.medicine + (event.dose ? ' · ' + event.dose : '')));
        }
        var schedule = [];
        if (event.medicine_start) {
            schedule.push('Desde ' + event.medicine_start);
        }
        if (event.medicine_end) {
            schedule.push('hasta ' + event.medicine_end);
        }
        if (event.medicine_time) {
            schedule.push('a las ' + event.medicine_time.slice(0, 5));
        }
        if (schedule.length) {
            medicine.appendChild(create('span', '', schedule.join(' ')));
        }
        bubble.appendChild(medicine);
    }

    function renderChat(chat, preserveScroll) {
        var events = Array.isArray(chat.events) ? chat.events : [];
        var signature = events.map(function (event) { return event.id + '@' + event.at; }).join('|');
        if (preserveScroll && signature === state.chatSignature) {
            return;
        }
        var wasNearBottom = elements.history.scrollHeight - elements.history.scrollTop - elements.history.clientHeight < 100;
        state.chatSignature = signature;
        state.currentThread = Number(chat.latest_thread_id || 0);
        elements.composerThread.value = String(state.currentThread);
        elements.history.textContent = '';

        if (events.length === 0) {
            var empty = create('div', 'message-history-empty');
            empty.appendChild(create('strong', '', 'Aún no hay mensajes'));
            empty.appendChild(create('span', '', 'Escribe el primero para iniciar la conversación con la familia.'));
            elements.history.appendChild(empty);
        } else {
            var previousDay = '';
            events.forEach(function (event) {
                var currentDay = String(event.at || '').slice(0, 10);
                if (currentDay !== previousDay) {
                    var day = create('div', 'message-day');
                    day.appendChild(create('span', '', dayLabel(event.at)));
                    elements.history.appendChild(day);
                    previousDay = currentDay;
                }
                if (event.is_initial && event.subject && event.subject !== 'Conversación con la familia') {
                    var topic = create('div', 'message-topic');
                    topic.appendChild(create('span', '', event.subject));
                    elements.history.appendChild(topic);
                }
                var row = create('div', 'message-bubble-row message-bubble-row--' + event.side);
                var bubble = create('div', 'message-bubble');
                bubble.appendChild(create('span', 'message-bubble__author', event.author));
                bubble.appendChild(create('p', 'message-bubble__text', event.text || 'Mensaje sin texto'));
                appendMedicine(bubble, event);
                bubble.appendChild(create('time', 'message-bubble__time', exactTime(event.at)));
                row.appendChild(bubble);
                elements.history.appendChild(row);
            });
        }

        if (!preserveScroll || wasNearBottom) {
            window.requestAnimationFrame(function () {
                elements.history.scrollTop = elements.history.scrollHeight;
            });
        }
    }

    function markRead(studentId) {
        var body = new URLSearchParams();
        body.set('_csrf', String(config.csrf || ''));
        body.set('id_alumno', String(studentId));
        return request(root.dataset.readEndpoint, { method: 'POST', body: body }).then(function () {
            var conversation = state.conversations.find(function (item) {
                return Number(item.student_id) === Number(studentId);
            });
            if (conversation && Number(conversation.unread_count) > 0) {
                var currentTotal = Number(elements.unread.textContent || 0);
                elements.unread.textContent = String(Math.max(0, currentTotal - Number(conversation.unread_count)));
                conversation.unread_count = 0;
                renderConversationList();
            }
            document.dispatchEvent(new CustomEvent('lapices:messages-changed'));
        }).catch(function (error) {
            elements.composerStatus.textContent = error.message;
            return null;
        });
    }

    function loadChat(preserveScroll) {
        if (!state.currentStudent) {
            return Promise.resolve();
        }
        var token = ++state.loadingChatToken;
        return request(root.dataset.chatEndpoint + '&id_alumno=' + encodeURIComponent(state.currentStudent))
            .then(function (payload) {
                if (token !== state.loadingChatToken || Number(payload.chat.student.student_id) !== state.currentStudent) {
                    return;
                }
                setChatHeader(payload.chat.student);
                renderChat(payload.chat, Boolean(preserveScroll));
                return markRead(state.currentStudent);
            })
            .catch(function (error) {
                if (!preserveScroll) {
                    elements.history.textContent = '';
                    elements.history.appendChild(create('div', 'message-list-error', error.message));
                }
            });
    }

    function openConversation(conversation, fromInitialUrl) {
        state.currentStudent = Number(conversation.student_id);
        state.currentConversation = conversation;
        state.chatSignature = '';
        elements.empty.hidden = true;
        elements.active.hidden = false;
        elements.layout.classList.add('is-chat-open');
        setChatHeader(conversation);
        elements.history.textContent = '';
        elements.history.appendChild(create('div', 'message-list-loading'));
        renderConversationList();
        if (!fromInitialUrl || Number(config.classroom_id || 0) !== Number(conversation.classroom_id)) {
            updateUrl(conversation);
        }
        loadChat(false);
    }

    function closeMobileChat() {
        elements.layout.classList.remove('is-chat-open');
    }

    function autoResizeComposer() {
        elements.text.style.height = 'auto';
        elements.text.style.height = Math.min(elements.text.scrollHeight, 145) + 'px';
    }

    function sendMessage(event) {
        event.preventDefault();
        var text = elements.text.value.trim();
        if (!state.currentStudent || !text) {
            return;
        }
        var body = new URLSearchParams();
        body.set('_csrf', String(config.csrf || ''));
        body.set('id_alumno', String(state.currentStudent));
        body.set('id_hilo', String(state.currentThread || 0));
        body.set('texto', text);
        body.set('asunto', 'Conversación con la familia');
        elements.send.disabled = true;
        elements.text.disabled = true;
        elements.composerStatus.textContent = 'Enviando…';
        request(root.dataset.sendEndpoint, { method: 'POST', body: body })
            .then(function () {
                elements.text.value = '';
                autoResizeComposer();
                elements.composerStatus.textContent = '';
                return Promise.all([loadChat(false), loadConversations(false)]);
            })
            .catch(function (error) {
                elements.composerStatus.textContent = error.message;
            })
            .finally(function () {
                var canSend = !state.currentConversation || Number(state.currentConversation.parent_count) > 0;
                elements.send.disabled = !canSend;
                elements.text.disabled = !canSend;
                if (canSend) {
                    elements.text.focus();
                }
            });
    }

    elements.classroom.value = String(Number(config.classroom_id || 0));
    elements.classroom.addEventListener('change', function () {
        state.currentStudent = 0;
        state.currentConversation = null;
        state.initialStudent = 0;
        state.chatSignature = '';
        elements.layout.classList.remove('is-chat-open');
        elements.active.hidden = true;
        elements.empty.hidden = false;
        loadConversations(true);
    });
    elements.search.addEventListener('input', renderConversationList);
    elements.refresh.addEventListener('click', function () {
        Promise.all([loadConversations(true), loadChat(true)]);
    });
    elements.back.addEventListener('click', closeMobileChat);
    elements.form.addEventListener('submit', sendMessage);
    elements.text.addEventListener('input', autoResizeComposer);
    elements.text.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            if (elements.text.value.trim()) {
                elements.form.requestSubmit();
            }
        }
    });

    loadConversations(true);
    window.setInterval(function () {
        if (document.visibilityState === 'visible') {
            loadConversations(false);
            loadChat(true);
        }
    }, 30000);
})();
