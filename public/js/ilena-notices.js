(() => {
    'use strict';

    const locale = () => document.documentElement.lang.toLowerCase().startsWith('en') ? 'en' : 'id';
    const ensureStack = () => {
        let stack = document.getElementById('ilena-toast-stack');
        if (!stack) {
            stack = document.createElement('div');
            stack.id = 'ilena-toast-stack';
            stack.className = 'ilena-toast-stack';
            stack.setAttribute('aria-label', locale() === 'en' ? 'Notifications' : 'Pemberitahuan');
            document.body.appendChild(stack);
        }
        return stack;
    };

    const show = (message, options = {}) => new Promise((resolve) => {
        const stack = ensureStack();
        const tone = ['success', 'warning', 'error'].includes(options.tone) ? options.tone : 'info';
        const duration = Number.isFinite(options.duration) ? Math.max(1800, options.duration) : 6500;
        const copy = {
            id: { success: 'Berhasil', warning: 'Perhatian', error: 'Ada kendala', info: 'Informasi' },
            en: { success: 'Done', warning: 'Please note', error: 'Something went wrong', info: 'Information' },
        }[locale()];
        const icons = {
            success: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12.5 4.2 4.2L19 7"/></svg>',
            warning: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5m0 3.2v.1M10.3 3.8 2.7 17a2 2 0 0 0 1.7 3h15.2a2 2 0 0 0 1.7-3l-7.6-13.2a2 2 0 0 0-3.4 0Z"/></svg>',
            error: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8v5m0 3.2v.1M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/></svg>',
            info: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 10.5v6m0-9.2v.1M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/></svg>',
        };
        const toast = document.createElement('section');
        toast.className = `ilena-toast ilena-toast--${tone}`;
        toast.style.setProperty('--toast-duration', `${duration}ms`);
        toast.dataset.tone = tone;
        toast.setAttribute('role', tone === 'error' || tone === 'warning' ? 'alert' : 'status');
        toast.setAttribute('aria-live', tone === 'error' || tone === 'warning' ? 'assertive' : 'polite');
        toast.setAttribute('aria-atomic', 'true');

        const mark = document.createElement('span');
        mark.className = 'ilena-toast__mark';
        mark.setAttribute('aria-hidden', 'true');
        mark.innerHTML = icons[tone];
        const body = document.createElement('div');
        body.className = 'ilena-toast__body';
        const heading = document.createElement('p');
        heading.className = 'ilena-toast__title';
        heading.textContent = String(options.title || copy[tone]);
        const messageCopy = document.createElement('p');
        messageCopy.className = 'ilena-toast__copy';
        messageCopy.textContent = String(message ?? '');
        body.append(heading, messageCopy);
        const close = document.createElement('button');
        close.className = 'ilena-toast__close';
        close.type = 'button';
        close.setAttribute('aria-label', locale() === 'en' ? 'Dismiss notification' : 'Tutup pemberitahuan');
        close.innerHTML = '&times;';
        const progress = document.createElement('span');
        progress.className = 'ilena-toast__progress';
        progress.setAttribute('aria-hidden', 'true');
        toast.append(mark, body, close, progress);
        stack.appendChild(toast);

        while (stack.children.length > 4) stack.firstElementChild?.remove();
        let timer = null;
        let remaining = duration;
        let startedAt = 0;
        let settled = false;
        const finish = () => {
            if (settled) return;
            settled = true;
            if (timer !== null) window.clearTimeout(timer);
            toast.classList.add('is-leaving');
            window.setTimeout(() => toast.remove(), 220);
            resolve(true);
        };
        const pause = () => {
            if (timer === null) return;
            window.clearTimeout(timer);
            timer = null;
            remaining = Math.max(0, remaining - (Date.now() - startedAt));
            toast.classList.add('is-paused');
        };
        const resume = () => {
            if (settled || timer !== null) return;
            toast.classList.remove('is-paused');
            startedAt = Date.now();
            timer = window.setTimeout(finish, remaining);
        };
        close.addEventListener('click', finish);
        toast.addEventListener('mouseenter', pause);
        toast.addEventListener('mouseleave', resume);
        toast.addEventListener('focusin', pause);
        toast.addEventListener('focusout', (event) => {
            if (!toast.contains(event.relatedTarget)) resume();
        });
        resume();
    });

    const promotePageMessages = () => {
        document.querySelectorAll('#main-content .pemberitahuan, #admin-main .pemberitahuan, #admin-main .alert-success, #admin-main .alert-danger, #admin-main .alert-warning, #admin-main .alert-info').forEach((notice) => {
            if (notice.closest('[role="dialog"], .modal, #edit-sandi')) return;
            if (notice.dataset.toastPromoted === 'true') return;
            notice.dataset.toastPromoted = 'true';
            const tone = notice.classList.contains('hijau') || notice.classList.contains('alert-success')
                ? 'success'
                : (notice.classList.contains('alert-danger') || notice.classList.contains('alert-warning')
                    ? (notice.classList.contains('alert-danger') ? 'error' : 'warning')
                    : (notice.getAttribute('role') === 'alert' ? 'warning' : 'info'));
            const requestedDuration = Number(notice.dataset.toastDuration);
            const duration = Number.isFinite(requestedDuration) && requestedDuration >= 1800
                ? requestedDuration
                : (notice.getAttribute('role') === 'alert' ? 8000 : 6500);
            const message = (notice.innerText || notice.textContent || '').trim();
            if (message) void show(message, { tone, duration });
            notice.remove();
        });
    };

    window.IlenaNotice = Object.freeze({ show, promotePageMessages });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', promotePageMessages, { once: true });
    else promotePageMessages();
})();
