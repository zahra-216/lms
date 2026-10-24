/* TT Metro Campus LMS - shared behaviour (sidebar, back button, flash messages, notifications). */
(function () {
    'use strict';

    // ---- Mobile sidebar ----
    var sidebar = document.getElementById('lmsSidebar');
    var overlay = document.getElementById('lmsOverlay');
    var toggle = document.getElementById('menuToggle');
    function setSidebar(open) {
        if (!sidebar) return;
        sidebar.classList.toggle('open', open);
        if (overlay) overlay.classList.toggle('open', open);
    }
    if (toggle) toggle.addEventListener('click', function () { setSidebar(!sidebar.classList.contains('open')); });
    if (overlay) overlay.addEventListener('click', function () { setSidebar(false); });

    // ---- Back button: return to the page the user came from, else go home ----
    document.querySelectorAll('[data-lms-back]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var cameFromHere = false;
            try { cameFromHere = document.referrer && new URL(document.referrer).origin === location.origin && document.referrer !== location.href; } catch (err) { /* ignore */ }
            if (cameFromHere && history.length > 1) history.back();
            else location.href = a.dataset.fallback || '/';
        });
    });

    // ---- Flash messages: success/info close themselves, errors stay until closed ----
    document.querySelectorAll('.lms-flash .alert[data-flash-type="success"], .lms-flash .alert[data-flash-type="info"]').forEach(function (el) {
        setTimeout(function () {
            if (window.bootstrap && bootstrap.Alert) bootstrap.Alert.getOrCreateInstance(el).close();
            else el.remove();
        }, 4500);
    });
    // Older pages still print their own copy of the same message; hide the duplicate.
    var shown = Array.prototype.map.call(document.querySelectorAll('[data-lms-flash]'), function (el) { return el.textContent.replace(/\s+/g, ' ').trim(); });
    document.querySelectorAll('.lms-main .alert:not([data-lms-flash])').forEach(function (el) {
        var text = el.textContent.replace(/\s+/g, ' ').trim();
        if (text && shown.some(function (s) { return s.indexOf(text) !== -1 || text.indexOf(s) !== -1; })) el.remove();
    });

    // ---- Notifications (mark one / all as read) ----
    var notif = document.querySelector('[data-notif-read-url]');
    if (notif) {
        var token = (document.querySelector('meta[name="csrf-token"]') || {}).content;
        var post = function (url) {
            return fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, credentials: 'same-origin' });
        };
        var updateCount = function () {
            var left = notif.querySelectorAll('[data-notif-item]').length;
            var badge = notif.querySelector('[data-notif-count]');
            if (badge) { if (left > 0) badge.textContent = left; else badge.remove(); }
        };
        notif.querySelectorAll('.mark-read-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                post(notif.dataset.notifReadUrl.replace('__ID__', btn.dataset.id)).then(function () {
                    var li = btn.closest('[data-notif-item]'); if (li) li.remove();
                    updateCount();
                });
            });
        });
        var all = document.getElementById('markAllReadBtn');
        if (all) all.addEventListener('click', function (e) {
            e.stopPropagation();
            post(notif.dataset.notifAllUrl).then(function () {
                notif.querySelectorAll('[data-notif-item]').forEach(function (li) { li.remove(); });
                updateCount();
                all.remove();
            });
        });
    }

    // ---- Double-submit guard: stop a form being sent twice by an impatient click ----
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || e.defaultPrevented) return;
        if (form.dataset.lmsSubmitting) { e.preventDefault(); return; }
        form.dataset.lmsSubmitting = '1';
        setTimeout(function () { delete form.dataset.lmsSubmitting; }, 4000);
    });
})();
