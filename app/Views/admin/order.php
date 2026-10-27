<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<?php
$orders = $pesanan ?? [];
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
.orders-pagination{display:flex;justify-content:space-between;align-items:center;gap:12px;padding-top:16px;color:var(--o-muted);font-size:12px}.orders-pagination>div{display:flex;gap:8px}.orders-pagination .action{min-height:38px}
.order-dialog{position:fixed;inset:0;z-index:1200;display:none;align-items:center;justify-content:center;padding:18px;background:rgba(30,26,22,.54)}.order-dialog.is-open{display:flex}.order-dialog__panel{width:min(720px,100%);max-height:min(88vh,850px);overflow:auto;background:var(--o-paper);border-radius:12px;padding:clamp(18px,3vw,30px);box-shadow:0 24px 70px rgba(0,0,0,.2)}
.order-dialog .eyebrow,.resi-dialog .eyebrow{font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:var(--o-muted)}.order-dialog .action,.resi-dialog .action{display:inline-flex;min-height:42px;align-items:center;justify-content:center;gap:8px;padding:0 15px;border:1px solid var(--o-line);border-radius:8px;background:transparent;color:var(--o-ink);font:600 13px/1.2 "Plus Jakarta Sans",sans-serif;text-decoration:none;cursor:pointer}.order-dialog .action:hover,.resi-dialog .action:hover{background:#f1ede6}.order-dialog .action-primary,.resi-dialog .action-primary{background:var(--o-accent);border-color:var(--o-accent);color:#fff}.order-dialog .action-primary:hover,.resi-dialog .action-primary:hover{background:#70482f;color:#fff}
.order-dialog__head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;border-bottom:1px solid var(--o-line);padding-bottom:15px;margin-bottom:18px}.order-dialog__head h2{margin:3px 0;font-size:20px;letter-spacing:-.03em}.order-dialog__close{border:1px solid var(--o-line);background:transparent;border-radius:7px;width:40px;height:40px;cursor:pointer;color:var(--o-ink)}
.order-dialog__customer{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 24px;margin-bottom:22px}.order-dialog__field label{display:block;color:var(--o-muted);font-size:11px;margin-bottom:3px}.order-dialog__field div{font-size:13px;font-weight:550;overflow-wrap:anywhere}.order-dialog__items{width:100%;border-collapse:collapse}.order-dialog__items th,.order-dialog__items td{padding:10px 7px;border-bottom:1px solid var(--o-line);text-align:left;font-size:13px}.order-dialog__items th{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--o-muted)}.order-dialog__items td:last-child,.order-dialog__items th:last-child{text-align:right;white-space:nowrap}.order-dialog__footer{display:flex;justify-content:flex-end;gap:8px;flex-wrap:wrap;margin-top:20px}
.resi-dialog{position:fixed;inset:0;z-index:1210;display:none;align-items:center;justify-content:center;padding:18px;background:rgba(30,26,22,.54)}.resi-dialog.is-open{display:flex}.resi-dialog__panel{width:min(460px,100%);background:var(--o-paper);border-radius:12px;padding:24px}.resi-dialog h2{font-size:19px;margin:0 0 18px}.resi-dialog label{display:block;font-size:12px;font-weight:650;margin:12px 0 5px}.resi-dialog input{width:100%;height:44px;border:1px solid #ded7ce;border-radius:7px;padding:0 11px;background:#fff;font:inherit}.resi-dialog__actions{display:flex;justify-content:flex-end;gap:8px;margin-top:20px}
@media(max-width:1050px){.online-orders .order-row{grid-template-columns:minmax(170px,1fr) minmax(150px,1fr) minmax(135px,.8fr) minmax(130px,.75fr)}.online-orders .row-actions{grid-column:1/-1;justify-content:flex-start;padding-top:8px;border-top:1px solid #f0ebe5}}
@media(max-width:700px){.online-orders{padding-top:0}.online-orders .page-head{align-items:flex-start;flex-direction:column;margin-bottom:18px}.online-orders .head-actions{width:100%}.online-orders .head-actions .action{flex:1}.online-orders .metrics{justify-content:space-between;margin-bottom:16px}.online-orders .metric{min-width:0;flex:1;padding:11px 6px 11px 0;margin-right:8px}.online-orders .metric strong{font-size:18px}.online-orders .metric span{font-size:10px}.online-orders .order-controls{align-items:stretch;flex-direction:column}.online-orders .filters{justify-content:stretch}.online-orders .search-box{max-width:none}.online-orders .filters select{flex:1;min-width:0}.online-orders .results-label{order:2}.online-orders .order-row{grid-template-columns:minmax(0,1fr) auto;gap:9px 12px;padding:15px 2px}.online-orders .order-row__id{grid-column:1}.online-orders .order-row__status{grid-column:2;grid-row:1;justify-self:end}.online-orders .order-row__customer{grid-column:1/-1}.online-orders .order-row__amount{grid-column:1}.online-orders .order-row__actions{grid-column:2;grid-row:3;justify-content:flex-end;padding:0;border:0}.online-orders .row-actions .icon-action{min-width:40px}.order-dialog__customer{grid-template-columns:1fr;gap:11px}.order-dialog__items th,.order-dialog__items td{padding:9px 4px;font-size:12px}.order-dialog__footer .action{flex:1}}
@media(prefers-reduced-motion:reduce){.online-orders *,.order-dialog *,.resi-dialog *{scroll-behavior:auto!important;transition:none!important;animation:none!important}}
</style>

<main class="online-orders">
  <header class="page-head">
    <div><div class="eyebrow">Operasional · Toko online</div><h1>Pesanan online</h1><p class="intro">Cari pesanan, tinjau detail, dan lanjutkan pemrosesan.</p></div>
    <div class="head-actions"><a class="action" href="/admin/order/add"><span class="material-icons" aria-hidden="true">add</span>Tambah pesanan</a></div>
  </header>

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
        $manualPayment = ($mid['payment_type'] ?? '') === 'admin';
      ?>
      <article class="order-row" data-order-row data-index="<?= (int) $index ?>" data-status="<?= esc($statusKey, 'attr') ?>">
        <div class="order-row__id"><div class="order-id"><?= esc($p['id_midtrans'] ?? '—') ?></div><div class="order-date"><?= esc($formattedDate) ?></div></div>
        <div class="order-row__customer"><div class="customer-name"><?= esc($p['nama'] ?? 'Nama tidak tersedia') ?></div><div class="customer-email"><?= esc($p['email'] ?? '') ?></div></div>
        <div class="order-row__amount amount">Rp <?= number_format($amount, 0, ',', '.') ?></div>
        <div class="order-row__status"><span class="status" data-status="<?= esc($statusKey, 'attr') ?>"><?= esc($statusText) ?></span></div>
        <div class="order-row__actions row-actions">
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
    <div class="order-dialog__footer"><a class="action" data-detail-invoice href="#">Buka invoice</a><a class="action action-primary" data-detail-order href="#">Buka pesanan</a></div>
  </div>
</div>

<div class="resi-dialog" id="resiDialog" role="dialog" aria-modal="true" aria-labelledby="resiTitle" aria-hidden="true"><div class="resi-dialog__panel"><h2 id="resiTitle">Perbarui pengiriman</h2><form action="/admin/actioneditresi" method="post"><?= csrf_field() ?><input type="hidden" name="idMid"><label for="resi-courier">Ekspedisi</label><input id="resi-courier" type="text" name="nama" required><label for="resi-service">Layanan</label><input id="resi-service" type="text" name="deskripsi" required><label for="resi-number">Nomor resi</label><input id="resi-number" type="text" name="resi" required><div class="resi-dialog__actions"><button class="action" type="button" data-close-resi>Batal</button><button class="action action-primary" type="submit">Simpan resi</button></div></form></div></div>

<script>
(() => {
  const orders = JSON.parse(<?= json_encode($pesananJson, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>) || [];
  const detail = document.getElementById('orderDialog');
  const resi = document.getElementById('resiDialog');
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
    detail.querySelector('[data-detail-order]').href = '/order/' + encodeURIComponent(order.id_midtrans || '');
    openLayer(detail);
  }
  document.querySelectorAll('[data-order-details]').forEach(button => button.addEventListener('click', () => showOrder(Number(button.dataset.orderDetails))));
  detail.querySelector('[data-close-order]').addEventListener('click', () => closeLayer(detail));
  detail.addEventListener('click', event => { if (event.target === detail) closeLayer(detail); });

  document.querySelectorAll('[data-edit-resi]').forEach(button => button.addEventListener('click', () => {
    resi.querySelector('[name="idMid"]').value = button.dataset.editResi || '';
    resi.querySelector('[name="nama"]').value = button.dataset.editResiCourier || '';
    resi.querySelector('[name="deskripsi"]').value = button.dataset.editResiService || '';
    resi.querySelector('[name="resi"]').value = button.dataset.editResiNumber || '';
    openLayer(resi);
  }));
  resi.querySelector('[data-close-resi]').addEventListener('click', () => closeLayer(resi));
  resi.addEventListener('click', event => { if (event.target === resi) closeLayer(resi); });
  document.querySelectorAll('[data-cancel-order]').forEach(button => button.addEventListener('click', async () => {
    if (!await window.IlenaDialog.confirm('Batalkan pesanan ini?', {title:'Batalkan pesanan', tone:'error'})) return;
    const form = document.createElement('form'); form.method='POST'; form.action='/cancelorder/' + encodeURIComponent(button.dataset.cancelOrder);
    const tokenName=document.querySelector('meta[name="csrf-token-name"]')?.content, tokenHash=document.querySelector('meta[name="csrf-token-hash"]')?.content;
    if(tokenName&&tokenHash){const token=document.createElement('input');token.type='hidden';token.name=tokenName;token.value=tokenHash;form.append(token);}
    document.body.append(form); form.submit();
  }));
  document.addEventListener('keydown', event => { if(event.key==='Escape'){if(detail.classList.contains('is-open'))closeLayer(detail);if(resi.classList.contains('is-open'))closeLayer(resi);} });
})();
</script>
<?= $this->endSection(); ?>
