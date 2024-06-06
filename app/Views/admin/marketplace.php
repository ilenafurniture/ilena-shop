<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>

<div style="padding: 2em;">
    <div class="d-flex justify-content-between">
        <div>
            <h1 class="teks-sedang">List Konfirmasi Ulang</h1>
        </div>
    </div>
    <div class="container-table mt-4">
        <div class="header-table">
            <div style="flex: 1;">No</div>
            <div style="flex: 2;">Id Marketplace</div>
            <div style="flex: 2;">Nama Pembeli</div>
            <div style="flex: 2;">Alamat Pembeli</div>
            <div style="flex: 2;">Action</div>
        </div>

        <div class="isi-table" onclick="openDetail()">
            <div style="flex: 1;">1</div>
            <div style="flex: 2;">SILN0001</div>
            <div style="flex: 2;">Udin Samsudin</div>
            <div style="flex: 2;">Jakarta,indonesia</div>
            <div style="flex: 2;">1</div>
            <div style="flex: 2;" class="gap-2">
                <a class="btn-default" href="/editproduct/" <i class=" material-icons">Konfirmasi</i></a>
                <a class="btn-default-merah" href="/editproduct" /<i class=" material-icons">Tolak</i></a>
            </div>
        </div>

        <!-- <div class="isi-table">
            <div style="flex: 1;">2</div>
            <div style="flex: 2;">TILN0001</div>
            <div style="flex: 2;">MTV 100 - Putih</div>
            <div style="flex: 2;">bali,indonesia</div>
            <div style="flex: 2;">1</div>
            <div style="flex: 2; color:green;">Selesai</div>
        </div> -->
    </div>
</div>
<div class="d-none justify-content-center align-items-center w-100" id="isi-tabel"
    style="background-color: rgba(0,0,0,0.5); position:fixed; top: 0; left: 0; width: 100vw; height: 100svh;">
    <div class="bg-white p-4" style="width: 50%; border: 0.5px solid black; border-radius: 1em; box-shadow:1em;">
        <div class=" gap-2">
            <h6 class="text-center">Detail:</h6>
            <div class="container-table">
                <div class="header-table">
                    <div style="flex: 2;">No Resi</div>
                    <div style="flex: 2;">Nama Barang</div>
                    <div style="flex: 2;">Varian Barang</div>
                    <div style="flex: 2;">Nama Pembeli</div>
                    <div style="flex: 2;">Alamat Pembeli</div>
                </div>
                <div class="isi-table">
                    <div style="flex: 2;">36365252</div>
                    <div style="flex: 2;">ALD Besi </div>
                    <div style="flex: 2;">Putih </div>
                    <div style="flex: 2;">Udin Samsudin</div>
                    <div style="flex: 2;">Jakarta,indonesia</div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center align-content-center">
            <button class="btn-default-merah" onclick="closemodal()">Batal</button>
        </div>
    </div>
</div>

<script>
const isiTabelElm = document.getElementById('isi-tabel');

function openDetail() {
    isiTabelElm.classList.add("d-flex");
    isiTabelElm.classList.remove("d-none");
}

function closemodal() {
    isiTabelElm.classList.remove("d-flex");
    isiTabelElm.classList.add("d-none");
}
</script>
<?= $this->endSection(); ?>