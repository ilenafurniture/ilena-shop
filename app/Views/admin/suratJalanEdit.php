<!-- app/Views/admin/suratJalanEdit.php -->
<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<style>
:root {
    --merah: #b31217;
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --slate-700: #334155;
    --ring: rgba(255, 180, 180, .35);
}

.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
    box-shadow: 0 10px 26px rgba(2, 8, 23, .06);
}

.card-head {
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    letter-spacing: -.2px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 14px;
}

.form-control,
.form-select,
input {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    background: #fff;
    font-weight: 600;
    font-size: 13px;
    transition: border-color .15s, box-shadow .15s;
}

.form-control:focus,
.form-select:focus,
input:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none;
}

.helper {
    position: relative;
    display: block;
    margin: 8px 0 0;
    padding: 8px 12px 8px 36px;
    font-size: 12px;
    line-height: 1.35;
    letter-spacing: -.2px;
    background: #e8f4ff;
    color: #1e4e79;
    border: 1px solid #cfe7ff;
    border-radius: 10px;
}

.helper:before {
    content: "info";
    font-family: "Material Icons";
    font-size: 18px;
    line-height: 1;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: .9;
}

.helper.warn {
    background: #ffe8e8;
    border-color: #ffc9c9;
    color: #8b2a2b;
}

.helper.warn:before {
    content: "priority_high";
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #111827;
    font-weight: 900;
}

.badge.red {
    color: #b42318;
    background: #feeaea;
    border-color: #ffd3cf;
}

.badge.ok {
    color: #065f46;
    background: #e7f8ef;
    border-color: #c9f0dc;
}

.table-wrap {
    border: 1px solid var(--slate-100);
    border-radius: 12px;
    overflow: hidden;
}

.tbl {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13px;
}

.tbl thead th {
    background: var(--slate-50);
    border-bottom: 1px solid var(--slate-100);
    padding: 10px 12px;
    text-align: left;
    position: sticky;
    top: 0;
    z-index: 1;
}

.tbl tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--slate-100);
    vertical-align: middle;
}

.tbl tbody tr:hover td {
    background: #fafafa;
}

.cell-right {
    text-align: right;
}

.cell-center {
    text-align: center;
}

.mono {
    font-variant-numeric: tabular-nums;
}

.btn-primary {
    background: linear-gradient(180deg, var(--merah), #a50e12);
    color: #fff;
    border: 0;
    padding: .8em 1.2em;
    border-radius: 10px;
    font-weight: 900;
    box-shadow: 0 10px 28px rgba(165, 14, 18, .25);
}

.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .7em 1em;
    border-radius: 10px;
    font-weight: 900;
}

.btn-ghost:hover {
    background: #e5e7eb;
}
</style>

<div style="padding:2em;">
    <div class="d-flex justify-content-between align-items-center mb-3" style="gap:12px;">
        <div>
            <h1 style="margin:0;font-weight:900;letter-spacing:-.3px;">Edit Surat Jalan</h1>
            <div style="color:#64748b;font-size:13px;font-weight:700;">
                ID Pesanan: <span class="mono"><?= esc($sj['id_pesanan'] ?? '-') ?></span>
                &nbsp;•&nbsp; Status: <b><?= esc(strtoupper($sj['status'] ?? '-')) ?></b>
            </div>
        </div>

        <div class="d-flex" style="gap:10px; flex-wrap:wrap; justify-content:flex-end;">
            <span class="badge <?= ($isInteriorSj ?? false) ? 'red' : 'ok' ?>">
                <i class="material-icons"
                    style="font-size:16px;"><?= ($isInteriorSj ?? false) ? 'home' : 'inventory_2' ?></i>
                <?= ($isInteriorSj ?? false) ? 'INTERIOR' : 'OFFLINE' ?>
            </span>

            <a href="<?= base_url('/admin/surat-jalan/offline') ?>" class="btn-ghost"
                style="text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                <i class="material-icons" style="font-size:18px;">arrow_back</i> Kembali
            </a>
        </div>
    </div>

    <?php if (!empty($msg)) : ?>
    <div class="helper info" style="margin-top:0;"><?= esc($msg) ?></div>
    <?php endif; ?>

    <div class="row g-3 mt-1">
        <!-- LEFT: Info + form -->
        <div class="col-lg-8">
            <div class="card-soft mb-3">
                <div class="card-head">
                    <div class="card-title">
                        <i class="material-icons" style="font-size:18px;color:#64748b;">description</i>
                        Info Dokumen
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="muted" style="font-size:12px;color:#64748b;font-weight:800;">Penerima</div>
                            <div style="font-weight:900;"><?= esc($pemesanan['nama'] ?? '-') ?></div>
                            <div class="mono" style="color:#64748b;font-size:12px;font-weight:800;">
                                <?= esc($pemesanan['nohp'] ?? '-') ?></div>
                        </div>

                        <div class="col-md-6">
                            <div class="muted" style="font-size:12px;color:#64748b;font-weight:800;">Tanggal Surat Jalan
                            </div>
                            <?php
                $tanggalVal = '';
                if (!empty($sj['tanggal'])) {
                  // input datetime-local butuh format: YYYY-MM-DDTHH:MM
                  $tanggalVal = date('Y-m-d\TH:i', strtotime($sj['tanggal']));
                }
              ?>
                            <form id="sjForm" method="post"
                                action="<?= base_url('/admin/surat-jalan/offline/'.$sj['id'].'/edit-save') ?>">
                                <?= csrf_field() ?>
                                <input type="datetime-local" name="tanggal" class="form-control"
                                    value="<?= esc($tanggalVal) ?>">
                                <div class="helper info">
                                    Edit qty lalu klik <b>Simpan</b>. Validasi sisa kirim akan dicek otomatis
                                    (berdasarkan SJ FINAL sebelumnya).
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-soft">
                <div class="card-head">
                    <div class="card-title">
                        <i class="material-icons" style="font-size:18px;color:#64748b;">local_shipping</i>
                        Item Surat Jalan
                    </div>

                    <div class="d-flex" style="gap:10px; flex-wrap:wrap;">
                        <?php if (($sj['status'] ?? '') === 'final'): ?>
                        <span class="badge ok"><i class="material-icons" style="font-size:16px;">lock</i> Final
                            (Read-only)</span>
                        <?php else: ?>
                        <button type="submit" form="sjForm" class="btn-primary"
                            style="display:inline-flex;align-items:center;gap:8px;">
                            <i class="material-icons" style="font-size:18px;">save</i> Simpan
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (empty($rows)): ?>
                    <div class="helper warn" style="margin-top:0;">Item SJ masih kosong.</div>
                    <?php else: ?>

                    <?php
              // OFFLINE: buat orderedMap untuk hitung allowed per item
              $orderedMap = [];
              if (!($isInteriorSj ?? false)) {
                foreach (($ordered ?? []) as $o) {
                  $key = ((int)$o['id_barang']).'||'.((string)$o['varian']);
                  $orderedMap[$key] = (int)$o['qty'];
                }
              }
            ?>

                    <div class="table-wrap" style="max-height: 65vh; overflow:auto;">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <?php if (!($isInteriorSj ?? false)): ?>
                                    <th>Produk</th>
                                    <th>Varian</th>
                                    <th class="cell-center">Dipesan</th>
                                    <th class="cell-center">Sudah Dikirim</th>
                                    <th class="cell-center">Sisa</th>
                                    <th class="cell-center" style="width:130px;">Qty Kirim (SJ ini)</th>
                                    <?php else: ?>
                                    <th>Kode Barang</th>
                                    <th>Nama</th>
                                    <th class="cell-center" style="width:130px;">Qty Kirim (SJ ini)</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($rows as $r): ?>
                                <?php
                      $itemId   = (int)($r['id'] ?? 0);
                      $qtySjIni = (int)($r['qty'] ?? 0);

                      if (!($isInteriorSj ?? false)) {
                        $idBarang = (int)($r['id_barang'] ?? 0);
                        $varian   = (string)($r['varian'] ?? '');
                        $key      = $idBarang.'||'.$varian;

                        $allowed  = (int)($orderedMap[$key] ?? 0);
                        $already  = (int)($shippedMap[$key] ?? 0);
                        $remain   = max(0, $allowed - $already);
                        $nama     = $r['barang_nama'] ?? ('ID#'.$idBarang);
                      } else {
                        $kodeBarang = (string)($r['kode_barang'] ?? '');
                        $nama       = (string)($r['nama_barang'] ?? ($r['varian'] ?? ''));
                      }
                    ?>
                                <tr>
                                    <?php if (!($isInteriorSj ?? false)): ?>
                                    <td style="font-weight:900;"><?= esc($nama) ?></td>
                                    <td class="mono"><?= esc($varian) ?></td>
                                    <td class="cell-center mono"><?= esc($allowed) ?></td>
                                    <td class="cell-center mono"><?= esc($already) ?></td>
                                    <td class="cell-center mono">
                                        <span class="badge <?= ($remain <= 0 ? 'red' : 'ok') ?>">
                                            <?= esc($remain) ?>
                                        </span>
                                    </td>
                                    <td class="cell-center">
                                        <input type="hidden" name="item_id[]" form="sjForm" value="<?= esc($itemId) ?>">
                                        <input type="number" name="qty[]" form="sjForm" class="form-control"
                                            style="text-align:center;" min="0" value="<?= esc($qtySjIni) ?>"
                                            <?= (($sj['status'] ?? '') === 'final') ? 'readonly' : '' ?>>
                                    </td>
                                    <?php else: ?>
                                    <td class="mono" style="font-weight:900;"><?= esc($kodeBarang) ?></td>
                                    <td><?= esc($nama) ?></td>
                                    <td class="cell-center">
                                        <input type="hidden" name="item_id[]" form="sjForm" value="<?= esc($itemId) ?>">
                                        <input type="number" name="qty[]" form="sjForm" class="form-control"
                                            style="text-align:center;" min="0" value="<?= esc($qtySjIni) ?>"
                                            <?= (($sj['status'] ?? '') === 'final') ? 'readonly' : '' ?>>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!($isInteriorSj ?? false)): ?>
                    <div class="helper info">
                        Kolom <b>Sisa</b> = (Qty dipesan) - (total qty dari SJ FINAL sebelumnya).
                        Jadi draft SJ lain tidak ikut mengurangi sisa.
                    </div>
                    <?php else: ?>
                    <div class="helper info">
                        Ini SJ Interior (item non-barang). Validasi sisa kirim mengikuti item project.
                    </div>
                    <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT: alamat ringkas -->
        <div class="col-lg-4">
            <div class="card-soft">
                <div class="card-head">
                    <div class="card-title">
                        <i class="material-icons" style="font-size:18px;color:#64748b;">person_pin_circle</i>
                        Alamat Pengiriman
                    </div>
                </div>
                <div class="card-body" style="font-size:13px;">
                    <div style="font-weight:900;"><?= esc($pemesanan['nama'] ?? '-') ?></div>
                    <div class="mono" style="color:#64748b; font-weight:800;"><?= esc($pemesanan['nohp'] ?? '-') ?>
                    </div>
                    <hr style="border-color:#f1f5f9;">
                    <div style="white-space:pre-wrap; color:#0f172a;">
                        <?= esc($pemesanan['alamat_pengiriman'] ?? '-') ?>
                    </div>

                    <?php if (!empty($pemesanan['alamat_tagihan'])): ?>
                    <hr style="border-color:#f1f5f9;">
                    <div style="font-weight:900;">Alamat Tagihan</div>
                    <div style="white-space:pre-wrap; color:#0f172a;">
                        <?= esc($pemesanan['alamat_tagihan']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-soft mt-3">
                <div class="card-head">
                    <div class="card-title">
                        <i class="material-icons" style="font-size:18px;color:#64748b;">tips_and_updates</i>
                        Catatan
                    </div>
                </div>
                <div class="card-body">
                    <div class="helper info" style="margin-top:0;">
                        Kalau qty di SJ ini kamu isi <b>0</b>, item tetap ada tapi tidak terkirim.
                        (Boleh kalau kamu mau draft dulu lalu finalize nanti.)
                    </div>
                    <?php if (($sj['status'] ?? '') === 'final'): ?>
                    <div class="helper warn">
                        SJ sudah final. Untuk koreksi qty, sebaiknya buat SJ baru (SJ ke-berikutnya).
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>