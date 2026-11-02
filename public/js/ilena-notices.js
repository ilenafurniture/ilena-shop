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
        const toast = document.createElement('section');
        toast.className = `ilena-toast ilena-toast--${tone}`;
        toast.dataset.tone = tone;
        toast.setAttribute('role', tone === 'error' || tone === 'warning' ? 'alert' : 'status');
        toast.setAttribute('aria-live', tone === 'error' || tone === 'warning' ? 'assertive' : 'polite');
        toast.setAttribute('aria-atomic', 'true');

        const mark = document.createElement('span');
        mark.className = 'ilena-toast__mark';
        mark.setAttribute('aria-hidden', 'true');
        mark.textContent = tone === 'success' ? '✓' : tone === 'error' ? '!' : tone === 'warning' ? '!' : 'i';
        const copy = document.createElement('p');
        copy.className = 'ilena-toast__copy';
        copy.textContent = String(message ?? '');
        const close = document.createElement('button');
        close.className = 'ilena-toast__close';
        close.type = 'button';
        close.setAttribute('aria-label', locale() === 'en' ? 'Dismiss notification' : 'Tutup pemberitahuan');
        close.innerHTML = '&times;';
        toast.append(mark, copy, close);
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
        };
        const resume = () => {
            if (settled || timer !== null) return;
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
        document.querySelectorAll('#main-content .pemberitahuan, #admin-main .pemberitahuan').forEach((notice) => {
            if (notice.closest('[role="dialog"], .modal, #edit-sandi')) return;
            if (notice.dataset.toastPromoted === 'true') return;
            notice.dataset.toastPromoted = 'true';
            const tone = notice.classList.contains('hijau') ? 'success' : (notice.getAttribute('role') === 'alert' ? 'warning' : 'info');
            const duration = notice.getAttribute('role') === 'alert' ? 8000 : 6500;
            const message = (notice.innerText || notice.textContent || '').trim();
            if (message) void show(message, { tone, duration });
            notice.remove();
        });
    };

    window.IlenaNotice = Object.freeze({ show, promotePageMessages });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', promotePageMessages, { once: true });
    else promotePageMessages();
})();
