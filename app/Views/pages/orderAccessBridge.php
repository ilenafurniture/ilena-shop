<?php
$isEnglish = ($locale ?? 'id') === 'en';
?>
<!doctype html>
<html lang="<?= esc($isEnglish ? 'en' : 'id', 'attr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title><?= $isEnglish ? 'Opening your order' : 'Membuka pesanan'; ?> | Ilena Furniture</title>
</head>
<body>
    <main>
        <p id="order-access-message" role="status" aria-live="polite">
            <?= $isEnglish ? 'Securely opening your order…' : 'Membuka pesanan dengan aman…'; ?>
        </p>
        <form id="order-access-form" method="post" action="<?= esc(site_url('/order-access/exchange'), 'attr'); ?>">
            <?= csrf_field(); ?>
            <input type="hidden" name="idorder" value="<?= esc($orderId, 'attr'); ?>">
            <input type="hidden" name="locale" value="<?= esc($locale, 'attr'); ?>">
            <input type="hidden" name="access_token" value="">
            <noscript><?= $isEnglish ? 'Enable JavaScript to securely open this order link.' : 'Aktifkan JavaScript untuk membuka tautan pesanan ini dengan aman.'; ?></noscript>
        </form>
    </main>
    <script>
    (() => {
        const form = document.getElementById('order-access-form');
        const message = document.getElementById('order-access-message');
        const params = new URLSearchParams(window.location.hash.slice(1));
        const token = params.get('access_token') || '';

        // The fragment is not sent to the server. Remove it before the POST as well.
        window.history.replaceState(null, '', window.location.pathname + window.location.search);
        if (!/^[a-f0-9]{64}$/i.test(token)) {
            message.textContent = <?= json_encode($isEnglish ? 'This order link is invalid or expired. Please open the latest link from your email.' : 'Tautan pesanan tidak valid atau sudah kedaluwarsa. Silakan buka tautan terbaru dari email.'); ?>;
            return;
        }

        form.elements.access_token.value = token;
        form.requestSubmit();
    })();
    </script>
</body>
</html>
