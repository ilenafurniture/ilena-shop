<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<?php
$orders = $pesanan ?? [];
$isDev = (string) session()->get('role') === '1';
$countByStatus = $statusCounts ?? ['Menunggu Pembayaran' => 0, 'Proses' => 0, 'Dikirim' => 0];
$pageUrl = static function (int $page) use ($orderQuery, $orderStatusFilter): string {
    $params = ['page' => $page];
    if ($orderQuery !== '') $params['q'] = $orderQuery;
    if ($orderStatusFilter !== '') $params['status'] = $orderStatusFilter;
    return '/admin/order/online?' . http_build_query($params);
};
?>
<style>
.online-orders,.order-dialog,.resi-dialog{--o-ink:#28231f;--o-muted:#756b62;--o-line:#e9e3da;--o-paper:#fffdfa;--o-bg:#f6f4f0;--o-accent:#8b5e3c;--o-red:#a65043;color:var(--o-ink)}.online-orders{max-width:1500px;margin:0 auto;padding:8px 0 36px}
.online-orders *{box-sizing:border-box}.online-orders .eyebrow{font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--o-muted)}
.online-orders h1{margin:4px 0;font-size:clamp(23px,2.5vw,30px);font-weight:650;letter-spacing:-.04em}.online-orders .intro{margin:0;color:var(--o-muted);font-size:14px}
.online-orders .page-head{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:25px}.online-orders .head-actions{display:flex;gap:8px;flex-wrap:wrap}
.online-orders .action{display:inline-flex;min-height:42px;align-items:center;justify-content:center;gap:8px;padding:0 15px;border:1px solid var(--o-line);border-radius:8px;background:transparent;color:var(--o-ink);font:600 13px/1.2 "Plus Jakarta Sans",sans-serif;text-decoration:none;cursor:pointer}
.online-orders .action:hover{background:#f1ede6;border-color:#d4c9bb;color:var(--o-ink)}.online-orders .action-primary{background:var(--o-accent);border-color:var(--o-accent);color:#fff}.online-orders .action-primary:hover{background:#70482f;color:#fff}
.online-orders .metrics{display:flex;gap:0;border-block:1px solid var(--o-line);margin-bottom:20px}.online-orders .metric{min-width:150px;padding:13px 22px 13px 0;margin-right:22px;border-right:1px solid var(--o-line)}.online-orders .metric:last-child{border:0}.online-orders .metric strong{display:block;font-size:21px;font-variant-numeric:tabular-nums;font-weight:650;line-height:1.2}.online-orders .metric span{font-size:12px;color:var(--o-muted)}
.online-orders .order-controls{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px}.online-orders .filters{display:flex;gap:8px;flex:1;justify-content:flex-end}
.online-orders .control{min-height:42px;border:1px solid #ded7ce;border-radius:8px;background:#fff;color:var(--o-ink);padding:0 12px;font:400 14px/1.3 "Plus Jakarta Sans",sans-serif}.online-orders .search-control{width:min(340px,100%);padding-left:38px}.online-orders .search-box{position:relative;flex:1;max-width:370px}.online-orders .search-box .material-icons{position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:19px;color:#8e857b;pointer-events:none}
.online-orders .results-label{font-size:12px;color:var(--o-muted);white-space:nowrap}.online-orders .orders-list{border-top:1px solid var(--o-line)}
.online-orders .order-row{display:grid;grid-template-columns:minmax(190px,1.2fr) minmax(160px,1fr) minmax(140px,.8fr) minmax(135px,.75fr) minmax(190px,auto);align-items:center;gap:18px;padding:17px 8px;border-bottom:1px solid var(--o-line);background:transparent;transition:background .15s ease}
.online-orders .order-row:hover{background:rgba(255,253,250,.75)}.online-orders .order-id{font:650 13px ui-monospace,SFMono-Regular,Consolas,monospace;letter-spacing:.015em;color:#6b503a;overflow-wrap:anywhere}.online-orders .order-date{font-size:12px;color:var(--o-muted);margin-top:5px}
.online-orders .customer-name{font-size:14px;font-weight:650}.online-orders .customer-email{font-size:12px;color:var(--o-muted);margin-top:4px;overflow-wrap:anywhere}.online-orders .amount{font-size:14px;font-weight:650;font-variant-numeric:tabular-nums;white-space:nowrap}.online-orders .status{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:650;white-space:nowrap}.online-orders .status::before{content:"";width:7px;height:7px;border-radius:50%;background:#8e857b}.online-orders .status[data-status="menunggu pembayaran"]::before{background:#bd8d48}.online-orders .status[data-status="proses"]::before{background:#8b684c}.online-orders .status[data-status="dikirim"]::before{background:#6d8b9b}.online-orders .status[data-status="selesai"]::before{background:#6d8c65}.online-orders .status[data-status="dibatalkan"]::before,.online-orders .status[data-status="gagal"]::before{background:#b45c50}
.online-orders .row-actions{display:flex;justify-content:flex-end;align-items:center;gap:6px;flex-wrap:wrap}.online-orders .icon-action{display:inline-flex;align-items:center;justify-content:center;min-width:40px;min-height:40px;padding:0 9px;border:1px solid transparent;border-radius:7px;background:transparent;color:#64594f;text-decoration:none;cursor:pointer}.online-orders .icon-action:hover{background:#f1ede6;color:#302a24}.online-orders .icon-action .material-icons{font-size:19px}.online-orders .icon-action-label{font-size:12px;font-weight:600}.online-orders .icon-action-danger{color:var(--o-red)}
.online-orders .empty-state{padding:52px 16px;text-align:center;color:var(--o-muted);border-bottom:1px solid var(--o-line)}.online-orders .empty-state strong{display:block;color:var(--o-ink);font-size:16px;margin-bottom:4px}
.online-orders [data-order-row][hidden]{display:none!important}
.bulk-order-tools{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 4px;margin:4px 0;border-bottom:1px solid var(--o-line);font-size:13px}.bulk-order-tools label{display:flex;align-items:center;gap:9px;cursor:pointer}.bulk-order-tools input,.delete-order-select{width:20px;height:20px;accent-color:#8b5e3c;cursor:pointer}.bulk-order-count{color:var(--o-muted)}.bulk-order-tools .action:disabled{opacity:.45;cursor:not-allowed}.delete-order-select{flex:0 0 20px}
.orders-pagination{display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:16px;color:var(--o-muted);font-size:12px}.orders-pagination>div{display:flex;gap:8px}.orders-pagination .action{min-height:38px}
.order-dialog{position:fixed;inset:0;z-index:1200;display:none;align-items:center;justify-content:center;padding:18px;background:rgba(30,26,22,.54)}.order-dialog.is-open{display:flex}.order-dialog__panel{width:min(720px,100%);max-height:min(88vh,850px);overflow:auto;background:var(--o-paper);border-radius:12px;padding:clamp(18px,3vw,30px);box-shadow:0 24px 70px rgba(0,0,0,.2)}
.order-dialog .eyebrow,.resi-dialog .eyebrow{font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--o-muted)}.order-dialog .action,.resi-dialog .action{display:inline-flex;min-height:42px;align-items:center;justify-content:center;gap:8px;padding:0 15px;border:1px solid var(--o-line);border-radius:8px;background:transparent;color:var(--o-ink);font:600 13px/1.2 "Plus Jakarta Sans",sans-serif;text-decoration:none;cursor:pointer}.order-dialog .action:hover,.resi-dialog .action:hover{background:#f1ede6}.order-dialog .action-primary,.resi-dialog .action-primary{background:var(--o-accent);border-color:var(--o-accent);color:#fff}.order-dialog .action-primary:hover,.resi-dialog .action-primary:hover{background:#70482f;color:#fff}
.order-dialog__head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;border-bottom:1px solid var(--o-line);padding-bottom:15px;margin-bottom:18px}.order-dialog__head h2{margin:3px 0;font-size:20px;letter-spacing:-.03em}.order-dialog__close{border:1px solid var(--o-line);background:transparent;border-radius:7px;width:40px;height:40px;cursor:pointer;color:var(--o-ink)}
.order-dialog__customer{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 24px;margin-bottom:22px}.order-dialog__field label{display:block;color:var(--o-muted);font-size:11px;margin-bottom:3px}.order-dialog__field div{font-size:13px;font-weight:550;overflow-wrap:anywhere}.order-dialog__items{width:100%;border-collapse:collapse}.order-dialog__items th,.order-dialog__items td{padding:10px 7px;border-bottom:1px solid var(--o-line);text-align:left;font-size:13px}.order-dialog__items th{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--o-muted)}.order-dialog__items td:last-child,.order-dialog__items th:last-child{text-align:right;white-space:nowrap}.order-dialog__footer{display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;margin-top:20px}
.resi-dialog{position:fixed;inset:0;z-index:1210;display:none;align-items:center;justify-content:center;padding:18px;background:rgba(30,26,22,.54)}.resi-dialog.is-open{display:flex}.resi-dialog__panel{width:min(460px,100%);background:var(--o-paper);border-radius:12px;padding:24px}.resi-dialog h2{font-size:19px;margin:0 0 18px}.resi-dialog label{display:block;font-size:12px;font-weight:650;margin:12px 0 5px}.resi-dialog input,.resi-dialog select{width:100%;height:44px;border:1px solid #ded7ce;border-radius:7px;padding:0 11px;background:#fff;font:inherit}.resi-dialog__actions{display:flex;justify-content:flex-end;gap:8px;margin-top:20px}
@media(max-width:1050px){.online-orders .order-row{grid-template-columns:minmax(170px,1fr) minmax(150px,1fr) minmax(135px,.8fr) minmax(130px,.75fr)}.online-orders .row-actions{grid-column:1/-1;justify-content:flex-start;padding-top:8px;border-top:1px solid #f0ebe5}}
@media(max-width:700px){.online-orders{padding-top:0}.online-orders .page-head{align-items:flex-start;flex-direction:column;margin-bottom:18px}.online-orders .head-actions{width:100%}.online-orders .head-actions .action{flex:1}.online-orders .metrics{justify-content:space-between;margin-bottom:16px}.online-orders .metric{min-width:0;flex:1;padding:11px 6px 11px 0;margin-right:8px}.online-orders .metric strong{font-size:18px}.online-orders .metric span{font-size:10px}.online-orders .order-controls{align-items:stretch;flex-direction:column}.online-orders .filters{justify-content:stretch}.online-orders .search-box{max-width:none}.online-orders .filters select{flex:1;min-width:0}.online-orders .results-label{order:2}.online-orders .order-row{grid-template-columns:minmax(0,1fr) auto;gap:9px 12px;padding:15px 2px}.online-orders .order-row__id{grid-column:1}.online-orders .order-row__status{grid-column:2;grid-row:1;justify-self:end}.online-orders .order-row__customer{grid-column:1/-1}.online-orders .order-row__amount{grid-column:1}.online-orders .order-row__actions{grid-column:2;grid-row:3;justify-content:flex-end;padding:0;border:0}.online-orders .row-actions .icon-action{min-width:40px}.order-dialog__customer{grid-template-columns:1fr;gap:11px}.order-dialog__items th,.order-dialog__items td{padding:9px 4px;font-size:12px}.order-dialog__footer .action{flex:1}}
@media(prefers-reduced-motion:reduce){.online-orders *,.order-dialog *,.resi-dialog *{scroll-behavior:auto!important;transition:none!important;animation:none!important}}
</style>

<main class="online-orders">
  <header class="page-head">
    <div><div class="eyebrow">Operasional · Toko online</div><h1>Pesanan online</h1><p class="intro">Cari pesanan, tinjau detail, dan lanjutkan pemrosesan.</p></div>
    <div class="head-actions"><a class="action" href="/admin/order/add"><span class="material-icons" aria-hidden="true">add</span>Tambah pesanan</a></div>
  </header>
  <?php if ($deleteError = session()->getFlashdata('order_delete_error')): ?><div class="alert alert-danger" role="alert"><?= esc($deleteError); ?></div><?php endif; ?>
  <?php if ($orderMessage = session()->getFlashdata('msg')): ?><div class="alert alert-success" role="status"><?= esc($orderMessage); ?></div><?php endif; ?>

  <section class="metrics" aria-label="Ringkasan status pesanan">
    <div class="metric"><strong><?= number_format((int) ($totalOrders ?? count($orders))) ?></strong><span>Total pesanan</span></div>
    <div class="metric"><strong><?= (int) $countByStatus['Menunggu Pembayaran'] ?></strong><span>Menunggu pembayaran</span></div>
    <div class="metric"><strong><?= (int) $countByStatus['Proses'] ?></strong><span>Diproses</span></div>
    <div class="metric"><strong><?= (int) $countByStatus['Dikirim'] ?></strong><span>Dikirim</span></div>
  </section>

  <section aria-label="Daftar pesanan">
    <div class="order-controls">
      <div class="results-label">Menampilkan <?= (int) ($firstOrderNumber ?? 0) ?>–<?= (int) ($lastOrderNumber ?? count($orders)) ?> dari <?= number_format((int) ($filteredOrders ?? count($orders))) ?> pesanan</div>
      <form class="filters" method="get" action="/admin/order/online">
        <label class="search-box"><span class="material-icons" aria-hidden="true">search</span><span class="visually-hidden">Cari pesanan</span><input class="control search-control" name="q" type="search" value="<?= esc($orderQuery ?? '') ?>" placeholder="Cari ID, nama, email, telepon…" autocomplete="off"></label>
        <label><span class="visually-hidden">Filter status</span><select class="control" name="status"><option value="">Semua status</option><?php foreach (['Menunggu Pembayaran'=>'Menunggu pembayaran','Proses'=>'Diproses','Dikirim'=>'Dikirim','Selesai'=>'Selesai','Dibatalkan'=>'Dibatalkan','Gagal'=>'Gagal'] as $key=>$label): ?><option value="<?= esc($key, 'attr') ?>" <?= ($orderStatusFilter ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option><?php endforeach; ?></select></label>
        <button class="action action-primary" type="submit">Terapkan</button><?php if (($orderQuery ?? '') !== '' || ($orderStatusFilter ?? '') !== ''): ?><a class="action" href="/admin/order/online">Reset</a><?php endif; ?>
      </form>
    </div>
    <?php if ($isDev): ?><div class="bulk-order-tools"><label><input type="checkbox" data-select-all-orders aria-label="Pilih semua pesanan yang dapat dihapus pada halaman ini"> Pilih semua pesanan terminal di halaman ini</label><span class="bulk-order-count" aria-live="polite"><span data-selected-order-count>0</span> dipilih</span><button class="action icon-action-danger" type="button" data-delete-selected-orders disabled>Hapus pilihan</button></div><?php endif; ?>
    <div class="orders-list" id="ordersList">
      <?php foreach ($orders as $index => $p):
        $mid = is_array($p['data_mid'] ?? null) ? $p['data_mid'] : (json_decode((string) ($p['data_mid'] ?? ''), true) ?: []);
        $transactionTime = strtotime((string) ($mid['transaction_time'] ?? '')) ?: 0;
        $formattedDate = $transactionTime ? date('d M Y · H:i', $transactionTime) : 'Tanggal tidak tersedia';
        $statusText = trim((string) ($p['status'] ?? 'Status tidak diketahui'));
        $statusKey = strtolower($statusText);
        $amount = (int) ($mid['gross_amount'] ?? 0);
        $isActive = in_array($statusText, ['Proses', 'Dikirim'], true);
        $isPending = $statusText === 'Menunggu Pembayaran';
        $isDeletable = in_array($statusText, ['Selesai', 'Dibatalkan', 'Gagal', 'Kadaluarsa', 'Ditolak'], true);
        $manualPayment = ($mid['payment_type'] ?? '') === 'admin';
      ?>
      <article class="order-row" data-order-row data-index="<?= (int) $index ?>" data-status="<?= esc($statusKey, 'attr') ?>">
        <div class="order-row__id"><div class="order-id"><?= esc($p['id_midtrans'] ?? '—') ?></div><div class="order-date"><?= esc($formattedDate) ?></div></div>
        <div class="order-row__customer"><div class="customer-name"><?= esc($p['nama'] ?? 'Nama tidak tersedia') ?></div><div class="customer-email"><?= esc($p['email'] ?? '') ?></div></div>
        <div class="order-row__amount amount">Rp <?= number_format($amount, 0, ',', '.') ?></div>
        <div class="order-row__status"><span class="status" data-status="<?= esc($statusKey, 'attr') ?>"><?= esc($statusText) ?></span></div>
        <div class="order-row__actions row-actions">
          <?php if ($isDev && $isDeletable): ?><input class="delete-order-select" type="checkbox" data-order-delete-select value="<?= esc((string) ($p['id_midtrans'] ?? ''), 'attr') ?>" aria-label="Pilih pesanan <?= esc($p['id_midtrans'] ?? '', 'attr') ?> untuk dihapus"><?php endif; ?>
          <button class="icon-action" type="button" data-order-details="<?= (int) $index ?>" aria-label="Lihat detail pesanan <?= esc($p['id_midtrans'] ?? '', 'attr') ?>" title="Detail"><span class="material-icons" aria-hidden="true">visibility</span></button>
          <a class="icon-action" href="/invoice/<?= rawurlencode((string) ($p['id_midtrans'] ?? '')) ?>" aria-label="Invoice" title="Invoice"><span class="material-icons" aria-hidden="true">description</span></a>
          <?php if ($isActive): ?>
            <a class="icon-action" href="/admin/labelbarang/<?= rawurlencode((string) ($p['id_midtrans'] ?? '')) ?>" target="_blank" rel="noopener" aria-label="Label barang" title="Label barang"><span class="material-icons" aria-hidden="true">label_outline</span></a>
            <button class="icon-action" type="button" data-edit-resi="<?= esc((string) ($p['id_midtrans'] ?? ''), 'attr') ?>" data-edit-resi-courier="<?= esc((string) ($p['kurir']['nama'] ?? ''), 'attr') ?>" data-edit-resi-service="<?= esc((string) ($p['kurir']['deskripsi'] ?? ''), 'attr') ?>" data-edit-resi-number="<?= esc((string) ($p['resi'] ?? ''), 'attr') ?>" aria-label="Edit resi" title="Edit resi"><span class="material-icons" aria-hidden="true">local_shipping</span></button>
            <a class="icon-action" href="/admin/suratjalan/<?= rawurlencode((string) ($p['id_midtrans'] ?? '')) ?>" target="_blank" rel="noopener" aria-label="Surat jalan" title="Surat jalan"><span class="material-icons" aria-hidden="true">print</span></a>
          <?php elseif ($isPending): ?>
            <?php if ($manualPayment): ?><form method="post" action="/admin/confirm-mp/<?= (int) ($p['id'] ?? 0) ?>" data-ilena-confirm="Konfirmasi bahwa pembayaran manual telah diterima?" data-ilena-confirm-title="Konfirmasi pembayaran"><?= csrf_field() ?><button class="icon-action icon-action-label" type="submit" title="Konfirmasi pembayaran manual">Tandai lunas</button></form><?php endif; ?>
            <button class="icon-action icon-action-danger" type="button" data-cancel-order="<?= esc((string) ($p['id_midtrans'] ?? ''), 'attr') ?>" aria-label="Batalkan pesanan" title="Batalkan pesanan"><span class="material-icons" aria-hidden="true">close</span></button>
          <?php endif; ?>
          <?php if ($isDev): ?><button class="icon-action icon-action-danger" type="button" data-delete-order="<?= esc((string) ($p['id_midtrans'] ?? ''), 'attr') ?>" aria-label="Hapus pesanan lokal <?= esc($p['id_midtrans'] ?? '', 'attr') ?>" title="Hapus data pesanan dari Ilena"><span class="material-icons" aria-hidden="true">delete_forever</span></button><?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
      <?php if (!$orders && (($orderQuery ?? '') !== '' || ($orderStatusFilter ?? '') !== '')): ?><div class="empty-state"><strong>Pesanan tidak ditemukan</strong>Coba ubah kata kunci atau filter status.</div><?php elseif (!$orders): ?><div class="empty-state"><strong>Belum ada pesanan online</strong>Pesanan baru akan tampil di sini.</div><?php endif; ?>
    </div>
    <?php if (($totalPages ?? 1) > 1): ?><nav class="orders-pagination" aria-label="Halaman daftar pesanan"><span>Halaman <?= (int) $currentPage ?> dari <?= (int) $totalPages ?></span><div><?php if ($currentPage > 1): ?><a class="action" href="<?= esc($pageUrl($currentPage - 1), 'attr') ?>" rel="prev">Sebelumnya</a><?php endif; ?><?php if ($currentPage < $totalPages): ?><a class="action" href="<?= esc($pageUrl($currentPage + 1), 'attr') ?>" rel="next">Berikutnya</a><?php endif; ?></div></nav><?php endif; ?>
  </section>
</main>

<div class="order-dialog" id="orderDialog" role="dialog" aria-modal="true" aria-labelledby="detailTitle" aria-hidden="true">
  <div class="order-dialog__panel"><header class="order-dialog__head"><div><div class="eyebrow">Rincian pesanan</div><h2 id="detailTitle">—</h2></div><button class="order-dialog__close" type="button" data-close-order aria-label="Tutup"><span class="material-icons" aria-hidden="true">close</span></button></header>
    <div class="order-dialog__customer"><div class="order-dialog__field"><label>Penerima</label><div data-detail="name">—</div></div><div class="order-dialog__field"><label>Email</label><div data-detail="email">—</div></div><div class="order-dialog__field"><label>Telepon</label><div data-detail="phone">—</div></div><div class="order-dialog__field"><label>Status</label><div data-detail="status">—</div></div><div class="order-dialog__field" style="grid-column:1/-1"><label>Alamat</label><div data-detail="address">—</div></div></div>
    <div class="table-responsive"><table class="order-dialog__items"><thead><tr><th>Produk</th><th>Jumlah</th><th>Harga satuan</th><th>Total</th></tr></thead><tbody id="detailItems"></tbody></table></div>
    <div class="order-dialog__footer"><a class="action" data-detail-invoice href="#">Buka invoice</a><button class="action action-primary" type="button" data-close-order>Tutup detail</button></div>
  </div>
</div>

<div class="resi-dialog" id="resiDialog" role="dialog" aria-modal="true" aria-labelledby="resiTitle" aria-hidden="true"><div class="resi-dialog__panel"><h2 id="resiTitle">Perbarui pengiriman</h2><form action="/admin/actioneditresi" method="post"><?= csrf_field() ?><input type="hidden" name="idMid"><input type="hidden" name="nama"><input type="hidden" name="deskripsi"><label for="resi-courier">Ekspedisi</label><select id="resi-courier" required><option value="">Pilih ekspedisi</option><?php foreach (['JNE','J&T Express','SiCepat','AnterAja','Pos Indonesia','TIKI','Ninja Xpress','Lion Parcel','ID Express','SAP Express','GoSend','GrabExpress','Lainnya'] as $carrier): ?><option value="<?= esc($carrier, 'attr') ?>"><?= esc($carrier) ?></option><?php endforeach; ?></select><div id="resi-courier-custom-wrap" hidden><label for="resi-courier-custom">Nama ekspedisi</label><input id="resi-courier-custom" name="nama_custom" type="text" maxlength="80" autocomplete="off"></div><label for="resi-service">Layanan</label><select id="resi-service" required><option value="">Pilih layanan</option></select><div id="resi-service-custom-wrap" hidden><label for="resi-service-custom">Nama layanan</label><input id="resi-service-custom" name="deskripsi_custom" type="text" maxlength="80" autocomplete="off"></div><label for="resi-number">Nomor resi</label><input id="resi-number" type="text" name="resi" maxlength="100" required autocomplete="off"><div class="resi-dialog__actions"><button class="action" type="button" data-close-resi>Batal</button><button class="action action-primary" type="submit">Simpan resi</button></div></form></div></div>
<?php if ($isDev): ?>
<div class="resi-dialog delete-order-dialog" id="deleteOrderDialog" role="dialog" aria-modal="true" aria-labelledby="deleteOrderTitle" aria-hidden="true">
  <div class="resi-dialog__panel">
    <div class="eyebrow">Tindakan Dev · Permanen</div>
    <h2 id="deleteOrderTitle">Hapus pesanan Ilena</h2>
    <p class="intro">Tindakan ini menghapus pesanan dan dokumen gudang/surat jalan terkait hanya dari database website Ilena. Tidak ada permintaan hapus ke Sistem Ilena.</p>
    <p class="intro">Antrean gudang, surat jalan, item surat jalan, dan mutasi stok yang terikat ke pesanan ikut dihapus. Saldo stok produk tidak diubah. Hanya pesanan terminal yang dapat dihapus.</p>
    <form id="deleteOrderForm" method="post" action="">
      <?= csrf_field() ?>
      <label for="deleteOrderPassword">Sandi akun Dev</label>
      <input id="deleteOrderPassword" name="current_password" type="password" autocomplete="current-password" required>
      <label for="deleteOrderWord">Ketik DELETE</label>
      <input id="deleteOrderWord" name="delete_confirmation" type="text" autocomplete="off" required pattern="DELETE" aria-describedby="deleteOrderIdentity">
      <p class="order-date" id="deleteOrderIdentity"><span data-delete-order-count>0</span> pesanan dipilih</p>
      <ul class="delete-order-selected-list" data-delete-order-list aria-label="Pesanan yang akan dihapus"></ul>
      <div id="deleteOrderHiddenIds"></div>
      <label class="delete-order-ack"><input type="checkbox" name="local_only_ack" value="1" required> Saya memahami data pesanan dan riwayat mutasi terkait dihapus permanen hanya dari Ilena; saldo stok tidak berubah.</label>
      <div class="resi-dialog__actions"><button class="action" type="button" data-close-delete>Batal</button><button class="action action-primary delete-order-submit" type="submit">Hapus pesanan permanen</button></div>
    </form>
  </div>
</div>
<style>
.delete-order-dialog .resi-dialog__panel{max-height:min(92dvh,760px);overflow:auto}.delete-order-dialog .intro{line-height:1.55;margin:8px 0 14px}.delete-order-dialog form{display:grid;gap:9px}.delete-order-dialog label{font-size:13px;font-weight:650}.delete-order-dialog input[type=password],.delete-order-dialog input[type=text]{width:100%;min-height:44px;padding:10px 12px;border:1px solid #d9d0c6;border-radius:7px;background:#fff;font:inherit}.delete-order-dialog input:focus-visible{outline:3px solid rgba(139,94,60,.24);border-color:#8b5e3c}.delete-order-dialog .delete-order-ack{display:flex;gap:10px;align-items:flex-start;margin:7px 0;font-weight:500}.delete-order-dialog .delete-order-ack input{width:20px;height:20px;flex:0 0 20px;margin-top:1px;accent-color:#8b5e3c}.delete-order-dialog .delete-order-submit{background:#a65043;border-color:#a65043}.delete-order-dialog .delete-order-submit:hover{background:#873d33}.delete-order-selected-list{max-height:120px;overflow:auto;padding-left:22px;margin:0;color:#63584e;font:12px ui-monospace,monospace}
</style>
<?php endif; ?>

<script>
(() => {
  const orders = JSON.parse(<?= json_encode($pesananJson, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>) || [];
  const detail = document.getElementById('orderDialog');
  const resi = document.getElementById('resiDialog');
  const deleteDialog = document.getElementById('deleteOrderDialog');
  const setText = (selector, value) => { const node = detail.querySelector(selector); if (node) node.textContent = value == null || value === '' ? '—' : String(value); };
  const openLayer = layer => { layer.classList.add('is-open'); layer.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; layer.querySelector('button')?.focus(); };
  const closeLayer = layer => { layer.classList.remove('is-open'); layer.setAttribute('aria-hidden','true'); document.body.style.overflow=''; };

  function showOrder(index) {
    const order = orders[index]; if (!order) return;
    const mid = order.data_mid || {}; let address = order.alamat || '';
    if (typeof address === 'string') { try { const parsed = JSON.parse(address); if (parsed && typeof parsed === 'object') address = parsed.alamat_lengkap || Object.values(parsed).filter(value => typeof value === 'string' && value.trim() !== '').join(', '); } catch (_) {} }
    setText('#detailTitle', order.id_midtrans); setText('[data-detail="name"]', order.nama); setText('[data-detail="email"]', order.email); setText('[data-detail="phone"]', order.nohp); setText('[data-detail="status"]', order.status); setText('[data-detail="address"]', typeof address === 'object' && address !== null ? (address.alamat_lengkap || JSON.stringify(address)) : address);
    const tbody = document.getElementById('detailItems'); tbody.replaceChildren();
    const lineItems = Array.isArray(order.items) ? order.items : [];
    lineItems.forEach(item => {
      const tr = document.createElement('tr');
      const quantity = Number(item.quantity ?? item.jumlah ?? 0) || 0;
      const unitPrice = Number(item.price ?? item.harga ?? 0) || 0;
      const values = [item.name || item.nama || 'Produk', quantity.toLocaleString('id-ID'), 'Rp ' + unitPrice.toLocaleString('id-ID'), 'Rp ' + (unitPrice * quantity).toLocaleString('id-ID')];
      values.forEach((value, index) => { const td = document.createElement('td'); td.textContent = value; if (index === 1) td.style.textAlign='center'; tr.append(td); }); tbody.append(tr);
    });
    if (!lineItems.length) { const tr=document.createElement('tr'), td=document.createElement('td'); td.colSpan=4; td.textContent='Rincian item tidak tersedia.'; tr.append(td); tbody.append(tr); }
    const fallbackTotal=lineItems.reduce((sum,item)=>sum+(Number(item.price ?? item.harga ?? 0)||0)*(Number(item.quantity ?? item.jumlah ?? 0)||0),0);
    const totalRow=document.createElement('tr'), totalLabel=document.createElement('td'), totalAmount=document.createElement('td');
    totalLabel.colSpan=3; totalLabel.textContent='Total pesanan'; totalLabel.style.fontWeight='700'; totalLabel.style.textAlign='right';
    totalAmount.textContent='Rp '+Number(mid.gross_amount ?? fallbackTotal).toLocaleString('id-ID'); totalAmount.style.fontWeight='700'; totalRow.append(totalLabel,totalAmount); tbody.append(totalRow);
    detail.querySelector('[data-detail-invoice]').href = '/invoice/' + encodeURIComponent(order.id_midtrans || '');
    openLayer(detail);
  }
  document.querySelectorAll('[data-order-details]').forEach(button => button.addEventListener('click', () => showOrder(Number(button.dataset.orderDetails))));
  detail.querySelectorAll('[data-close-order]').forEach(button => button.addEventListener('click', () => closeLayer(detail)));
  detail.addEventListener('click', event => { if (event.target === detail) closeLayer(detail); });

  document.querySelectorAll('[data-edit-resi]').forEach(button => button.addEventListener('click', () => {
    const courier = resi.querySelector('#resi-courier');
    const service = resi.querySelector('#resi-service');
    const savedCourier = button.dataset.editResiCourier || '';
    const savedService = button.dataset.editResiService || '';
    courier.value = [...courier.options].some(option => option.value === savedCourier) ? savedCourier : (savedCourier ? 'Lainnya' : '');
    courier.dispatchEvent(new Event('change'));
    if (courier.value === 'Lainnya') resi.querySelector('[name="nama_custom"]').value = savedCourier;
    fillServiceChoices(savedService);
    if (service.value === 'Lainnya') resi.querySelector('[name="deskripsi_custom"]').value = savedService;
    resi.querySelector('[name="idMid"]').value = button.dataset.editResi || '';
    resi.querySelector('[name="resi"]').value = button.dataset.editResiNumber === 'Menunggu pengiriman' ? '' : (button.dataset.editResiNumber || '');
    openLayer(resi);
  }));
  const courierSelect = resi.querySelector('#resi-courier');
  const serviceSelect = resi.querySelector('#resi-service');
  const serviceChoices = {
    'JNE': ['REG','YES','OKE','JTR','Cargo'], 'J&T Express': ['EZ','Eco','Super'],
    'SiCepat': ['REG','BEST','HALU','GOKIL'], 'AnterAja': ['Reguler','Next Day','Same Day','Cargo'],
    'Pos Indonesia': ['Pos Reguler','Pos Kilat Khusus','Pos Nextday'], 'TIKI': ['REG','ONS','ECO','TRC'],
    'Ninja Xpress': ['Standard','Next Day','Cargo'], 'Lion Parcel': ['REGPACK','JAGOPACK','BOSSPACK'],
    'ID Express': ['Reguler','Same Day'], 'SAP Express': ['Reguler','Same Day'],
    'GoSend': ['Instant','Same Day'], 'GrabExpress': ['Instant','Same Day']
  };
  const fillServiceChoices = (selected = '') => {
    serviceSelect.replaceChildren(new Option('Pilih layanan', ''), ...((serviceChoices[courierSelect.value] || ['Reguler']).map(value => new Option(value, value))), new Option('Lainnya', 'Lainnya'));
    serviceSelect.value = [...serviceSelect.options].some(option => option.value === selected) ? selected : (selected ? 'Lainnya' : '');
    serviceSelect.dispatchEvent(new Event('change'));
  };
  courierSelect.addEventListener('change', () => {
    const custom = courierSelect.value === 'Lainnya';
    resi.querySelector('#resi-courier-custom-wrap').hidden = !custom;
    resi.querySelector('[name="nama_custom"]').required = custom;
    fillServiceChoices();
  });
  serviceSelect.addEventListener('change', () => {
    const custom = serviceSelect.value === 'Lainnya';
    resi.querySelector('#resi-service-custom-wrap').hidden = !custom;
    resi.querySelector('[name="deskripsi_custom"]').required = custom;
  });
  resi.querySelector('form').addEventListener('submit', () => {
    resi.querySelector('[name="nama"]').value = courierSelect.value === 'Lainnya' ? resi.querySelector('[name="nama_custom"]').value.trim() : courierSelect.value;
    resi.querySelector('[name="deskripsi"]').value = serviceSelect.value === 'Lainnya' ? resi.querySelector('[name="deskripsi_custom"]').value.trim() : serviceSelect.value;
  });
  resi.querySelector('[data-close-resi]').addEventListener('click', () => closeLayer(resi));
  resi.addEventListener('click', event => { if (event.target === resi) closeLayer(resi); });
  if (deleteDialog) {
    const deleteForm = document.getElementById('deleteOrderForm');
    document.querySelectorAll('[data-delete-order]').forEach(button => button.addEventListener('click', () => {
      const orderId = button.dataset.deleteOrder || '';
      openDeleteDialog([orderId]);
    }));
    const selectableOrders = [...document.querySelectorAll('[data-order-delete-select]')];
    const selectAllOrders = document.querySelector('[data-select-all-orders]');
    const deleteSelectedButton = document.querySelector('[data-delete-selected-orders]');
    const selectedCount = document.querySelector('[data-selected-order-count]');
    const refreshBulkSelection = () => {
      const selected = selectableOrders.filter(input => input.checked);
      selectedCount.textContent = String(selected.length);
      deleteSelectedButton.disabled = selected.length === 0;
      selectAllOrders.checked = selected.length > 0 && selected.length === selectableOrders.length;
      selectAllOrders.indeterminate = selected.length > 0 && selected.length < selectableOrders.length;
    };
    selectableOrders.forEach(input => input.addEventListener('change', refreshBulkSelection));
    selectAllOrders.addEventListener('change', () => { selectableOrders.forEach(input => { input.checked = selectAllOrders.checked; }); refreshBulkSelection(); });
    deleteSelectedButton.addEventListener('click', () => openDeleteDialog(selectableOrders.filter(input => input.checked).map(input => input.value)));
    function openDeleteDialog(ids) {
      ids = [...new Set(ids)].filter(Boolean);
      if (!ids.length || ids.length > 50) return;
      deleteForm.reset();
      deleteForm.action = '/admin/order/delete-bulk';
      const hidden = document.getElementById('deleteOrderHiddenIds');
      hidden.replaceChildren();
      const list = deleteDialog.querySelector('[data-delete-order-list]');
      list.replaceChildren();
      ids.forEach(id => {
        const input = document.createElement('input'); input.type = 'hidden'; input.name = 'order_ids[]'; input.value = id; hidden.append(input);
        const item = document.createElement('li'); item.textContent = id; list.append(item);
      });
      deleteDialog.querySelector('[data-delete-order-count]').textContent = String(ids.length);
      openLayer(deleteDialog);
      document.getElementById('deleteOrderPassword').focus();
    }
    deleteDialog.querySelector('[data-close-delete]').addEventListener('click', () => closeLayer(deleteDialog));
    deleteDialog.addEventListener('click', event => { if (event.target === deleteDialog) closeLayer(deleteDialog); });
    deleteForm.addEventListener('submit', async event => {
      event.preventDefault();
      if (!deleteForm.reportValidity()) return;
      const count = deleteDialog.querySelector('[data-delete-order-count]').textContent;
      if (!await window.IlenaDialog.confirm(`Hapus permanen ${count} pesanan pilihan dari Ilena?`, {title:'Konfirmasi penghapusan permanen', tone:'error'})) return;
      deleteForm.submit();
    });
  }
  document.querySelectorAll('[data-cancel-order]').forEach(button => button.addEventListener('click', async () => {
    if (!await window.IlenaDialog.confirm('Batalkan pesanan ini?', {title:'Batalkan pesanan', tone:'error'})) return;
    const form = document.createElement('form'); form.method='POST'; form.action='/cancelorder/' + encodeURIComponent(button.dataset.cancelOrder);
    const tokenName=document.querySelector('meta[name="csrf-token-name"]')?.content, tokenHash=document.querySelector('meta[name="csrf-token-hash"]')?.content;
    if(tokenName&&tokenHash){const token=document.createElement('input');token.type='hidden';token.name=tokenName;token.value=tokenHash;form.append(token);}
    document.body.append(form); form.submit();
  }));
  document.addEventListener('keydown', event => { if(event.key==='Escape'){if(detail.classList.contains('is-open'))closeLayer(detail);if(resi.classList.contains('is-open'))closeLayer(resi);if(deleteDialog?.classList.contains('is-open'))closeLayer(deleteDialog);} });
})();
</script>
<?= $this->endSection(); ?>
