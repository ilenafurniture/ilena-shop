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
            return open({ message, title: options.title, tone: options.tone || 'info' });
        },
        confirm(message, options = {}) {
            return open({ message, title: options.title || 'Konfirmasi', confirm: true, confirmText: options.confirmText, cancelText: options.cancelText, tone: options.tone || 'warning' });
        }
    };
    window.IlenaDialog = api;

    // Existing alert() callers now use the same branded, accessible dialog.
    window.alert = message => { void api.alert(message); };

    const allowedSubmit = new WeakSet();
    document.addEventListener('submit', async event => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        const submitter = event.submitter;
        const message = submitter?.dataset.ilenaConfirm || form.dataset.ilenaConfirm;
        if (!message) return;
        if (allowedSubmit.has(form)) {
            allowedSubmit.delete(form);
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        const accepted = await api.confirm(message, {
            title: submitter?.dataset.ilenaConfirmTitle || form.dataset.ilenaConfirmTitle || 'Konfirmasi tindakan',
            confirmText: submitter?.dataset.ilenaConfirmButton || form.dataset.ilenaConfirmButton || 'Ya, lanjutkan',
            tone: form.dataset.ilenaConfirmTone || 'warning'
        });
        if (!accepted) return;

        allowedSubmit.add(form);
        if (typeof form.requestSubmit === 'function') form.requestSubmit(event.submitter || undefined);
        else form.submit();
    }, true);
})();
