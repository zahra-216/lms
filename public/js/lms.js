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

    // ---- Keep the user's place ----
    // After saving, adding or deleting, the page the user returns to opens the way they left it: same scroll
    // position, same open panels, same chosen tab and search words. Remembered in this browser tab only.
    (function () {
        var main = document.querySelector('.lms-main');
        var store;
        try { store = window.sessionStorage; store.getItem('lms.saved'); } catch (err) { return; }
        if (!main) return;

        var PANELS = '.lms-main .collapse[id], .lms-main .level-body[id], .lms-main .faculty-body[id]';
        var PICKERS = ['.level-pick', '.month-btn', '[data-bs-toggle="tab"]', '[data-bs-toggle="pill"]'];
        var SEARCHES = ['studentSearch', 'nameSearch', 'moduleSearch', 'contentSearch', 'searchInput'];
        var FORM_PAGE = /\/(edit|create|add-content)$/;
        var all = function (sel) { return Array.prototype.slice.call(document.querySelectorAll(sel)); };
        var key = 'lms.place:' + location.pathname + location.search;
        var restored = null;

        function read() {
            try {
                var p = JSON.parse(store.getItem(key));
                return p && Date.now() - p.t < 60 * 60 * 1000 ? p : null;
            } catch (err) { return null; }
        }

        function remember() {
            var p = { t: Date.now(), y: window.scrollY, panels: {}, details: [], picks: {}, search: {}, across: [],
                from: restored ? restored.from : document.referrer };
            all(PANELS).forEach(function (el) { p.panels[el.id] = el.classList.contains('show'); });
            p.details = all('.lms-main details').map(function (d) { return d.open; });
            PICKERS.forEach(function (sel) {
                all('.lms-main ' + sel).forEach(function (b, i) { if (b.classList.contains('active')) p.picks[sel] = i; });
            });
            SEARCHES.forEach(function (id) {
                var el = document.getElementById(id);
                if (el && el.value) p.search[id] = el.value;
            });
            // wide tables can be scrolled sideways too
            p.across = all('.lms-main table').map(function (t) { return t.parentElement.scrollLeft; });
            try { store.setItem(key, JSON.stringify(p)); } catch (err) { /* storage full or blocked */ }
        }

        function setPanel(el, open) {
            if (el.classList.contains('show') === open) return;
            el.classList.toggle('show', open);
            all('[data-bs-target="#' + el.id + '"], [href="#' + el.id + '"][data-bs-toggle="collapse"]').forEach(function (t) {
                t.classList.toggle('collapsed', !open);
                t.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
            all('[onclick*="\'' + el.id + '\'"] .chevron').forEach(function (c) { c.classList.toggle('rotate', open); });
        }

        function restore(p) {
            restored = p;
            // the message floats over the page, so it is seen and does not push the content down
            var flash = document.querySelector('.lms-flash');
            if (flash) flash.classList.add('is-floating');

            all(PANELS).forEach(function (el) { if (el.id in p.panels) setPanel(el, p.panels[el.id]); });
            all('.lms-main details').forEach(function (d, i) { if (i < p.details.length) d.open = p.details[i]; });
            Object.keys(p.picks).forEach(function (sel) {
                var b = all('.lms-main ' + sel)[p.picks[sel]];
                if (b && !b.disabled && !b.classList.contains('active')) b.click();
            });

            // some pages fill themselves a moment later (a chosen month, pictures), so keep trying briefly
            var stopped = false, tries = 0;
            ['wheel', 'touchstart', 'keydown', 'mousedown'].forEach(function (type) {
                window.addEventListener(type, function () { stopped = true; }, { once: true, passive: true });
            });
            (function settle() {
                if (stopped) return;
                Object.keys(p.search).forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el && el.value !== p.search[id]) { el.value = p.search[id]; el.dispatchEvent(new Event('input', { bubbles: true })); }
                });
                all('.lms-main table').forEach(function (t, i) { if (p.across && p.across[i]) t.parentElement.scrollLeft = p.across[i]; });
                window.scrollTo({ top: p.y, behavior: 'instant' });
                if (++tries < 4 || (Math.abs(window.scrollY - p.y) > 2 && tries < 30)) setTimeout(settle, 100);
            })();
        }

        // a form that changes something is being sent: the next page should open where it was left
        var markSaved = function (form) {
            if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') return;
            if (/\/logout$/.test(form.getAttribute('action') || '')) return;
            try { store.setItem('lms.saved', String(Date.now())); } catch (err) { /* ignore */ }
        };
        document.addEventListener('submit', function (e) {
            if (!e.defaultPrevented && e.target instanceof HTMLFormElement) markSaved(e.target);
        });
        var send = HTMLFormElement.prototype.submit; // some links send their form from a script, which fires no event
        HTMLFormElement.prototype.submit = function () { markSaved(this); return send.apply(this, arguments); };
        window.addEventListener('pagehide', remember);

        var saved = Number(store.getItem('lms.saved') || 0);
        store.removeItem('lms.saved');
        var nav = (window.performance && performance.getEntriesByType('navigation')[0]) || {};
        var justSaved = saved && Date.now() - saved < 20 * 60 * 1000;
        // with mistakes to correct, stay at the top where they are listed
        var mistakes = !!document.querySelector('.lms-flash ul');
        var place = (justSaved || nav.type === 'back_forward') && !mistakes ? read() : null;
        if (!place) return;

        var start = function () { setTimeout(function () { restore(place); }, 0); };
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
        else start();

        // Back on a page reached by saving would reopen the form just saved: go to the page before it instead
        if (justSaved) {
            document.addEventListener('click', function (e) {
                var a = e.target.closest && e.target.closest('[data-lms-back]');
                if (!a) return;
                e.preventDefault();
                e.stopImmediatePropagation();
                var url = null;
                try { url = new URL(place.from); } catch (err) { /* opened directly */ }
                var usable = url && url.origin === location.origin && !FORM_PAGE.test(url.pathname)
                    && url.pathname !== location.pathname && !/\/login$/.test(url.pathname);
                try { store.setItem('lms.saved', String(Date.now())); } catch (err) { /* ignore */ }
                location.href = usable ? url.href : (a.dataset.fallback || '/');
            }, true);
        }
    })();

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

    // ---- A popup window must not move the page behind it ----
    // (hiding the scrollbar re-flows wide pages, and the browser then scrolls by itself)
    var popupY = null;
    var holdPlace = function () { if (popupY !== null) window.scrollTo({ top: popupY, behavior: 'instant' }); };
    document.addEventListener('show.bs.modal', function () { popupY = window.scrollY; setTimeout(holdPlace, 0); });
    document.addEventListener('shown.bs.modal', holdPlace);

    // ---- Confirmation window (replaces the browser's plain "this site says" popup) ----
    // Use data-confirm="Message" on a <form> (or on its submit button). Optional: data-confirm-ok="Button text",
    // data-confirm-title="Title". Messages that start with "Delete" get a red button.
    var confirmEl = null;
    var confirmCallback = null;
    function buildConfirm() {
        var wrap = document.createElement('div');
        wrap.innerHTML =
            '<div class="modal fade lms-confirm" tabindex="-1" aria-labelledby="lmsConfirmTitle" aria-hidden="true">' +
            '<div class="modal-dialog modal-dialog-centered"><div class="modal-content">' +
            '<div class="modal-body text-center p-4 pb-3">' +
            '<div class="lms-confirm-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>' +
            '<h5 class="lms-confirm-title" id="lmsConfirmTitle">Please confirm</h5>' +
            '<p class="lms-confirm-text mb-0"></p></div>' +
            '<div class="modal-footer border-0 justify-content-center gap-2 pt-0 pb-4">' +
            '<button type="button" class="btn lms-confirm-cancel" data-bs-dismiss="modal">Cancel</button>' +
            '<button type="button" class="btn lms-confirm-ok">Confirm</button></div>' +
            '</div></div></div>';
        confirmEl = wrap.firstChild;
        document.body.appendChild(confirmEl);
        confirmEl.querySelector('.lms-confirm-ok').addEventListener('click', function () {
            var run = confirmCallback;
            confirmCallback = null;
            bootstrap.Modal.getInstance(confirmEl).hide();
            if (run) run();
        });
        confirmEl.addEventListener('hidden.bs.modal', function () { confirmCallback = null; });
    }
    window.lmsConfirm = function (message, onOk, options) {
        options = options || {};
        if (!(window.bootstrap && bootstrap.Modal)) { if (window.confirm(message)) onOk(); return; }
        if (!confirmEl) buildConfirm();
        var danger = options.danger !== undefined ? options.danger : /^\s*delete/i.test(message);
        confirmEl.classList.toggle('is-danger', danger);
        confirmEl.querySelector('.lms-confirm-title').textContent = options.title || (danger ? 'Delete this item?' : 'Please confirm');
        confirmEl.querySelector('.lms-confirm-text').textContent = message;
        confirmEl.querySelector('.lms-confirm-ok').textContent = options.okText || (danger ? 'Delete' : 'Confirm');
        confirmCallback = onOk;
        bootstrap.Modal.getOrCreateInstance(confirmEl).show();
    };
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.dataset.lmsConfirmed) { delete form.dataset.lmsConfirmed; return; }
        var src = (e.submitter && e.submitter.hasAttribute('data-confirm')) ? e.submitter : (form.hasAttribute('data-confirm') ? form : null);
        if (!src) return;
        e.preventDefault();
        var submitter = e.submitter;
        window.lmsConfirm(src.getAttribute('data-confirm'), function () {
            form.dataset.lmsConfirmed = '1';
            if (form.requestSubmit) form.requestSubmit(submitter || undefined); else form.submit();
        }, { okText: src.getAttribute('data-confirm-ok') || undefined, title: src.getAttribute('data-confirm-title') || undefined });
    }, true);

    // ---- Double-submit guard: stop a form being sent twice by an impatient click ----
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || e.defaultPrevented) return;
        if (form.dataset.lmsSubmitting) { e.preventDefault(); return; }
        form.dataset.lmsSubmitting = '1';
        setTimeout(function () { delete form.dataset.lmsSubmitting; }, 4000);
    });
})();
