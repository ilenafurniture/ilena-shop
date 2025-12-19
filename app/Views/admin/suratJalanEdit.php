<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<div class="container py-3">

    <?php if (!empty($msg)): ?>
    <div class="alert alert-info"><?= esc($msg) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
        <div>
            <h5 class="mb-1">Edit Surat Jalan</h5>
            <div class="text-muted" style="font-size:12px;">
                ID Pesanan: <b><?= esc($sj['id_pesanan']) ?></b> |
                SJ Ke: <b><?= (int)$sj['sj_ke'] ?></b> |
                Status: <b><?= esc($sj['status']) ?></b>
            </div>
            <div class="text-muted" style="font-size:12px;">
                Penerima: <b><?= esc($pemesanan['nama'] ?? '-') ?></b>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm"
                href="<?= base_url('admin/order/offline/' . (($pemesanan['jenis'] ?? 'sale') === 'nf' ? 'nf' : (($pemesanan['jenis'] ?? '') === 'sp' ? 'sp' : 'sale'))) ?>">
                Kembali
            </a>

            <a class="btn btn-outline-primary btn-sm" href="<?= base_url('admin/surat-jalan/offline/' . $sj['id']) ?>">
                Preview Print
            </a>

            <?php if (($sj['status'] ?? '') !== 'final'): ?>
            <form method="post" action="<?= base_url('admin/surat-jalan/offline/' . $sj['id'] . '/finalize') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-success btn-sm" type="submit">Finalize</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <form method="post" action="<?= base_url('admin/surat-jalan/offline/' . $sj['id'] . '/edit') ?>" class="card mb-3">
        <?= csrf_field() ?>
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Tanggal SJ</label>
                    <?php
                        $tglVal = $sj['tanggal'] ?? date('Y-m-d H:i:s');
                        $tglVal = str_replace(' ', 'T', substr($tglVal, 0, 16)); // datetime-local
                    ?>
                    <input type="datetime-local" name="tanggal" class="form-control form-control-sm"
                        value="<?= esc($tglVal) ?>" <?= (($sj['status'] ?? '') === 'final') ? 'disabled' : '' ?>>
                </div>
                <div class="col-md-8 text-muted" style="font-size:12px;">
                    Tips: isi qty sesuai barang yang benar-benar dikirim. Sistem akan nolak kalau melebihi sisa.
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:55px;">No</th>
                            <th style="width:160px;">Kode</th>
                            <th>Nama</th>
                            <th style="width:170px;">Varian</th>
                            <th style="width:140px;">Qty</th>
                            <th style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; foreach ($rows as $r): ?>
                        <?php
                                $kode = !empty($r['kode_barang']) ? $r['kode_barang'] : (string)($r['id_barang'] ?? '-');
                                $nama = !empty($r['nama_barang']) ? $r['nama_barang'] : ($r['barang_nama'] ?? '-');
                                $qty  = (int)($r['qty'] ?? 0);
                            ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><span class="text-monospace"><?= esc(strtoupper($kode)) ?></span></td>
                            <td><?= esc($nama) ?></td>
                            <td><?= esc($r['varian'] ?? '') ?></td>
                            <td>
                                <input type="hidden" name="item_id[]" value="<?= (int)$r['id'] ?>">
                                <input type="number" min="0" name="qty[]" class="form-control form-control-sm"
                                    value="<?= $qty ?>" <?= (($sj['status'] ?? '') === 'final') ? 'disabled' : '' ?>>
                            </td>
                            <td class="text-center">
                                <?php if (($sj['status'] ?? '') !== 'final'): ?>
                                <form method="post"
                                    action="<?= base_url('admin/surat-jalan/offline/' . $sj['id'] . '/item/delete') ?>"
                                    onsubmit="return confirm('Hapus item ini?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="item_id" value="<?= (int)$r['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                                </form>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada item.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (($sj['status'] ?? '') !== 'final'): ?>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary btn-sm" type="submit">Simpan Perubahan</button>
            </div>
            <?php endif; ?>
        </div>
    </form>

    <?php if (($sj['status'] ?? '') !== 'final'): ?>
    <div class="card">
        <div class="card-header">
            <b>Tambah Item</b>
        </div>
        <div class="card-body">

            <?php if (!$isInteriorSj): ?>
            <!-- OFFLINE MODE -->
            <form method="post" action="<?= base_url('admin/surat-jalan/offline/' . $sj['id'] . '/item/add') ?>"
                class="row g-2">
                <?= csrf_field() ?>
                <input type="hidden" name="mode" value="offline">

                <div class="col-md-4">
                    <label class="form-label">ID Barang</label>
                    <input type="number" name="id_barang" class="form-control form-control-sm" placeholder="contoh: 12"
                        required>
                    <div class="text-muted" style="font-size:12px;">(kalau mau lebih cantik, nanti kita ganti jadi
                        dropdown cari barang)</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Varian</label>
                    <input type="text" name="varian" class="form-control form-control-sm" placeholder="contoh: walnut"
                        required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Qty</label>
                    <input type="number" min="1" name="qty" class="form-control form-control-sm" value="1">
                </div>

                <div class="col-md-2 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-outline-primary btn-sm" type="submit">Tambah</button>
                </div>
            </form>
            <?php else: ?>
            <!-- INTERIOR MODE -->
            <form method="post" action="<?= base_url('admin/surat-jalan/offline/' . $sj['id'] . '/item/add') ?>"
                class="row g-2">
                <?= csrf_field() ?>
                <input type="hidden" name="mode" value="interior">

                <div class="col-md-3">
                    <label class="form-label">Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control form-control-sm" placeholder="ILN-001"
                        required>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control form-control-sm" placeholder="Kitchen Set"
                        required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Varian</label>
                    <input type="text" name="varian" class="form-control form-control-sm" placeholder="Project A">
                </div>

                <div class="col-md-1">
                    <label class="form-label">Qty</label>
                    <input type="number" min="1" name="qty" class="form-control form-control-sm" value="1">
                </div>

                <div class="col-md-1 d-grid">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-outline-primary btn-sm" type="submit">+</button>
                </div>
            </form>
            <?php endif; ?>

        </div>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection(); ?>