<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<?php
$voucherSorted = $voucher ?? [];
usort($voucherSorted, static fn($a, $b) => ((int) ($b['id'] ?? 0)) <=> ((int) ($a['id'] ?? 0)));
$q = (string) (service('request')->getGet('q') ?? '');
$targetLabels = ['semua' => 'Semua pengguna', 'baru' => 'Member baru', 'lama' => 'Member lama', 'spesifik' => 'Email tertentu'];
?>
<style>
.voucher-page{--v-ink:#2d2823;--v-muted:#796f65;--v-line:#e9e2d9;--v-paper:#fffdfa;--v-accent:#8b5e3c;color:var(--v-ink)}
.voucher-page .eyebrow{font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:var(--v-muted);font-weight:700}
.voucher-page h1{font-size:clamp(1.5rem,2.5vw,2rem);font-weight:650;letter-spacing:-.04em;margin:.2rem 0 .45rem}
.voucher-page .intro{color:var(--v-muted);max-width:680px;margin:0}
.voucher-page .voucher-section{background:var(--v-paper);border:1px solid var(--v-line);border-radius:12px;padding:clamp(16px,2.2vw,28px);}
.voucher-page .section-heading{font-size:1rem;font-weight:700;letter-spacing:-.02em;margin:0}
.voucher-page .subtle{font-size:.82rem;color:var(--v-muted)}
.voucher-page .form-label{font-size:.8rem;font-weight:650;color:#51483f;margin-bottom:.35rem}
.voucher-page .form-control,.voucher-page .form-select{min-height:43px;border-color:#ded7ce;border-radius:8px;background-color:#fff;font-size:.9rem}
.voucher-page textarea.form-control{min-height:100px}
.voucher-page .form-text{font-size:.75rem;color:var(--v-muted)}
.voucher-page .form-check{margin:0}
.voucher-page .form-check-input:checked{background-color:var(--v-accent);border-color:var(--v-accent)}
.voucher-page .btn-primary-ilena{background:var(--v-accent);border:1px solid var(--v-accent);color:white;border-radius:8px;padding:.65rem 1rem;font-weight:650}
.voucher-page .btn-primary-ilena:hover{background:#70482f;color:white}
.voucher-page .btn-quiet{border:1px solid var(--v-line);background:transparent;color:var(--v-ink);border-radius:8px;padding:.6rem .85rem}
.voucher-page .btn-quiet:hover{background:#f5f0ea}
.voucher-page .toolbar{display:flex;gap:.6rem;flex-wrap:wrap}
.voucher-page .table-wrap{overflow:auto;border-top:1px solid var(--v-line);margin:0 -1px}
.voucher-page table{min-width:850px;margin:0}
.voucher-page thead th{font-size:.7rem;letter-spacing:.09em;text-transform:uppercase;color:var(--v-muted);font-weight:700;padding:13px 14px;border-bottom:1px solid var(--v-line);white-space:nowrap}
.voucher-page tbody td{padding:14px;border-bottom:1px solid #f0ebe5;vertical-align:middle}
.voucher-page tbody tr:last-child td{border-bottom:0}
.voucher-page .code{font-family:ui-monospace,SFMono-Regular,Consolas,monospace;font-weight:700;letter-spacing:.03em;color:#6e482f}
.voucher-page .name{font-weight:650}
.voucher-page .meta{font-size:.76rem;color:var(--v-muted);margin-top:3px;max-width:300px}
.voucher-page .tag{display:inline-flex;align-items:center;border:1px solid var(--v-line);border-radius:99px;padding:4px 9px;font-size:.74rem;color:#5f554b;white-space:nowrap}
.voucher-page .tag.is-active{border-color:#c7d9c5;color:#41643e;background:#f5faf3}
.voucher-page .tag.is-off{border-color:#e4d4ce;color:#815343;background:#fbf6f3}
.voucher-page .actions{display:flex;align-items:center;gap:7px;white-space:nowrap}
.voucher-page .switch{position:relative;display:inline-flex;width:42px;height:24px;vertical-align:middle}
.voucher-page .switch input{opacity:0;width:0;height:0;position:absolute}
.voucher-page .switch-track{position:absolute;inset:0;border-radius:99px;background:#d7d0c8;transition:.18s;cursor:pointer}
.voucher-page .switch-track:after{content:"";position:absolute;width:18px;height:18px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.18s;box-shadow:0 1px 3px #0002}
.voucher-page .switch input:checked+.switch-track{background:var(--v-accent)}
.voucher-page .switch input:checked+.switch-track:after{transform:translateX(18px)}
.voucher-page .switch input:focus-visible+.switch-track{outline:2px solid #8b5e3c;outline-offset:3px}
.voucher-page .switch.is-loading{opacity:.55;pointer-events:none}
.voucher-page .email-field[hidden]{display:none!important}
.voucher-page .modal-content{border:1px solid var(--v-line);border-radius:12px}
.voucher-page .modal-header,.voucher-page .modal-footer{border-color:var(--v-line)}
.voucher-page .empty{padding:42px 16px;text-align:center;color:var(--v-muted)}
.voucher-page .toast-stack{position:fixed;right:20px;bottom:20px;z-index:1100;display:grid;gap:8px;pointer-events:none}
.voucher-page .toast-item{pointer-events:auto;max-width:min(380px,calc(100vw - 32px));background:#fffdfa;border:1px solid var(--v-line);border-left:3px solid #66855c;border-radius:8px;padding:12px 14px;box-shadow:0 10px 28px #2d28231a;animation:voucher-in .18s ease-out}
.voucher-page .toast-item.error{border-left-color:#a65c4d}.voucher-page .toast-item.info{border-left-color:#8b5e3c}
.voucher-page .toast-title{font-weight:700;font-size:.86rem}.voucher-page .toast-message{font-size:.82rem;color:var(--v-muted);margin-top:2px}
@keyframes voucher-in{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
@media(max-width:767.98px){.voucher-page .mobile-hide{display:none}.voucher-page .toolbar>*{flex:1}.voucher-page .toolbar .btn{font-size:.82rem}.voucher-page .voucher-section{padding:16px}.voucher-page .table-wrap{margin:0 -16px}.voucher-page .toast-stack{right:12px;bottom:12px}}
@media(prefers-reduced-motion:reduce){.voucher-page *{animation:none!important;transition:none!important}}
</style>

<section class="voucher-page container-fluid px-0 mt-2">
  <header class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div><div class="eyebrow">Promosi · Administrasi</div><h1>Kelola voucher</h1><p class="intro">Atur diskon, masa berlaku, batas pemakaian, dan siapa yang dapat menggunakannya.</p></div>
    <nav class="toolbar" aria-label="Aksi voucher"><a class="btn btn-quiet" href="/admin/voucher/usage">Riwayat pemakaian</a><a class="btn btn-primary-ilena" href="#voucher-create">Buat voucher</a></nav>
  </header>

  <?php if (session()->getFlashdata('msg')): ?><div id="voucher-flash" class="d-none"><?= esc(session()->getFlashdata('msg')) ?></div><?php endif; ?>

  <section class="voucher-section mb-4" id="voucher-create" aria-labelledby="create-title">
    <div class="d-flex justify-content-between align-items-start gap-3 mb-4"><div><h2 class="section-heading" id="create-title">Voucher baru</h2><div class="subtle mt-1">Kode dibuat huruf kapital; syarat yang kosong berarti tanpa batas.</div></div></div>
    <form method="post" action="/admin/voucher/add" class="row g-3" id="voucherCreateForm">
      <?= csrf_field(); ?>
      <div class="col-12 col-md-4"><label class="form-label" for="new-code">Kode voucher</label><input id="new-code" name="kode" class="form-control" maxlength="40" pattern="[A-Za-z0-9][A-Za-z0-9_-]{2,39}" placeholder="WELCOME10" autocomplete="off" required><div class="form-text">3–40 karakter: huruf, angka, - atau _.</div></div>
      <div class="col-12 col-md-4"><label class="form-label" for="new-name">Nama voucher</label><input id="new-name" name="nama" class="form-control" maxlength="120" placeholder="Sambutan pelanggan baru" required></div>
      <div class="col-12 col-md-4"><label class="form-label" for="new-desc">Catatan (opsional)</label><input id="new-desc" name="deskripsi" class="form-control" maxlength="255" placeholder="Ringkasan promo"></div>
      <div class="col-6 col-md-3"><label class="form-label" for="new-type">Potongan</label><select id="new-type" name="tipe" class="form-select"><option value="persen">Persentase</option><option value="nominal">Nominal rupiah</option></select></div>
      <div class="col-6 col-md-3"><label class="form-label" for="new-value">Nilai potongan</label><input id="new-value" name="nilai" class="form-control" type="number" min="1" max="100" step="1" placeholder="10" required></div>
      <div class="col-6 col-md-3"><label class="form-label" for="new-min">Minimal belanja (Rp)</label><input id="new-min" name="minimal_belanja" class="form-control" type="number" min="0" step="1" value="0"></div>
      <div class="col-6 col-md-3"><label class="form-label" for="new-max">Batas pemakaian</label><input id="new-max" name="max_pakai" class="form-control" type="number" min="0" step="1" value="0"><div class="form-text">0 = tanpa batas.</div></div>
      <div class="col-6 col-md-3"><label class="form-label" for="new-start">Mulai berlaku</label><input id="new-start" name="mulai" class="form-control" type="date"></div>
      <div class="col-6 col-md-3"><label class="form-label" for="new-end">Berakhir</label><input id="new-end" name="berakhir" class="form-control" type="date"></div>
      <div class="col-12 col-md-6"><label class="form-label" for="new-target">Pengguna yang memenuhi syarat</label><select id="new-target" name="target" class="form-select"><option value="semua">Semua pengguna</option><option value="baru">Member baru</option><option value="lama">Member lama</option><option value="spesifik">Email tertentu</option></select></div>
      <div class="col-12 email-field" data-email-field="new" hidden><label class="form-label" for="new-emails">Email yang diizinkan</label><textarea id="new-emails" name="list_email" class="form-control" placeholder="nama@email.com, pelanggan@email.com"></textarea><div class="form-text">Pisahkan dengan baris baru, koma, titik koma, atau spasi.</div></div>
      <div class="col-12 d-flex flex-wrap gap-3 align-items-center pt-1">
        <label class="form-check"><input class="form-check-input" type="checkbox" name="auto_apply" value="1"> <span class="form-check-label">Terapkan otomatis saat memenuhi syarat</span></label>
        <label class="form-check"><input class="form-check-input" type="checkbox" name="sekali_pakai_per_user" value="1"> <span class="form-check-label">Maksimal sekali per pengguna</span></label>
        <label class="form-check"><input class="form-check-input" type="checkbox" name="aktif" value="1" checked> <span class="form-check-label">Aktifkan sekarang</span></label>
      </div>
      <div class="col-12 d-flex gap-2 pt-1"><button class="btn btn-primary-ilena" type="submit">Simpan voucher</button><button class="btn btn-quiet" type="reset">Kosongkan</button></div>
    </form>
  </section>

  <section class="voucher-section p-0" aria-labelledby="list-title">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 p-3 p-md-4">
      <div><h2 class="section-heading" id="list-title">Daftar voucher</h2><div class="subtle mt-1"><?= count($voucherSorted) ?> voucher<?= count($voucherSorted) === 1 ? '' : 's' ?> ditampilkan</div></div>
      <form method="get" action="/admin/voucher" class="d-flex gap-2" role="search"><label class="visually-hidden" for="voucher-search">Cari voucher</label><input id="voucher-search" name="q" value="<?= esc($q) ?>" class="form-control" placeholder="Cari kode atau nama" style="min-width:min(290px,62vw)"><button class="btn btn-quiet" type="submit">Cari</button><?php if ($q !== ''): ?><a class="btn btn-quiet" href="/admin/voucher">Reset</a><?php endif; ?></form>
    </div>
    <div class="table-wrap"><table class="table align-middle"><thead><tr><th>Voucher</th><th>Potongan</th><th>Syarat</th><th class="mobile-hide">Target</th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>
      <?php if ($voucherSorted): foreach ($voucherSorted as $v): $type = $v['tipe'] ?? 'persen'; $target = $v['target'] ?? 'semua'; ?>
      <tr data-row-id="<?= (int) $v['id'] ?>">
        <td><div class="code"><?= esc($v['kode']) ?></div><div class="name mt-1"><?= esc($v['nama']) ?></div><?php if (!empty($v['deskripsi'])): ?><div class="meta"><?= esc($v['deskripsi']) ?></div><?php endif; ?></td>
        <td class="text-nowrap"><strong><?= $type === 'persen' ? esc((string) (float) $v['nilai']) . '%' : 'Rp ' . number_format((float) $v['nilai'], 0, ',', '.') ?></strong><div class="meta"><?= !empty($v['auto_apply']) ? 'Otomatis' : 'Pilih saat checkout' ?></div></td>
        <td><div class="text-nowrap">Min. Rp <?= number_format((float) ($v['minimal_belanja'] ?? 0), 0, ',', '.') ?></div><div class="meta"><?= !empty($v['mulai']) ? date('d M Y', strtotime($v['mulai'])) : 'Langsung' ?> – <?= !empty($v['berakhir']) ? date('d M Y', strtotime($v['berakhir'])) : 'Tanpa akhir' ?></div><div class="meta">Batas: <?= (int) ($v['max_pakai'] ?? 0) > 0 ? (int) $v['max_pakai'] . ' kali' : 'tanpa batas' ?></div></td>
        <td class="mobile-hide"><span class="tag"><?= esc($targetLabels[$target] ?? 'Semua pengguna') ?></span><?php if (!empty($v['sekali_pakai_per_user'])): ?><div class="meta mt-1">Sekali per pengguna</div><?php endif; ?></td>
        <td><span class="tag <?= !empty($v['aktif']) ? 'is-active' : 'is-off' ?> status-label"><?= !empty($v['aktif']) ? 'Aktif' : 'Nonaktif' ?></span><label class="switch ms-2" title="Ubah status voucher <?= esc($v['kode'], 'attr') ?>"><input type="checkbox" class="toggle-aktif" data-id="<?= (int) $v['id'] ?>" data-code="<?= esc($v['kode'], 'attr') ?>" aria-label="Aktifkan voucher <?= esc($v['kode'], 'attr') ?>" <?= !empty($v['aktif']) ? 'checked' : '' ?>><span class="switch-track"></span></label></td>
        <td><div class="actions justify-content-end">
          <button type="button" class="btn btn-sm btn-quiet btn-edit-voucher" data-bs-toggle="modal" data-bs-target="#editVoucherModal"
            data-id="<?= (int) $v['id'] ?>" data-kode="<?= esc($v['kode'], 'attr') ?>" data-nama="<?= esc($v['nama'], 'attr') ?>" data-deskripsi="<?= esc($v['deskripsi'] ?? '', 'attr') ?>" data-tipe="<?= esc($type, 'attr') ?>" data-nilai="<?= esc((string) $v['nilai'], 'attr') ?>" data-minimal_belanja="<?= esc((string) ($v['minimal_belanja'] ?? 0), 'attr') ?>" data-max_pakai="<?= esc((string) ($v['max_pakai'] ?? 0), 'attr') ?>" data-mulai="<?= esc((string) ($v['mulai'] ?? ''), 'attr') ?>" data-berakhir="<?= esc((string) ($v['berakhir'] ?? ''), 'attr') ?>" data-target="<?= esc($target, 'attr') ?>" data-list_email="<?= esc((string) ($v['list_email'] ?? '[]'), 'attr') ?>" data-auto_apply="<?= !empty($v['auto_apply']) ? '1' : '0' ?>" data-sekali_pakai_per_user="<?= !empty($v['sekali_pakai_per_user']) ? '1' : '0' ?>" data-aktif="<?= !empty($v['aktif']) ? '1' : '0' ?>">Edit</button>
          <form method="post" action="/admin/voucher/delete/<?= (int) $v['id'] ?>" data-ilena-confirm="Hapus voucher <?= esc($v['kode'], 'attr') ?>?" data-ilena-confirm-title="Hapus voucher"><?= csrf_field() ?><button type="submit" class="btn btn-sm btn-quiet">Hapus</button></form>
        </div></td>
      </tr>
      <?php endforeach; else: ?><tr><td colspan="6" class="empty"><?= $q !== '' ? 'Tidak ada voucher yang cocok dengan pencarian.' : 'Belum ada voucher. Buat voucher pertama melalui formulir di atas.' ?></td></tr><?php endif; ?>
    </tbody></table></div>
  </section>
</section>

<div class="modal fade" id="editVoucherModal" tabindex="-1" aria-labelledby="editVoucherTitle" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content voucher-page">
  <div class="modal-header"><div><div class="eyebrow">Pengaturan promosi</div><h2 class="modal-title fs-5" id="editVoucherTitle">Edit voucher</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
  <form method="post" id="formEditVoucher" action=""><?= csrf_field() ?><div class="modal-body"><div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="edit-code">Kode voucher</label><input id="edit-code" name="kode" class="form-control" maxlength="40" required></div><div class="col-md-6"><label class="form-label" for="edit-name">Nama voucher</label><input id="edit-name" name="nama" class="form-control" maxlength="120" required></div>
    <div class="col-12"><label class="form-label" for="edit-desc">Catatan</label><input id="edit-desc" name="deskripsi" class="form-control" maxlength="255"></div>
    <div class="col-6 col-md-3"><label class="form-label" for="edit-type">Potongan</label><select id="edit-type" name="tipe" class="form-select"><option value="persen">Persentase</option><option value="nominal">Nominal rupiah</option></select></div><div class="col-6 col-md-3"><label class="form-label" for="edit-value">Nilai</label><input id="edit-value" name="nilai" type="number" min="1" max="100" step="1" class="form-control" required></div>
    <div class="col-6 col-md-3"><label class="form-label" for="edit-min">Minimal belanja (Rp)</label><input id="edit-min" name="minimal_belanja" type="number" min="0" step="1" class="form-control"></div><div class="col-6 col-md-3"><label class="form-label" for="edit-max">Batas pemakaian</label><input id="edit-max" name="max_pakai" type="number" min="0" step="1" class="form-control"></div>
    <div class="col-6"><label class="form-label" for="edit-start">Mulai berlaku</label><input id="edit-start" name="mulai" type="date" class="form-control"></div><div class="col-6"><label class="form-label" for="edit-end">Berakhir</label><input id="edit-end" name="berakhir" type="date" class="form-control"></div>
    <div class="col-12"><label class="form-label" for="edit-target">Pengguna yang memenuhi syarat</label><select id="edit-target" name="target" class="form-select"><option value="semua">Semua pengguna</option><option value="baru">Member baru</option><option value="lama">Member lama</option><option value="spesifik">Email tertentu</option></select></div>
    <div class="col-12 email-field" data-email-field="edit" hidden><label class="form-label" for="edit-emails">Email yang diizinkan</label><textarea id="edit-emails" name="list_email" class="form-control" placeholder="nama@email.com, pelanggan@email.com"></textarea><div class="form-text">Alamat yang sudah tercatat tetap dipertahankan; email baru ditambahkan ke daftar.</div></div>
    <div class="col-12 d-flex flex-wrap gap-3"><label class="form-check"><input class="form-check-input" type="checkbox" name="auto_apply" value="1" id="edit-auto"><span class="form-check-label">Terapkan otomatis</span></label><label class="form-check"><input class="form-check-input" type="checkbox" name="sekali_pakai_per_user" value="1" id="edit-once"><span class="form-check-label">Sekali per pengguna</span></label><label class="form-check"><input class="form-check-input" type="checkbox" name="aktif" value="1" id="edit-active"><span class="form-check-label">Aktif</span></label></div>
  </div></div><div class="modal-footer"><button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary-ilena">Simpan perubahan</button></div></form>
</div></div></div>
<div class="voucher-page"><div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div></div>

<script>
(() => {
  const stack = document.getElementById('toastStack');
  const notify = (message, type = 'success') => {
    if (!stack) return;
    const item = document.createElement('div'); item.className = `toast-item ${type}`;
    const title = document.createElement('div'); title.className = 'toast-title'; title.textContent = type === 'error' ? 'Perlu diperiksa' : (type === 'info' ? 'Informasi' : 'Selesai');
    const body = document.createElement('div'); body.className = 'toast-message'; body.textContent = message;
    item.append(title, body); stack.append(item); window.setTimeout(() => item.remove(), 4200);
  };
  const flash = document.getElementById('voucher-flash');
  if (flash) { const message = flash.textContent.trim(); notify(message, message.startsWith('Gagal:') || message.startsWith('Gagal ') ? 'error' : 'success'); }

  const typeControls = document.querySelectorAll('select[name="tipe"]');
  typeControls.forEach(select => {
    const update = () => { const value = select.closest('form').querySelector('[name="nilai"]'); value.max = select.value === 'persen' ? '100' : ''; value.placeholder = select.value === 'persen' ? '10' : '50000'; };
    select.addEventListener('change', update); update();
  });
  const targetControls = document.querySelectorAll('select[name="target"]');
  targetControls.forEach(select => {
    const update = () => { const field = select.closest('form').querySelector('.email-field'); if (field) field.hidden = select.value !== 'spesifik'; };
    select.addEventListener('change', update); update();
  });

  const editForm = document.getElementById('formEditVoucher');
  document.querySelectorAll('.btn-edit-voucher').forEach(button => button.addEventListener('click', () => {
    const data = button.dataset; editForm.action = `/admin/voucher/edit/${encodeURIComponent(data.id)}`;
    for (const [field, key] of Object.entries({kode:'kode',nama:'nama',deskripsi:'deskripsi',tipe:'tipe',nilai:'nilai',minimal_belanja:'minimal_belanja',max_pakai:'max_pakai',mulai:'mulai',berakhir:'berakhir',target:'target'})) {
      const input = editForm.querySelector(`[name="${field}"]`); if (input) input.value = data[key] || '';
    }
    let emails = []; try { const parsed = JSON.parse(data.list_email || '[]'); if (Array.isArray(parsed)) emails = parsed; } catch (_) {}
    editForm.querySelector('[name="list_email"]').value = emails.join('\n');
    document.getElementById('edit-auto').checked = data.auto_apply === '1'; document.getElementById('edit-once').checked = data.sekali_pakai_per_user === '1'; document.getElementById('edit-active').checked = data.aktif === '1';
    editForm.querySelector('[name="tipe"]').dispatchEvent(new Event('change')); editForm.querySelector('[name="target"]').dispatchEvent(new Event('change'));
  }));

  document.querySelectorAll('.toggle-aktif').forEach(input => input.addEventListener('change', async () => {
    const oldValue = !input.checked, active = input.checked ? 1 : 0, label = input.closest('td').querySelector('.status-label'), oldLabel = label.textContent;
    const wrap = input.closest('.switch'); wrap.classList.add('is-loading'); input.disabled = true;
    const body = new URLSearchParams({aktif: String(active)});
    const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content;
    const csrfHash = document.querySelector('meta[name="csrf-token-hash"]')?.content;
    if (csrfName && csrfHash) body.append(csrfName, csrfHash);
    try {
      const response = await fetch(`/admin/voucher/toggle/${encodeURIComponent(input.dataset.id)}`, {method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},body,credentials:'same-origin'});
      const json = await response.json(); if (!response.ok || !json.success) throw new Error(json.message || 'Status voucher gagal diperbarui.');
      label.textContent = active ? 'Aktif' : 'Nonaktif'; label.classList.toggle('is-active', !!active); label.classList.toggle('is-off', !active); notify(`Voucher ${input.dataset.code} ${active ? 'diaktifkan' : 'dinonaktifkan'}.`);
      const nextHash = json.csrfHash || json.csrf_hash; if (nextHash && csrfName) document.querySelector('meta[name="csrf-token-hash"]').content = nextHash;
    } catch (error) { input.checked = oldValue; label.textContent = oldLabel; notify(error.message || 'Status voucher gagal diperbarui.', 'error'); }
    finally { input.disabled = false; wrap.classList.remove('is-loading'); }
  }));
})();
</script>
<?= $this->endSection(); ?>
