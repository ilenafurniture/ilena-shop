(() => {
    'use strict';

    const ensureDialog = () => {
        let dialog = document.getElementById('ilena-dialog');
        if (dialog) return dialog;

        dialog = document.createElement('dialog');
        dialog.id = 'ilena-dialog';
        dialog.className = 'ilena-dialog';
        dialog.setAttribute('aria-labelledby', 'ilena-dialog-title');
        dialog.innerHTML = `
            <form method="dialog" class="ilena-dialog__panel">
                <span class="ilena-dialog__mark" aria-hidden="true"></span>
                <h2 id="ilena-dialog-title" class="ilena-dialog__title"></h2>
                <p class="ilena-dialog__message"></p>
                <div class="ilena-dialog__actions">
                    <button class="ilena-dialog__button ilena-dialog__cancel" value="cancel" type="submit">Batal</button>
                    <button class="ilena-dialog__button ilena-dialog__confirm" value="confirm" type="submit">Mengerti</button>
                </div>
            </form>`;
        document.body.appendChild(dialog);
        return dialog;
    };

    const allowedSubmit = new WeakSet();
    const allowedDeleteClick = new WeakSet();
    const isAdminPage = document.body.classList.contains('ilena-admin-redesign');
    const destructivePath = /(?:^|\/)(?:delete[a-z0-9_-]*|hapus[a-z0-9_-]*|remove[a-z0-9_-]*|destroy[a-z0-9_-]*|purge[a-z0-9_-]*|del)(?:\/|$)/i;
    const isDestructiveUrl = value => {
        if (!value) return false;
        try {
            const url = new URL(value, window.location.href);
            return url.origin === window.location.origin
                && (destructivePath.test(url.pathname) || /^(delete|hapus)$/i.test(url.searchParams.get('action') || ''));
        } catch (_) {
            return false;
        }
    };

    const open = ({ title, message, confirm = false, confirmText, cancelText, tone = 'info' }) => new Promise(resolve => {
        const dialog = ensureDialog();
        const form = dialog.querySelector('form');
        const titleNode = dialog.querySelector('.ilena-dialog__title');
        const messageNode = dialog.querySelector('.ilena-dialog__message');
        const mark = dialog.querySelector('.ilena-dialog__mark');
        const ok = dialog.querySelector('.ilena-dialog__confirm');
        const cancel = dialog.querySelector('.ilena-dialog__cancel');

        titleNode.textContent = title || (confirm ? 'Konfirmasi' : 'Informasi');
        messageNode.textContent = String(message ?? '');
        mark.dataset.tone = tone;
        dialog.dataset.tone = tone;
        ok.textContent = confirmText || (confirm ? 'Lanjutkan' : 'Mengerti');
        cancel.textContent = cancelText || 'Batal';
        cancel.hidden = !confirm;
        ok.value = 'confirm';
        cancel.value = 'cancel';

        const finish = event => {
            if (event) event.preventDefault();
            dialog.removeEventListener('close', onClose);
            resolve(dialog.returnValue === 'confirm');
        };
        const onClose = () => finish();
        dialog.addEventListener('close', onClose, { once: true });
        form.addEventListener('submit', event => {
            const submitter = event.submitter;
            dialog.returnValue = submitter?.value || 'confirm';
        }, { once: true, capture: true });
        const onBackdrop = event => {
            if (event.target === dialog) dialog.close('cancel');
        };
        dialog.addEventListener('click', onBackdrop);
        dialog.addEventListener('close', () => dialog.removeEventListener('click', onBackdrop), { once: true });

        if (typeof dialog.showModal === 'function') dialog.showModal();
        else {
            dialog.setAttribute('open', '');
            dialog.classList.add('is-open');
        }
        ok.focus();
    });

    const api = {
        alert(message, options = {}) {
            if (window.IlenaNotice?.show) {
                return window.IlenaNotice.show(message, {
                    tone: options.tone || 'info',
                    duration: options.duration
                });
            }
            return open({ message, title: options.title, tone: options.tone || 'info' });
        },
        confirm(message, options = {}) {
            return open({ message, title: options.title || 'Konfirmasi', confirm: true, confirmText: options.confirmText, cancelText: options.cancelText, tone: options.tone || 'warning' });
        }
    };
    window.IlenaDialog = api;

    // Existing alert() callers now use the same branded, accessible dialog.
    window.alert = message => { void api.alert(message); };

    document.addEventListener('submit', async event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        // This sensitive flow already requires a custom dialog, the Dev
        // password, a typed DELETE, and an acknowledgement checkbox.
        if (form.id === 'deleteOrderForm') return;
        const submitter = event.submitter;
        if (allowedSubmit.has(form)) {
            allowedSubmit.delete(form);
            return;
        }

        const explicitMessage = submitter?.dataset.ilenaConfirm || form.dataset.ilenaConfirm || form.dataset.confirm;
        const submitterLabel = [submitter?.getAttribute('aria-label') || '', submitter?.title || '', submitter?.textContent || ''].join(' ');
        const destructive = (isAdminPage && isDestructiveUrl(submitter?.formAction || form.action))
            || form.hasAttribute('data-ilena-destructive')
            || (isAdminPage && /\b(hapus|delete|remove|destroy)\b/i.test(submitterLabel));
        if (!explicitMessage && !destructive) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        const accepted = await api.confirm(explicitMessage || form.dataset.ilenaDeleteMessage || (localeIsEnglish()
            ? 'This permanently deletes data and cannot be undone. Continue?'
            : 'Data akan dihapus permanen dan tidak dapat dikembalikan. Lanjutkan?'), {
            title: submitter?.dataset.ilenaConfirmTitle || form.dataset.ilenaConfirmTitle || (localeIsEnglish() ? 'Confirm deletion' : 'Konfirmasi penghapusan'),
            confirmText: submitter?.dataset.ilenaConfirmButton || form.dataset.ilenaConfirmButton || (destructive ? (localeIsEnglish() ? 'Yes, delete' : 'Ya, hapus') : (localeIsEnglish() ? 'Continue' : 'Ya, lanjutkan')),
            tone: form.dataset.ilenaConfirmTone || (destructive ? 'error' : 'warning')
        });
        if (!accepted) return;

        allowedSubmit.add(form);
        if (typeof form.requestSubmit === 'function') form.requestSubmit(event.submitter || undefined);
        else form.submit();
    }, true);

    document.addEventListener('click', async event => {
        if (!isAdminPage) return;
        const control = event.target.closest?.('button, a, input[type="button"], input[type="submit"], [role="button"]');
        if (!control) return;
        if (allowedDeleteClick.has(control)) {
            allowedDeleteClick.delete(control);
            return;
        }

        // Bulk order removal has its own stronger confirmation flow (password
        // and typed DELETE); do not stack a second popup over that dialog.
        if (control.matches('[data-delete-order], [data-delete-selected-orders]') || control.closest('#deleteOrderDialog')) return;
        if (control.hasAttribute('data-ilena-confirm-skip')) return;
        const form = control.form || control.closest('form');
        const formSubmitter = form && control.matches('button, input[type="submit"]');
        const formMessage = form && (control.dataset.ilenaConfirm || form.dataset.ilenaConfirm);
        const formDestructive = form && isAdminPage && isDestructiveUrl(control.formAction || form.action);
        if (form && formSubmitter && (formMessage || formDestructive)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            const accepted = await api.confirm(
                formMessage || (localeIsEnglish()
                    ? 'This permanently deletes data and cannot be undone. Continue?'
                    : 'Data akan dihapus permanen dan tidak dapat dikembalikan. Lanjutkan?'),
                {
                    title: control.dataset.ilenaConfirmTitle || form.dataset.ilenaConfirmTitle || (localeIsEnglish() ? 'Confirm deletion' : 'Konfirmasi penghapusan'),
                    confirmText: control.dataset.ilenaConfirmButton || form.dataset.ilenaConfirmButton || (formDestructive ? (localeIsEnglish() ? 'Yes, delete' : 'Ya, hapus') : (localeIsEnglish() ? 'Continue' : 'Lanjutkan')),
                    tone: control.dataset.ilenaConfirmTone || form.dataset.ilenaConfirmTone || (formDestructive ? 'error' : 'warning')
                }
            );
            if (!accepted) return;

            allowedSubmit.add(form);
            if (typeof form.requestSubmit === 'function') {
                if (control.form === form && control.type === 'submit') form.requestSubmit(control);
                else form.requestSubmit();
            } else {
                form.submit();
            }
            return;
        }

        const href = control instanceof HTMLAnchorElement ? control.href : '';
        const hasDeleteLabel = /\b(hapus|delete|remove|destroy)\b/i.test([
            control.getAttribute('aria-label') || '', control.getAttribute('title') || '',
            control.dataset.act === 'del' ? 'hapus' : '', control.textContent || ''
        ].join(' '));
        const destructiveLink = isDestructiveUrl(href);
        const draftDeleteButton = control.matches('button, input[type="button"], [role="button"]')
            && control.type !== 'submit' && hasDeleteLabel;
        if (!destructiveLink && !draftDeleteButton) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        const english = localeIsEnglish();
        const controlLabel = [control.getAttribute('aria-label') || '', control.title || '', control.textContent || ''].join(' ').trim();
        const draftItem = controlLabel.replace(/\b(hapus|delete|remove)\b\s*/i, '').replace(/[?!.:]+$/, '').trim();
        const accepted = await api.confirm(
            control.dataset.ilenaDeleteMessage || (destructiveLink
                ? (english ? 'This permanently deletes data and cannot be undone. Continue?' : 'Data akan dihapus permanen dan tidak dapat dikembalikan. Lanjutkan?')
                : (english
                    ? `Remove ${draftItem || 'this item'} from the form? Changes take effect only after you save.`
                    : `Hapus ${draftItem || 'item ini'} dari formulir? Perubahan berlaku setelah Anda menekan Simpan.`)),
            {
                title: control.dataset.ilenaConfirmTitle || (english ? 'Confirm deletion' : 'Konfirmasi penghapusan'),
                confirmText: english ? 'Yes, delete' : 'Ya, hapus',
                tone: 'error'
            }
        );
        if (!accepted) return;
        if (destructiveLink) {
            window.location.assign(href);
            return;
        }
        allowedDeleteClick.add(control);
        control.click();
    }, true);

    function localeIsEnglish() {
        return document.documentElement.lang.toLowerCase().startsWith('en');
    }
})();
