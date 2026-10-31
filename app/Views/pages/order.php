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

/* Table pesanan */
.container-table {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
}

.header-table,
.isi-table {
    display: grid;
    grid-template-columns: 1.6fr 1.2fr 1.2fr 1fr 1fr;
    gap: .5rem;
    padding: .9rem 1rem;
}

.header-table {
    background: linear-gradient(180deg, #fafafa, #f3f4f6);
    font-weight: 700;
    color: #374151;
    text-transform: uppercase;
    font-size: .78rem;
    letter-spacing: .06em;
    border-bottom: 1px solid var(--line);
}

.isi-table {
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s ease;
}

.isi-table:last-child {
    border-bottom: 0;
}

.isi-table:hover {
    background: #f9fafb;
}

/* Offcanvas (detail pesanan) */
.container-offcanvas {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: min(720px, 92vw);
    background: #fff;
    transform: translateX(100%);
    transition: transform .25s ease-out;
    border-left: 1px solid var(--line);
    padding: 16px;
    overflow-y: auto;
    box-shadow: -16px 0 40px rgba(0, 0, 0, .08);
}

.container-offcanvas.show {
    transform: translateX(0);
}

.container-offcanvas h3 {
    font-weight: 800;
    letter-spacing: -.02em;
}

.container-offcanvas .garis {
    display: block;
    height: 1px;
    background: var(--line);
}

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

/* Responsif */
@media (max-width: 768px) {

    .header-table,
    .isi-table {
        grid-template-columns: 1.6fr 1.2fr .9fr .9fr 1fr;
        font-size: .95rem;
    }
}

/* Tombol kecil offcanvas */
.btn-close-ghost {
    border: 1px solid var(--line);
    background: #fff;
    border-radius: 10px;
    padding: .35rem .6rem;
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
            <?= $msgSandi; ?>
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
                <input type="password" name="sandi" class="form-control" autocomplete="new-password" minlength="12" maxlength="72" required>
            </div>
            <div class="mb-3">
                <label class="mb-1"><?= lang('Store.confirmPassword'); ?></label>
                <input type="password" class="form-control" oninput="konfirmasiSandi(event)" required
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

<div class="container konten baris-ke-kolom ilena-order-page">
    <!-- Kolom kanan (ringkasan akun) ditaruh duluan di DOM kecil? tetap: tidak ubah sistem -->
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4 account-card">
            <h2><?= lang('Store.accountTitle'); ?></h2>

            <div class="account-row mt-2">
                <p>Email</p>
                <p class="fw-bold"><?= $email; ?></p>
            </div>

            <div class="account-row">
                <p><?= lang('Store.password'); ?></p>
                <a class="btn-teks-aja" onclick="openGantiSandi()"><?= lang('Store.changePassword'); ?></a>
            </div>

            <span class="garis my-2"></span>

            <a href="<?= esc(ilena_store_path('/account'), 'attr'); ?>" class="mb-2 btn-default-abu w-100 text-center"><?= lang('Store.profileNav'); ?></a>
            <a href="<?= esc(ilena_store_path('/order'), 'attr'); ?>" class="mb-2 btn-default-abu w-100 text-center"><?= lang('Store.ordersNav'); ?></a>
            <form action="<?= esc(ilena_store_path('/logout'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?><button type="submit" class="btn-default-merah w-100 text-center"><?= lang('Store.logout'); ?></button>
            </form>
        </div>
    </div>

    <!-- Kolom kiri (daftar pesanan) -->
    <div style="flex:1;">
        <header class="ilena-order-heading">
            <div>
                <p class="ilena-order-heading__eyebrow"><?= service('request')->getLocale() === 'en' ? 'ACCOUNT' : 'AKUN'; ?> <span>/</span> <?= lang('Store.transactionHistory'); ?></p>
                <h1><?= lang('Store.myOrders'); ?></h1>
            </div>
            <span class="ilena-order-heading__count"><?= count($pesanan); ?> <?= lang('Store.orderCount'); ?></span>
        </header>
        <?php if ($notice = session()->getFlashdata('commerce_notice')): ?><div class="alert alert-info" role="status"><?= esc($notice); ?></div><?php endif; ?>

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

        <!-- Desktop -->
        <div style="overflow-x: auto;" class="show-block-ke-hide">
            <div class="container-table">
                <div class="header-table">
                    <div><?= lang('Store.orderId'); ?></div>
                    <div><?= lang('Store.date'); ?></div>
                    <div><?= lang('Store.recipient'); ?></div>
                    <div><?= lang('Store.price'); ?></div>
                    <div><?= lang('Store.status'); ?></div>
                </div>

                <?php foreach ($pesanan as $ind_p => $p) { ?>
                <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)" title="<?= esc(lang('Store.viewOrderDetails'), 'attr'); ?>">
                    <div class="text-truncate"><?= $p['id_midtrans']; ?></div>
                    <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = service('request')->getLocale() === 'en' ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                        ?>
                    <div><?= $tgl; ?></div>
                    <div class="text-truncate"><?= $p['nama']; ?></div>
                    <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                    <div>
                        <?php
                          $s = strtolower($p['status']);
                          $cls = 'badge-status ';
                          if(in_array($s, ['proses','capture','settlement'])) $cls.='proses';
                          elseif($s==='menunggu pembayaran' || $s==='pending') $cls.='menunggu';
                          elseif($s==='kadaluarsa' || $s==='expire') $cls.='kadaluarsa';
                          elseif($s==='ditolak' || $s==='deny' || $s==='gagal' || $s==='failure') $cls.='gagal';
                          elseif($s==='dibatalkan' || $s==='cancel') $cls.='dibatalkan';
                          elseif(strpos($s,'refund')!==false) $cls.='refund';
                          else $cls.='kadaluarsa';
                        ?>
                        <?php $statusLabel = in_array($s, ['proses','capture','settlement'], true) ? lang('Store.statusProcessing') : (in_array($s, ['menunggu pembayaran','pending'], true) ? lang('Store.statusPending') : (in_array($s, ['kadaluarsa','expire'], true) ? lang('Store.statusExpired') : (in_array($s, ['ditolak','deny','gagal','failure'], true) ? lang('Store.statusRejected') : (in_array($s, ['dibatalkan','cancel'], true) ? lang('Store.statusCancelled') : $p['status'])))); ?>
                        <span class="<?= $cls; ?>"><?= esc($statusLabel); ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- Mobile -->
        <div style="overflow-x: auto;" class="hide-ke-show-block">
            <div class="container-table" style="width: 660px;">
                <div class="header-table">
                    <div><?= lang('Store.orderId'); ?></div>
                    <div><?= lang('Store.date'); ?></div>
                    <div><?= lang('Store.recipient'); ?></div>
                    <div><?= lang('Store.price'); ?></div>
                    <div><?= lang('Store.status'); ?></div>
                </div>

                <?php foreach ($pesanan as $ind_p => $p) { ?>
                <div class="isi-table" onclick="openDetail(<?= $ind_p; ?>)">
                    <div class="text-truncate"><?= $p['id_midtrans']; ?></div>
                    <?php
                        $transactionTime = strtotime($p['data_mid']['transaction_time']);
                        $bulan = service('request')->getLocale() === 'en' ? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                        $tgl = date("d", $transactionTime) . " " . $bulan[(int)date("m", $transactionTime) - 1] . " " . date("Y H:i:s", $transactionTime);
                        ?>
                    <div><?= $tgl; ?></div>
                    <div class="text-truncate"><?= $p['nama']; ?></div>
                    <div>Rp <?= number_format((int)$p['data_mid']['gross_amount'], 0, ',', '.'); ?></div>
                    <div>
                        <?php
                          $s = strtolower($p['status']);
                          $cls = 'badge-status ';
                          if(in_array($s, ['proses','capture','settlement'])) $cls.='proses';
                          elseif($s==='menunggu pembayaran' || $s==='pending') $cls.='menunggu';
                          elseif($s==='kadaluarsa' || $s==='expire') $cls.='kadaluarsa';
                          elseif($s==='ditolak' || $s==='deny' || $s==='gagal' || $s==='failure') $cls.='gagal';
                          elseif($s==='dibatalkan' || $s==='cancel') $cls.='dibatalkan';
                          elseif(strpos($s,'refund')!==false) $cls.='refund';
                          else $cls.='kadaluarsa';
                        ?>
                        <?php $statusLabel = in_array($s, ['proses','capture','settlement'], true) ? lang('Store.statusProcessing') : (in_array($s, ['menunggu pembayaran','pending'], true) ? lang('Store.statusPending') : (in_array($s, ['kadaluarsa','expire'], true) ? lang('Store.statusExpired') : (in_array($s, ['ditolak','deny','gagal','failure'], true) ? lang('Store.statusRejected') : (in_array($s, ['dibatalkan','cancel'], true) ? lang('Store.statusCancelled') : $p['status'])))); ?>
                        <span class="<?= $cls; ?>"><?= esc($statusLabel); ?></span>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Offcanvas detail -->
<div class="container-offcanvas" style="z-index: 100;">
    <div class="d-flex justify-content-end">
        <button class="btn btn-light btn-close-ghost" onclick="closeDetail()" title="<?= esc(lang('Store.close'), 'attr'); ?>">
            <i class="material-icons">close</i>
        </button>
    </div>

    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="m-0 w-50" style="letter-spacing: -1px;"><?= lang('Store.orderId'); ?></p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="id">#ILENA00001</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;"><?= lang('Store.recipientName'); ?></p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="nama">—</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;"><?= lang('Store.recipientPhone'); ?></p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="nohp">—</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;"><?= lang('Store.recipientAddress'); ?></p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="alamat">—</p>
    </div>
    <div class="mt-2 d-flex justify-content-between pb-1">
        <p class="w-50 m-0" style="letter-spacing: -1px;"><?= lang('Store.orderEmail'); ?></p>
        <p class="w-50 fw-bold m-0 text-end" style="letter-spacing: -1px;" id="email">—</p>
    </div>

    <h3 class="my-3"><?= lang('Store.products'); ?></h3>
    <div id="item">
        <div class="d-flex mb-1 border-bottom pb-1">
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;"><?= lang('Store.name'); ?></p>
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;"><?= lang('Store.quantity'); ?></p>
            <p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;"><?= lang('Store.price'); ?></p>
        </div>
    </div>

    <span class="garis my-4"></span>

    <div class="d-flex justify-content-center gap-2">
        <a href="" class="btn-default" id="btn-invoice"><?= lang('Store.invoice'); ?></a>
        <a href="" class="btn-default" id="btn-detail"><?= lang('Store.orderDetails'); ?></a>
    </div>

    <details class="ilena-order-service-request mt-4">
        <summary><?= service('request')->getLocale() === 'en' ? 'Need help with this order?' : 'Perlu bantuan untuk pesanan ini?'; ?></summary>
        <p><?= service('request')->getLocale() === 'en' ? 'Send a cancellation, return, or refund request. The team will review it; this form does not itself cancel an order or issue a refund.' : 'Kirim permintaan pembatalan, retur, atau refund. Tim akan meninjaunya; formulir ini tidak otomatis membatalkan pesanan atau menerbitkan refund.'; ?></p>
        <form method="post" id="order-service-form" action="<?= esc(ilena_store_path('/commerce/order-case/cancellation'), 'attr'); ?>">
            <?= csrf_field(); ?><input type="hidden" name="order_id" id="service-case-order-id">
            <label><?= service('request')->getLocale() === 'en' ? 'Request type' : 'Jenis permintaan'; ?><select id="service-case-type"><option value="cancellation"><?= service('request')->getLocale() === 'en' ? 'Cancellation request' : 'Permintaan pembatalan'; ?></option><option value="return"><?= service('request')->getLocale() === 'en' ? 'Return' : 'Retur'; ?></option><option value="refund">Refund</option></select></label>
            <label><?= service('request')->getLocale() === 'en' ? 'Reason' : 'Alasan'; ?><input name="reason" maxlength="80" minlength="3" required></label>
            <label><?= service('request')->getLocale() === 'en' ? 'Additional details (optional)' : 'Keterangan tambahan (opsional)'; ?><textarea name="message" maxlength="2000" rows="3"></textarea></label>
            <button type="submit" class="btn-default"><?= service('request')->getLocale() === 'en' ? 'Submit request' : 'Kirim permintaan'; ?></button>
        </form>
    </details>

    <div class="my-3 text-center" style="color:var(--muted); font-size:.85rem;">
        <?= lang('Store.closeHint'); ?>
    </div>
</div>

<script>
/* Data asli dari controller (tetap) */
const pesanan = JSON.parse('<?= $pesananJson; ?>');

/* DOM refs */
const idElm = document.getElementById("id");
const namaElm = document.getElementById("nama");
const nohpElm = document.getElementById("nohp");
const alamatElm = document.getElementById("alamat");
const emailElm = document.getElementById("email");
const itemElm = document.getElementById("item");
const containerOffcanvasElm = document.querySelector(".container-offcanvas");
const btnInvoiceElm = document.getElementById('btn-invoice');
const btnDetailElm = document.getElementById('btn-detail');
const serviceCaseOrderElm = document.getElementById('service-case-order-id');
const serviceCaseTypeElm = document.getElementById('service-case-type');
const serviceCaseFormElm = document.getElementById('order-service-form');
const storeRoutePrefix = <?= json_encode(service('request')->getLocale() === 'en' ? '/en' : ''); ?>;

const sandiElm = document.querySelector('input[name="sandi"]');
const editSandiElm = document.getElementById('edit-sandi');

/* Helpers */
function rupiah(n) {
    try {
        return new Intl.NumberFormat('id-ID').format(Number(n || 0));
    } catch (e) {
        return n;
    }
}

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

/* Offcanvas detail pesanan */
function openDetail(indP) {
    const p = pesanan[indP];
    if (!p) return;

    idElm.textContent = p.id_midtrans || '-';
    namaElm.textContent = p.nama || '-';
    nohpElm.textContent = p.nohp || '-';
    alamatElm.textContent = p.alamat || '-';
    emailElm.textContent = p.email || '-';

    // header
    itemElm.innerHTML =
        '<div class="d-flex mb-1 border-bottom pb-1">' +
        '<p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;"><?= esc(lang('Store.name'), 'js'); ?></p>' +
        '<p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;"><?= esc(lang('Store.quantity'), 'js'); ?></p>' +
        '<p class="d-block fw-bold m-0 text-center text-secondary" style="flex: 1; letter-spacing: -1px;"><?= esc(lang('Store.price'), 'js'); ?></p>' +
        '</div>';

    (p.items || []).forEach(el => {
        const lineName = String(el.name || '').toLowerCase();
        if (!['biaya ongkir', 'biaya admin', 'voucher', 'flash sale'].includes(lineName)) {
            itemElm.innerHTML +=
                '<div class="d-flex py-1">' +
                '<p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + (el
                    .name || '-') + '</p>' +
                '<p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">' + (el
                    .quantity || 0) + '</p>' +
                '<p class="d-block fw-bold m-0 text-center" style="flex: 1; letter-spacing: -1px;">Rp ' +
                rupiah(el.price) + '</p>' +
                '</div>';
        }
    });

    btnInvoiceElm.href = storeRoutePrefix + '/invoice/' + p.id_midtrans;
    btnDetailElm.href = storeRoutePrefix + '/orderdetail/' + (String(p.status || '').toLowerCase()) + '?idorder=' + p.id_midtrans;
    serviceCaseOrderElm.value = p.id_midtrans || '';
    serviceCaseFormElm.action = storeRoutePrefix + '/commerce/order-case/' + encodeURIComponent(serviceCaseTypeElm.value);

    containerOffcanvasElm.classList.add("show");
}

serviceCaseTypeElm.addEventListener('change', () => {
    serviceCaseFormElm.action = storeRoutePrefix + '/commerce/order-case/' + encodeURIComponent(serviceCaseTypeElm.value);
});

function closeDetail() {
    containerOffcanvasElm.classList.remove("show");
}
/* Tutup offcanvas saat tekan ESC */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDetail();
});
</script>

<?= $this->endSection(); ?>
