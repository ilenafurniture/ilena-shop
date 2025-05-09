<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div class="d-none justify-content-center align-items-center w-100" id="modal-add" style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white p-4" style="width:fit-content; border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <form action="/admin/actionaddmutasi" method="post">
            <div class="form-floating mb-1">
                <input type="datetime-local" class="form-control" id="floatingInput" placeholder="name@example.com" name="tanggal">
                <label for="floatingInput">Tanggal</label>
            </div>
            <div class="form-floating mb-1">
                <select type="text" class="form-select" id="floatingInput1" placeholder="name@example.com" name="barang">
                    <?php foreach ($product as $p) {
                        foreach ($p['varian'] as $ind_v => $v) { ?>
                            <option value="<?= $p['id']; ?>-<?= strtoupper($v['nama']); ?>-<?= $ind_v; ?>" <?= $data == $p['id'] . '-' . strtoupper($v['nama']) ? 'selected' : ''; ?>>
                                <?= $p['kategori']; ?> - <?= $p['subkategori']; ?> -
                                <?= $p['deskripsi']['dimensi']['asli']['panjang']; ?>x<?= $p['deskripsi']['dimensi']['asli']['lebar']; ?>x<?= $p['deskripsi']['dimensi']['asli']['tinggi']; ?>
                                - <?= $v['nama']; ?></option>
                    <?php }
                    } ?>
                </select>
                <label for="floatingInput1">Barang</label>
            </div>
            <div class="d-flex gap-1 mb-1">
                <div class="form-floating flex-grow-1">
                    <select name="jenis" id="" class="form-select">
                        <option value="debit">Debit</option>
                        <option value="kredit">Kredit</option>
                    </select>
                    <label for="floatingInput3">Jenis</label>
                </div>
                <div class="form-floating flex-grow-1">
                    <input type="number" class="form-control" id="floatingPassword" name="nominal" required>
                    <label for="floatingPassword">Jumlah barang</label>
                </div>
            </div>
            <div class="form-floating mb-2">
                <input type="text" class="form-control" id="floatingInput3" name="alasan" required>
                <label for="floatingInput3">Alasan</label>
            </div>
            <div class="d-flex gap-1">
                <button type="submit" class="btn-default">Tambah</button>
                <button type="button" class="btn-default-merah" onclick="closeModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
<div style="padding: 2em;">
    <div class="d-flex gap-3 justify-content-between align-items-end mb-3">
        <div>
            <h6 class="mb-2">Kartu Stok</h6>
            <div>
                <select name="barang" class="form-select" onchange="gantiBarang(event)">
                    <?php foreach ($product as $p) {
                        foreach ($p['varian'] as $ind_v => $v) { ?>
                            <option value="<?= $p['id']; ?>-<?= strtoupper($v['nama']); ?>-<?= $ind_v; ?>" <?= $data == $p['id'] . '-' . strtoupper($v['nama']) ? 'selected' : ''; ?>>
                                <?= $p['kategori']; ?> - <?= $p['subkategori']; ?> -
                                <?= $p['deskripsi']['dimensi']['asli']['panjang']; ?>x<?= $p['deskripsi']['dimensi']['asli']['lebar']; ?>x<?= $p['deskripsi']['dimensi']['asli']['tinggi']; ?>
                                - <?= $v['nama']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
        </div>
        <div class="show-block-ke-hide">
            <button onclick="openModal()" class="btn-default">Tambah data</button>
        </div>
        <div class="hide-ke-show-block">
            <button onclick="openModal()" class="btn-default"><i class="material-icons">add</i></button>
        </div>
    </div>
    <?php if ($msg) { ?>
        <div class="pemberitahuan my-1" role="alert">
            <?= $msg; ?>
        </div>
    <?php } ?>
    <div class="container-table show-block-ke-hide">
        <div class="header-table border-buttom border-dark">
            <div style="flex: 1;">No</div>
            <div style="flex: 2;">Tanggal</div>
            <div style="flex: 3;">Keterangan</div>
            <div style="flex: 1;">Debit</div>
            <div style="flex: 1;">Kredit</div>
            <div style="flex: 1;">Saldo</div>
            <div style="flex: 2;">Status</div>
        </div>
        <?php
        $no = 1;
        foreach ($mutasi as $m) { ?>
            <div class="isi-table" <?= $m['pending'] ? 'style="color: var(--merah)"' : ''; ?>>
                <div style="flex: 1;"><?= $no; ?></div>
                <div style="flex: 2;"><?= $m['tanggal']; ?></div>
                <div style="flex: 3;"><?= $m['keterangan']; ?></div>
                <div style="flex: 1;"><?= $m['debit']; ?></div>
                <div style="flex: 1;"><?= $m['kredit']; ?></div>
                <div style="flex: 1;"><?= $m['pending'] ? ($m['id_pesanan'] == 'MANUALLY' ? '?' : $m['saldo']) : $m['saldo']; ?></div>
                <div style="flex: 2;"><?= $m['pending'] ? ($m['id_pesanan'] == 'MANUALLY' ? 'Menunggu konfirm' : 'Surat jalan belum print') : 'Success'; ?></div>
            </div>
        <?php $no++;
        } ?>
    </div>
    <div class="hide-ke-show-block" style="overflow-x: auto;">
        <div class="container-table" style="width: 500px;">
            <div class="header-table border-buttom border-dark">
                <div style="flex: 1;">No</div>
                <div style="flex: 2;">Tanggal</div>
                <div style="flex: 3;">Keterangan</div>
                <div style="flex: 1;">Debit</div>
                <div style="flex: 1;">Kredit</div>
                <div style="flex: 1;">Saldo</div>
                <div style="flex: 2;">Status</div>
            </div>
            <?php
            $no = 1;
            foreach ($mutasi as $m) { ?>
                <div class="isi-table" <?= $m['pending'] ? 'style="color: var(--merah)"' : ''; ?>>
                    <div style="flex: 1;"><?= $no; ?></div>
                    <div style="flex: 2;"><?= $m['tanggal']; ?></div>
                    <div style="flex: 3;"><?= $m['keterangan']; ?></div>
                    <div style="flex: 1;"><?= $m['debit']; ?></div>
                    <div style="flex: 1;"><?= $m['kredit']; ?></div>
                    <div style="flex: 1;"><?= $m['pending'] ? ($m['id_pesanan'] == 'MANUALLY' ? '?' : $m['saldo']) : $m['saldo']; ?></div>
                    <div style="flex: 2;"><?= $m['pending'] ? ($m['id_pesanan'] == 'MANUALLY' ? 'Menunggu konfirm' : 'Surat jalan belum print') : 'Success'; ?></div>
                </div>
            <?php $no++;
            } ?>
        </div>
    </div>
</div>
<script>
    const modalElm = document.getElementById('modal-add');

    function openModal(idBarang, nama) {
        modalElm.classList.add("d-flex");
        modalElm.classList.remove("d-none");
    }

    function closeModal() {
        modalElm.classList.remove("d-flex");
        modalElm.classList.add("d-none");
    }

    function gantiBarang(e) {
        const id = e.target.value
        console.log(id)
        window.location.href = '/admin/mutasi/' + id.split('-')[0] + '-' + id.split('-')[1]
    }
</script>
<?= $this->endSection(); ?>