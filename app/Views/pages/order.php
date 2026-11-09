<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* ===== Scoped styles: aman, tidak ganggu halaman lain ===== */
:root {
    --card-r: 14px;
    --soft: #f8fafc;
    --line: #e5e7eb;
    --ink: #111827;
    --muted: #6b7280;
}

/* Overlay edit sandi */
#edit-sandi {
    backdrop-filter: blur(2px);
}

#edit-sandi .modal-card {
    width: min(520px, 92vw);
    border-radius: 16px;
    border: 1px solid var(--line);
    box-shadow: 0 18px 60px rgba(0, 0, 0, .18);
}

#edit-sandi h2 {
    font-weight: 800;
    letter-spacing: -.02em;
}

#edit-sandi .btn-teks-aja {
    text-decoration: none;
}

/* Kartu kiri (akun) */
.account-card {
    border-radius: var(--card-r);
    border: 1px solid var(--line);
    background: #fff;
    box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
}

.account-card h2 {
    font-weight: 800;
    letter-spacing: -.02em;
}

.account-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .6rem 0;
}

.account-row p {
    margin: 0;
}

/* Pesanan dibuka sebagai halaman detail, bukan drawer agar seluruh alur berada di satu tempat. */
.ilena-order-list {
    border-top: 1px solid var(--line);
}

.ilena-order-item {
    border-bottom: 1px solid var(--line);
}

.ilena-order-link {
    display: grid;
    grid-template-columns: minmax(150px, 1.15fr) minmax(150px, 1fr) minmax(120px, .9fr) minmax(130px, .8fr) auto;
    align-items: center;
    gap: 18px;
    padding: 18px 10px;
    color: inherit;
    text-decoration: none;
    transition: background-color .16s ease, padding .16s ease;
}

.ilena-order-link:hover,
.ilena-order-link:focus-visible {
    background: #faf8f5;
    color: inherit;
}

.ilena-order-link:focus-visible {
    outline: 2px solid #a62c2b;
    outline-offset: -2px;
}

.ilena-order-id { font-weight: 700; overflow-wrap: anywhere; }
.ilena-order-meta { display: grid; gap: 4px; min-width: 0; }
.ilena-order-label { color: var(--muted); font-size: .78rem; }
.ilena-order-recipient { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ilena-order-total { font-weight: 700; white-space: nowrap; }
.ilena-order-open { color: #a62c2b; font-size: .88rem; font-weight: 700; white-space: nowrap; }

/* Grid ringkas kanan */
.tigapuluh-ke-seratus .card {
    border: 1px solid var(--line);
    border-radius: var(--card-r);
    box-shadow: 0 6px 24px rgba(0, 0, 0, .06);
}

/* Badge status */
.badge-status {
    display: inline-block;
    padding: .28rem .5rem;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 700;
}

.badge-status.proses {
    background: #ecfeff;
    color: #0369a1;
}

.badge-status.menunggu {
    background: #fff7ed;
    color: #c2410c;
}

.badge-status.kadaluarsa {
    background: #f1f5f9;
    color: #475569;
}

.badge-status.ditolak,
.badge-status.gagal {
    background: #ffe4e6;
    color: #e11d48;
}

.badge-status.dibatalkan {
    background: #f3f4f6;
    color: #6b7280;
}

.badge-status.refund {
    background: #f0fdf4;
    color: #166534;
}

@media (max-width: 760px) {
    .ilena-order-link { grid-template-columns: minmax(0, 1fr) auto; gap: 8px 14px; padding: 15px 4px; }
    .ilena-order-link > .ilena-order-meta:nth-child(2), .ilena-order-link > .ilena-order-meta:nth-child(3) { grid-column: 1; }
    .ilena-order-link > .ilena-order-meta:nth-child(3) { grid-row: 3; }
    .ilena-order-link > .ilena-order-total { grid-column: 1; grid-row: 4; }
    .ilena-order-link > .ilena-order-open { grid-column: 2; grid-row: 1 / span 4; align-self: center; }
}

@media (prefers-reduced-motion: reduce) {
    .ilena-order-link { transition: none; }
}
</style>

<div id="edit-sandi"
    style="position: fixed; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.55); z-index: 100; top: 0; left: 0;"
    class="<?= $msgSandi ? 'd-flex' : 'd-none'; ?> justify-content-center align-items-center">
    <div class="p-4 bg-light modal-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2><?= lang('Store.changePassword'); ?></h2>
            <button type="button" class="btn-close-ghost" onclick="closeEditSandi()">
                <i class="material-icons">close</i>
            </button>
        </div>

        <?php if ($msgSandi) { ?>
        <div class="pemberitahuan my-2 mx-auto" style="width: fit-content;" role="alert">
            <?= esc(ilena_localize_notice((string) $msgSandi)); ?>
        </div>
        <?php } ?>

        <form action="<?= esc(ilena_store_path('/editsandi/order'), 'attr'); ?>" method="post" novalidate>
            <?= csrf_field(); ?>
            <div class="mb-2">
                <label class="mb-1"><?= lang('Store.currentPassword'); ?></label>
                <input type="password" name="sandiLama" class="form-control" autocomplete="current-password" maxlength="72" required>
            </div>
            <div class="mb-2">
                <label class="mb-1"><?= lang('Store.newPassword'); ?></label>
                <input type="password" name="sandi" class="form-control" autocomplete="new-password" minlength="8" maxlength="72" required>
                <small class="form-text"><?= esc(lang('Store.passwordPolicyError')); ?></small>
            </div>
            <div class="mb-3">
                <label class="mb-1"><?= lang('Store.confirmPassword'); ?></label>
                <input type="password" class="form-control" oninput="konfirmasiSandi(event)" minlength="8" maxlength="72" required
                    name="sandiKonfirm">
                <div class="invalid-feedback">
                    <?= lang('Store.passwordMismatch'); ?>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-2">
                <button type="submit" class="btn-default"><?= lang('Store.update'); ?></button>
                <button type="button" class="btn-teks-aja" onclick="closeEditSandi()"><?= lang('Store.cancel'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="container konten baris-ke-kolom ilena-order-page ilena-customer-account-shell">
    <?= $this->include('partials/account_navigation', ['activeAccountTab' => 'orders']); ?>

    <main class="ilena-customer-account-main">
        <header class="ilena-order-heading">
            <div>
                <p class="ilena-order-heading__eyebrow"><?= service('request')->getLocale() === 'en' ? 'ACCOUNT' : 'AKUN'; ?> <span>/</span> <?= lang('Store.transactionHistory'); ?></p>
                <h1><?= lang('Store.myOrders'); ?></h1>
            </div>
            <span class="ilena-order-heading__count"><?= count($pesanan); ?> <?= lang('Store.orderCount'); ?></span>
        </header>
        <?php if ($notice = session()->getFlashdata('commerce_notice')): ?><div class="alert alert-info" role="status"><?= esc(ilena_localize_notice((string) $notice)); ?></div><?php endif; ?>

        <?php if (!empty($customerServiceCases)): ?>
        <section class="ilena-service-case-tracker" aria-labelledby="service-case-tracker-title">
            <h2 id="service-case-tracker-title"><?= service('request')->getLocale() === 'en' ? 'Service request updates' : 'Pembaruan permintaan layanan'; ?></h2>
            <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th><?= service('request')->getLocale() === 'en' ? 'Order' : 'Pesanan'; ?></th><th><?= service('request')->getLocale() === 'en' ? 'Request' : 'Permintaan'; ?></th><th><?= service('request')->getLocale() === 'en' ? 'Status' : 'Status'; ?></th><th><?= service('request')->getLocale() === 'en' ? 'Last update' : 'Pembaruan'; ?></th></tr></thead><tbody>
            <?php foreach ($customerServiceCases as $serviceCase): ?><tr><td><?= esc($serviceCase['order_id']); ?></td><td><?= esc(ucfirst((string) $serviceCase['case_type'])); ?> · <?= esc($serviceCase['reason']); ?></td><td><?= esc(match ($serviceCase['status']) { 'received' => service('request')->getLocale() === 'en' ? 'Received' : 'Diterima', 'reviewing' => service('request')->getLocale() === 'en' ? 'Under review' : 'Sedang ditinjau', 'resolved' => service('request')->getLocale() === 'en' ? 'Handled' : 'Ditangani', 'rejected' => service('request')->getLocale() === 'en' ? 'Closed' : 'Ditutup', default => $serviceCase['status'] }); ?></td><td><?= esc($serviceCase['updated_at'] ?: $serviceCase['created_at']); ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
        </section>
        <?php endif; ?>

        <?php if (empty($pesanan)) : ?>
        <section class="ilena-order-empty" aria-live="polite">
            <p class="ilena-order-empty__eyebrow"><?= lang('Store.orderHistory'); ?></p>
            <h2><?= lang('Store.noOrders'); ?></h2>
            <p><?= lang('Store.noOrdersBody'); ?></p>
            <a href="<?= esc(ilena_store_path('/product'), 'attr'); ?>"><?= lang('Store.exploreCollection'); ?> <span aria-hidden="true">↗</span></a>
        </section>
        <?php else : ?>

        <section class="ilena-order-list" aria-label="<?= esc(lang('Store.transactionHistory'), 'attr'); ?>">
            <?php foreach ($pesanan as $p):
                $orderId = (string) ($p['id_midtrans'] ?? '');
                $rawStatus = (string) ($p['status'] ?? '');
                $statusKey = strtolower($rawStatus);
                $detailHref = ilena_store_path('/orderdetail/' . rawurlencode($statusKey) . '?idorder=' . rawurlencode($orderId));
                $transactionTime = strtotime((string) ($p['data_mid']['transaction_time'] ?? '')) ?: 0;
                $monthNames = service('request')->getLocale() === 'en' ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $orderDate = $transactionTime ? date('d', $transactionTime) . ' ' . $monthNames[(int) date('m', $transactionTime) - 1] . ' ' . date('Y, H:i', $transactionTime) : '—';
                $statusClass = 'badge-status ';
                if (in_array($statusKey, ['proses', 'capture', 'settlement'], true)) $statusClass .= 'proses';
                elseif (in_array($statusKey, ['menunggu pembayaran', 'pending'], true)) $statusClass .= 'menunggu';
                elseif (in_array($statusKey, ['kadaluarsa', 'expire'], true)) $statusClass .= 'kadaluarsa';
                elseif (in_array($statusKey, ['ditolak', 'deny', 'gagal', 'failure'], true)) $statusClass .= 'gagal';
                elseif (in_array($statusKey, ['dibatalkan', 'cancel'], true)) $statusClass .= 'dibatalkan';
                elseif (str_contains($statusKey, 'refund')) $statusClass .= 'refund';
                else $statusClass .= 'kadaluarsa';
                $statusLabel = in_array($statusKey, ['proses', 'capture', 'settlement'], true) ? lang('Store.statusProcessing') : (in_array($statusKey, ['menunggu pembayaran', 'pending'], true) ? lang('Store.statusPending') : (in_array($statusKey, ['kadaluarsa', 'expire'], true) ? lang('Store.statusExpired') : (in_array($statusKey, ['ditolak', 'deny', 'gagal', 'failure'], true) ? lang('Store.statusRejected') : (in_array($statusKey, ['dibatalkan', 'cancel'], true) ? lang('Store.statusCancelled') : $rawStatus))));
                $isEnglish = service('request')->getLocale() === 'en';
            ?>
            <article class="ilena-order-item">
                <a class="ilena-order-link" href="<?= esc($detailHref, 'attr'); ?>" aria-label="<?= esc(($isEnglish ? 'View order ' : 'Lihat pesanan ') . $orderId, 'attr'); ?>">
                    <div class="ilena-order-meta">
                        <span class="ilena-order-label"><?= lang('Store.orderId'); ?></span>
                        <span class="ilena-order-id"><?= esc($orderId); ?></span>
                        <span class="<?= esc($statusClass, 'attr'); ?>"><?= esc($statusLabel); ?></span>
                    </div>
                    <div class="ilena-order-meta">
                        <span class="ilena-order-label"><?= lang('Store.date'); ?></span>
                        <time datetime="<?= $transactionTime ? esc(date(DATE_ATOM, $transactionTime), 'attr') : ''; ?>"><?= esc($orderDate); ?></time>
                    </div>
                    <div class="ilena-order-meta">
                        <span class="ilena-order-label"><?= lang('Store.recipient'); ?></span>
                        <span class="ilena-order-recipient"><?= esc((string) ($p['nama'] ?? '—')); ?></span>
                    </div>
                    <div class="ilena-order-total">Rp <?= number_format((int) ($p['data_mid']['gross_amount'] ?? 0), 0, ',', '.'); ?></div>
                    <span class="ilena-order-open"><?= $isEnglish ? 'View details' : 'Lihat detail'; ?> <span aria-hidden="true">›</span></span>
                </a>
            </article>
            <?php endforeach; ?>
        </section>
        <?php endif; ?>
    </main>
</div>

<script>
const sandiElm = document.querySelector('input[name="sandi"]');
const editSandiElm = document.getElementById('edit-sandi');

/* Edit sandi overlay */
function openGantiSandi() {
    editSandiElm.classList.remove('d-none');
    editSandiElm.classList.add('d-flex');
}

function closeEditSandi() {
    editSandiElm.classList.add('d-none');
    editSandiElm.classList.remove('d-flex');
}

function konfirmasiSandi(e) {
    if (sandiElm.value !== e.target.value) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
}
/* Tutup overlay sandi saat klik di luar kartu */
editSandiElm.addEventListener('click', (ev) => {
    if (ev.target === editSandiElm) closeEditSandi();
});

</script>

<?= $this->endSection(); ?>
