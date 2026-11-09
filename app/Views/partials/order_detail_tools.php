<?php
$isEnglish = service('request')->getLocale() === 'en';
$orderId = (string) ($pemesananSelected['id_midtrans'] ?? $id_order ?? '');
?>
<section class="ilena-order-detail-tools" aria-label="<?= $isEnglish ? 'Order documents and support' : 'Dokumen dan bantuan pesanan'; ?>">
    <div class="ilena-order-documents">
        <a href="<?= esc(ilena_store_path('/invoice/' . rawurlencode($orderId)), 'attr'); ?>">
            <?= $isEnglish ? 'View invoice' : 'Lihat invoice'; ?>
        </a>
        <a href="<?= esc(ilena_store_path('/invoice/' . rawurlencode($orderId) . '/pdf'), 'attr'); ?>">
            <?= $isEnglish ? 'Download PDF' : 'Unduh PDF'; ?>
        </a>
    </div>

    <details class="ilena-order-service-request">
        <summary><?= $isEnglish ? 'Need help with this order?' : 'Perlu bantuan untuk pesanan ini?'; ?></summary>
        <p><?= $isEnglish ? 'Send a cancellation, return, or refund request. The team will review it; submitting this form does not automatically cancel the order or issue a refund.' : 'Kirim permintaan pembatalan, retur, atau refund. Tim akan meninjaunya; formulir ini tidak otomatis membatalkan pesanan atau menerbitkan refund.'; ?></p>
        <form method="post" action="<?= esc(ilena_store_path('/commerce/order-case/cancellation'), 'attr'); ?>" data-ilena-order-case data-case-base="<?= esc(ilena_store_path('/commerce/order-case'), 'attr'); ?>">
            <?= csrf_field(); ?>
            <input type="hidden" name="order_id" value="<?= esc($orderId, 'attr'); ?>">
            <label>
                <?= $isEnglish ? 'Request type' : 'Jenis permintaan'; ?>
                <select data-case-type>
                    <option value="cancellation"><?= $isEnglish ? 'Cancellation request' : 'Permintaan pembatalan'; ?></option>
                    <option value="return"><?= $isEnglish ? 'Return' : 'Retur'; ?></option>
                    <option value="refund">Refund</option>
                </select>
            </label>
            <label>
                <?= $isEnglish ? 'Reason' : 'Alasan'; ?>
                <input name="reason" maxlength="80" minlength="3" required>
            </label>
            <label class="ilena-order-service-request__message">
                <?= $isEnglish ? 'Additional details (optional)' : 'Keterangan tambahan (opsional)'; ?>
                <textarea name="message" maxlength="2000" rows="3"></textarea>
            </label>
            <button type="submit"><?= $isEnglish ? 'Submit request' : 'Kirim permintaan'; ?></button>
        </form>
    </details>
</section>

<style>
.ilena-order-detail-tools{display:grid;gap:16px;margin:20px 0;padding-top:16px;border-top:1px solid #e8e2dc;color:#352f2a}
.ilena-order-documents{display:flex;flex-wrap:wrap;gap:10px}
.ilena-order-documents a{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 15px;border:1px solid #a62c2b;border-radius:6px;color:#a62c2b;font-weight:600;text-decoration:none}
.ilena-order-documents a:first-child{background:#a62c2b;color:#fff}
.ilena-order-documents a:hover{filter:brightness(.94)}
.ilena-order-service-request{border-top:1px solid #eee8e1;padding-top:14px}
.ilena-order-service-request summary{width:fit-content;cursor:pointer;color:#842524;font-weight:600}
.ilena-order-service-request p{max-width:720px;margin:10px 0;color:#655e57;font-size:.9rem;line-height:1.55}
.ilena-order-service-request form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;max-width:720px}
.ilena-order-service-request label{display:grid;gap:5px;font-size:.85rem}
.ilena-order-service-request__message{grid-column:1/-1}
.ilena-order-service-request input,.ilena-order-service-request select,.ilena-order-service-request textarea{width:100%;min-height:42px;border:1px solid #d9d2c9;border-radius:6px;padding:9px 10px;font:inherit}
.ilena-order-service-request button{justify-self:start;min-height:42px;border:0;border-radius:6px;padding:0 16px;background:#a62c2b;color:#fff;font:inherit;font-weight:600;cursor:pointer}
@media(max-width:600px){.ilena-order-service-request form{grid-template-columns:1fr}.ilena-order-service-request__message{grid-column:auto}.ilena-order-documents a{flex:1}}
</style>
<script>
document.querySelectorAll('[data-ilena-order-case]').forEach((form) => {
    const type = form.querySelector('[data-case-type]');
    const base = form.dataset.caseBase;
    if (!type || !base) return;
    type.addEventListener('change', () => { form.action = `${base}/${encodeURIComponent(type.value)}`; });
});
</script>
