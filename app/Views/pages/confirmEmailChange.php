<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<main class="container py-5" style="max-width:680px; min-height:50vh">
    <section class="p-4 p-md-5 bg-white rounded-4 shadow-sm" aria-labelledby="email-confirm-title">
        <p class="text-uppercase small text-secondary mb-2">ILENA ACCOUNT</p>
        <h1 id="email-confirm-title" class="h3 mb-3">
            <?= service('request')->getLocale() === 'en' ? 'Confirm your new email' : 'Konfirmasi email baru'; ?>
        </h1>
        <?php if (!empty($valid)): ?>
            <p><?= service('request')->getLocale() === 'en'
                ? 'Continue only if you requested this change. Your order and voucher history will remain linked to your account.'
                : 'Lanjutkan hanya jika Kakak meminta perubahan ini. Riwayat pesanan dan voucher tetap terhubung ke akun.'; ?></p>
            <form method="post" action="<?= esc(ilena_store_path('/account/email/confirm'), 'attr'); ?>">
                <?= csrf_field(); ?>
                <input type="hidden" name="token" value="<?= esc($token, 'attr'); ?>">
                <button type="submit" class="btn-default-merah">
                    <?= service('request')->getLocale() === 'en' ? 'Confirm email change' : 'Konfirmasi perubahan email'; ?>
                </button>
            </form>
        <?php else: ?>
            <p role="status"><?= service('request')->getLocale() === 'en'
                ? 'This confirmation link is invalid or has expired. Request a new link from your account.'
                : 'Tautan konfirmasi tidak valid atau sudah kedaluwarsa. Minta tautan baru dari akun Kakak.'; ?></p>
            <a href="<?= esc(ilena_store_path('/account'), 'attr'); ?>"><?= service('request')->getLocale() === 'en' ? 'Back to account' : 'Kembali ke akun'; ?></a>
        <?php endif; ?>
    </section>
</main>
<?= $this->endSection(); ?>
