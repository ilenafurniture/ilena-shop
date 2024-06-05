<?= $this->extend("gudang/layout/template"); ?>
<?= $this->section("content"); ?>
<div class="d-none justify-content-center align-items-center w-100" id="modal-add" style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white" style="width:fit-content; border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <div style="padding: 2em;" class="d-flex justify-content-center align-items-center flex-column gap-4">
            <form action="/gudang/actionaddmutasi" method="post">
                <div class="mb-5">
                    <div class="form-floating mb-1">
                        <input type="date" class="form-control" id="floatingInput" placeholder="name@example.com" name="tanggal">
                        <label for="floatingInput">Tanggal</label>
                    </div>
                    <div class="form-floating mb-1">
                        <input type="text" class="form-control" id="floatingInput" placeholder="name@example.com" name="keterangan">
                        <label for="floatingInput">Keterangan</label>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="form-floating flex-grow-1">
                            <select name="jenis" id="" class="form-select">
                                <option value="debit">Debit</option>
                                <option value="kredit">Kredit</option>
                            </select>
                            <label for="floatingInput">Jenis</label>
                        </div>
                        <div class="form-floating flex-grow-1">
                            <input type="number" class="form-control" id="floatingPassword" name="nominal">
                            <label for="floatingPassword">Nominal</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn-default">Tambah</button>
                    <button type="button" class="btn-default-merah" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between gap-4">
        <div style="flex:1">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="mb-2">Kartu Stok</h6>
                    <div>
                        <select name="barang" class="form-select">
                            <option value="id">id_barang1</option>
                            <option value="id">id_barang2</option>
                            <option value="id">namabarang1</option>
                            <option value="id">namabarang2</option>
                        </select>
                    </div>
                </div>
                <button onclick="openModal()" class="btn-default">Tambah data</button>
            </div>
            <div class="container-table">
                <div class="header-table border-buttom border-dark">
                    <div style="flex: 1;">No</div>
                    <div style="flex: 2;">Tanggal</div>
                    <div style="flex: 3;">Keterangan</div>
                    <div style="flex: 2;">Debit</div>
                    <div style="flex: 2;">Kredit</div>
                    <div style="flex: 2;">Saldo</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 1;">1</div>
                    <div style="flex: 2;">31 Mei 2024</div>
                    <div style="flex: 3;">ISI_NYA ID PESANAN</div>
                    <div style="flex: 2;">6</div>
                    <div style="flex: 2;">4</div>
                    <div style="flex: 2;">6</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 1;">2</div>
                    <div style="flex: 2;">3 Juni 2024</div>
                    <div style="flex: 3;">ISI_NYA ID PESANAN</div>
                    <div style="flex: 2;">9</div>
                    <div style="flex: 2;">5</div>
                    <div style="flex: 2;">4</div>
                </div>
            </div>
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
</script>
<?= $this->endSection(); ?>