<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<style>
/* ==== THEME HOOKS: gunakan variabel warna milikmu ==== */
:root {
    --brand: var(--merah);
    /* warna utama */
    --brand-weak: color-mix(in oklab, var(--merah) 15%, white);
    --ink: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --card: #ffffff;
    --card-weak: #fafafa;
}

/* tombol tema merah */
.btn-merah {
    background: var(--brand);
    border: 1px solid var(--brand);
    color: #fff;
    border-radius: 10px;
    padding: .6rem 1rem;
}

.btn-merah:hover {
    filter: brightness(.95);
    color: #fff;
}

.btn-outline-merah {
    border: 1px solid var(--brand);
    color: var(--brand);
    border-radius: 10px;
    padding: .6rem 1rem;
    background: transparent;
}

.btn-outline-merah:hover {
    background: var(--brand-weak);
    color: var(--brand);
}

/* ====== Scoped styling ====== */
.vouch-page .card {
    border: 1px solid var(--line);
    border-radius: 14px;
    background: var(--card);
}

.vouch-page .card-header {
    background: linear-gradient(180deg, var(--card) 0%, var(--card-weak) 100%);
    border-bottom: 1px solid #eef0f3;
    border-top-left-radius: 14px !important;
    border-top-right-radius: 14px !important;
}

.vouch-page .form-label {
    font-weight: 600;
    font-size: .9rem;
    color: #374151;
}

.vouch-page .hint {
    font-size: .78rem;
    color: var(--muted);
}

.vouch-page .title {
    letter-spacing: -.02em;
    color: var(--ink);
}

.vouch-page .badge-chip {
    border-radius: 999px;
    padding: .35rem .7rem;
    font-weight: 600;
    background: var(--brand-weak);
    color: var(--brand);
    border: 1px dashed var(--brand);
}

.vouch-page .table thead th {
    font-size: .82rem;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.vouch-page .table td,
.vouch-page .table th {
    vertical-align: middle;
}

.vouch-page .pill {
    border-radius: 999px;
    padding: .25rem .6rem;
    font-weight: 600;
    font-size: .78rem;
    background: #f3f4f6;
    border: 1px solid var(--line);
}

.vouch-page .toolbar {
    gap: .6rem;
}

.vouch-page .search-input {
    border-radius: 10px;
    padding-left: 40px;
}

.vouch-page .search-icon {
    position: absolute;
    left: 12px;
    top: 9px;
    color: #9ca3af;
}

.vouch-page .soft {
    background: #f8fafc;
    border: 1px dashed var(--line);
    border-radius: 12px;
}

.vouch-page .table-responsive {
    border-radius: 12px;
    overflow: hidden;
}

@media (max-width: 992px) {
    .vouch-page .grid-2 {
        grid-template-columns: 1fr !important;
    }
}

/* Modal look */
.modal-modern .modal-content {
    border-radius: 16px;
    border: 1px solid var(--line);
    box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
}

.modal-modern .modal-header {
    border-bottom: 1px solid #eef0f3;
}

.modal-modern .modal-title {
    font-weight: 700;
    letter-spacing: -.02em;
    color: var(--ink);
}

/* Toggle switch (pakai warna tema) */
.switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #e5e7eb;
    transition: .2s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background: white;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
}

input:checked+.slider {
    background: var(--brand);
}

input:checked+.slider:before {
    transform: translateX(22px);
}

/* Tombol kecil tabel, tetap tema */
.btn-chip {
    border-radius: 10px;
    padding: .4rem .7rem;
}

.btn-outline-primary {
    border-color: var(--brand);
    color: var(--brand);
}

.btn-outline-primary:hover {
    background: var(--brand-weak);
    color: var(--brand);
}

.btn-outline-danger {
    border-color: #ef4444;
    color: #ef4444;
}

.btn-outline-danger:hover {
    background: #fee2e2;
    color: #ef4444;
}
</style>

<div class="vouch-page container-fluid px-0 mt-2">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <h2 class="mb-1 title">🎟️ Kelola Voucher</h2>
            <div class="hint">Buat, edit (popup), toggle aktif/nonaktif—tanpa mengganggu sistem yang sudah jalan.</div>
        </div>
        <div class="toolbar d-flex">
            <a href="#formVoucher" class="btn btn-merah">
                <i class="material-icons">add</i> Voucher Baru
            </a>
            <a href="/admin/voucher" class="btn btn-outline-merah">
                <i class="material-icons">refresh</i> Muat Ulang
            </a>
        </div>
    </div>

    <?php if(session()->getFlashdata('msg')): ?>
    <div class="alert alert-success border-0 shadow-sm"><?= session()->getFlashdata('msg'); ?></div>
    <?php endif; ?>

    <div class="row g-3">
        <!-- Form Tambah -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm" id="formVoucher">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Tambah Voucher Baru</h5>
                        <div class="hint">Field kompatibel dengan controller & model kamu.</div>
                    </div>
                    <span class="badge badge-chip">Realtime</span>
                </div>
                <div class="card-body">
                    <form method="post" action="/admin/voucher/add" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Voucher</label>
                            <input type="text" name="kode" class="form-control" placeholder="WELCOME5" required>
                            <div class="hint mt-1">Contoh: WELCOME5, EOFY10, NEWUSER50K</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Voucher</label>
                            <input type="text" name="nama" class="form-control" placeholder="Voucher Member Baru">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" class="form-control"
                                placeholder="Voucher untuk member baru">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipe</label>
                            <select name="tipe" class="form-select">
                                <option value="persen">Persen (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai</label>
                            <input type="number" name="nilai" class="form-control" placeholder="5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimal Belanja (Rp)</label>
                            <input type="number" name="minimal_belanja" class="form-control" placeholder="0">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Maks. Pakai (Global)</label>
                            <input type="number" name="max_pakai" class="form-control" placeholder="0 = tanpa batas">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mulai</label>
                            <input type="date" name="mulai" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berakhir</label>
                            <input type="date" name="berakhir" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Target Pengguna</label>
                            <select name="target" class="form-select">
                                <option value="semua">Semua</option>
                                <option value="baru">Member Baru</option>
                                <option value="lama">Member Lama</option>
                                <option value="spesifik">Spesifik (custom)</option>
                            </select>
                            <div class="hint mt-1">Pilih “Member Baru” untuk voucher sambutan.</div>
                        </div>

                        <div class="col-md-6 d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="auto_apply" value="1"
                                    id="autoApply">
                                <label class="form-check-label" for="autoApply">Auto Apply</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sekali_pakai_per_user" value="1"
                                    id="onceUser">
                                <label class="form-check-label" for="onceUser">Sekali per user</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aktif" value="1" id="aktif"
                                    checked>
                                <label class="form-check-label" for="aktif">Aktif</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-merah">
                                <i class="material-icons">save</i> Simpan Voucher
                            </button>
                            <button type="reset" class="btn btn-outline-merah ms-2">
                                <i class="material-icons">backspace</i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Tips -->
        <div class="col-12 col-lg-6">
            <div class="soft p-3 h-100">
                <div class="d-flex align-items-center mb-2">
                    <i class="material-icons me-2" style="color:var(--brand)">info</i>
                    <strong>Tips singkat</strong>
                </div>
                <ul class="mb-0">
                    <li><b>Auto Apply</b> otomatis muncul di checkout jika syarat terpenuhi.</li>
                    <li><b>Target = Member Baru</b> akan diberi label jelas untuk user saat memilih voucher.</li>
                    <li><b>Sekali per user</b> + <b>limit global</b> menjaga kontrol promo.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- List Voucher -->
    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Daftar Voucher</h5>
                <div class="hint">Edit cepat via popup, toggle aktif/nonaktif, atau hapus jika diperlukan.</div>
            </div>
            <!-- Search mini -->
            <form method="get" action="/admin/voucher" class="position-relative" style="min-width:260px;">
                <i class="material-icons search-icon">search</i>
                <input type="text" name="q" value="<?= esc(service('request')->getGet('q') ?? '') ?>"
                    class="form-control search-input" placeholder="Cari kode/nama voucher...">
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        <th>Target</th>
                        <th>Auto</th>
                        <th>Aktif</th>
                        <th>Limit</th>
                        <th style="width: 210px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($voucher) && count($voucher) > 0): ?>
                    <?php foreach($voucher as $v): ?>
                    <tr class="text-center" data-row-id="<?= $v['id']; ?>">
                        <td class="text-muted"><?= $v['id']; ?></td>
                        <td><span class="pill"><?= esc($v['kode']); ?></span></td>
                        <td class="text-start">
                            <div class="fw-semibold"><?= esc($v['nama']); ?></div>
                            <?php if(!empty($v['deskripsi'])): ?>
                            <div class="hint"><?= esc($v['deskripsi']); ?></div>
                            <?php endif; ?>
                            <?php if(($v['target'] ?? '') === 'baru'): ?>
                            <span class="badge" style="background:var(--brand-weak); color:var(--brand)">Voucher Member
                                Baru</span>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst($v['tipe']); ?></td>
                        <td>
                            <?php if($v['tipe'] === 'persen'): ?>
                            <?= (float)$v['nilai']; ?>%
                            <?php else: ?>
                            Rp <?= number_format((float)$v['nilai'], 0, ',', '.'); ?>
                            <?php endif; ?>
                        </td>
                        <td><?= ucfirst($v['target']); ?></td>
                        <td><?= !empty($v['auto_apply']) ? '✅' : '❌'; ?></td>
                        <td>
                            <!-- Toggle Aktif/Nonaktif -->
                            <label class="switch" title="Aktifkan / Nonaktifkan">
                                <input type="checkbox" class="toggle-aktif" data-id="<?= $v['id']; ?>"
                                    <?= !empty($v['aktif']) ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td><?= ((int)$v['max_pakai']>0)? (int)$v['max_pakai'] : '∞'; ?></td>
                        <td class="text-nowrap">
                            <!-- EDIT => Modal -->
                            <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-chip btn-edit-voucher"
                                data-bs-toggle="modal" data-bs-target="#editVoucherModal" data-id="<?= $v['id']; ?>"
                                data-kode="<?= esc($v['kode'], 'attr'); ?>" data-nama="<?= esc($v['nama'], 'attr'); ?>"
                                data-deskripsi="<?= esc($v['deskripsi'] ?? '', 'attr'); ?>"
                                data-tipe="<?= esc($v['tipe'], 'attr'); ?>"
                                data-nilai="<?= esc($v['nilai'], 'attr'); ?>"
                                data-minimal_belanja="<?= esc($v['minimal_belanja'] ?? 0, 'attr'); ?>"
                                data-max_pakai="<?= esc($v['max_pakai'] ?? 0, 'attr'); ?>"
                                data-mulai="<?= esc($v['mulai'] ?? '', 'attr'); ?>"
                                data-berakhir="<?= esc($v['berakhir'] ?? '', 'attr'); ?>"
                                data-target="<?= esc($v['target'] ?? 'semua', 'attr'); ?>"
                                data-auto_apply="<?= !empty($v['auto_apply']) ? '1' : '0'; ?>"
                                data-sekali_pakai_per_user="<?= !empty($v['sekali_pakai_per_user']) ? '1' : '0'; ?>"
                                data-aktif="<?= !empty($v['aktif']) ? '1' : '0'; ?>">
                                Edit
                            </button>

                            <a href="/admin/voucher/delete/<?= $v['id']; ?>"
                                class="btn btn-sm btn-outline-danger btn-chip"
                                onclick="return confirm('Hapus voucher <?= esc($v['kode']); ?>?')">
                                Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Belum ada voucher. Tambahkan lewat formulir
                            di atas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== Modal Edit Voucher (Popup) ===== -->
<div class="modal fade modal-modern" id="editVoucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form method="post" id="formEditVoucher" action="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Voucher</label>
                            <input type="text" name="kode" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Voucher</label>
                            <input type="text" name="nama" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" name="deskripsi" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipe</label>
                            <select name="tipe" class="form-select">
                                <option value="persen">Persen (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nilai</label>
                            <input type="number" name="nilai" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimal Belanja (Rp)</label>
                            <input type="number" name="minimal_belanja" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Maks. Pakai (Global)</label>
                            <input type="number" name="max_pakai" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mulai</label>
                            <input type="date" name="mulai" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Berakhir</label>
                            <input type="date" name="berakhir" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Target Pengguna</label>
                            <select name="target" class="form-select">
                                <option value="semua">Semua</option>
                                <option value="baru">Member Baru</option>
                                <option value="lama">Member Lama</option>
                                <option value="spesifik">Spesifik (custom)</option>
                            </select>
                        </div>

                        <div class="col-md-6 d-flex flex-wrap gap-3 align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="auto_apply" value="1"
                                    id="edit_auto_apply">
                                <label class="form-check-label" for="edit_auto_apply">Auto Apply</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sekali_pakai_per_user" value="1"
                                    id="edit_once">
                                <label class="form-check-label" for="edit_once">Sekali per user</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aktif" value="1" id="edit_aktif">
                                <label class="form-check-label" for="edit_aktif">Aktif</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-merah" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-merah">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===== Inline Script (layout admin/template tidak punya section scripts) ===== -->
<script>
(() => {
    // (Opsional) kalau kamu pakai CSRF, taruh <meta name="csrf-token" content="<?= csrf_hash() ?>"> di admin/template.php
    const getCsrf = () => {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    };

    // ====== Isi Modal Edit dari tombol ======
    const form = document.getElementById('formEditVoucher');
    document.querySelectorAll('.btn-edit-voucher').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            form.action = '/admin/voucher/edit/' + id;

            form.querySelector('[name="kode"]').value = btn.getAttribute('data-kode') || '';
            form.querySelector('[name="nama"]').value = btn.getAttribute('data-nama') || '';
            form.querySelector('[name="deskripsi"]').value = btn.getAttribute('data-deskripsi') ||
                '';
            form.querySelector('[name="tipe"]').value = btn.getAttribute('data-tipe') || 'persen';
            form.querySelector('[name="nilai"]').value = btn.getAttribute('data-nilai') || 0;
            form.querySelector('[name="minimal_belanja"]').value = btn.getAttribute(
                'data-minimal_belanja') || 0;
            form.querySelector('[name="max_pakai"]').value = btn.getAttribute('data-max_pakai') ||
            0;

            const mulai = (btn.getAttribute('data-mulai') || '').substring(0, 10);
            const akhir = (btn.getAttribute('data-berakhir') || '').substring(0, 10);
            form.querySelector('[name="mulai"]').value = mulai;
            form.querySelector('[name="berakhir"]').value = akhir;

            form.querySelector('[name="target"]').value = btn.getAttribute('data-target') ||
            'semua';

            form.querySelector('#edit_auto_apply').checked = btn.getAttribute('data-auto_apply') ===
                '1';
            form.querySelector('#edit_once').checked = btn.getAttribute(
                'data-sekali_pakai_per_user') === '1';
            form.querySelector('#edit_aktif').checked = btn.getAttribute('data-aktif') === '1';
        });
    });

    // ====== Toggle Aktif / Nonaktif (AJAX) ======
    document.querySelectorAll('.toggle-aktif').forEach(chk => {
        chk.addEventListener('change', async (e) => {
            const id = e.target.getAttribute('data-id');
            const aktif = e.target.checked ? 1 : 0;
            const body = new URLSearchParams();
            body.append('aktif', aktif);

            try {
                const res = await fetch(`/admin/voucher/toggle/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': getCsrf()
                    },
                    body: body.toString()
                });
                const json = await res.json();
                if (!json.success) throw new Error(json.message || 'Gagal update status');

                // gunakan helper notif dari layout kamu
                if (typeof callNotif === 'function') callNotif(
                    `Voucher #${id} ${aktif? 'diaktifkan' : 'dinonaktifkan'}.`);
            } catch (err) {
                // rollback UI jika gagal
                e.target.checked = !aktif;
                alert(err.message || 'Gagal mengubah status voucher');
            }
        });
    });
})();
</script>

<?= $this->endSection(); ?>