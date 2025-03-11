<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
.muter {
    animation: putarputar 2s linear infinite;
}

@keyframes putarputar {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
<div style="padding: 2em;">
    <h1>Ganti Ukurang Gambar Skuyyyy</h1>
    <div class="d-flex gap-1">
        <button class="btn-default mb-3 d-none" id="btn-mulai">Mulai Ganti</button>
        <button class="btn-default mb-3" id="btn-ganti-lokasi">Mulai Ganti Lokasi</button>
    </div><?php foreach ($barang as $b) { ?>
    <div class="d-flex gap-1 align-items-center fw-bold">
        <i class="material-icons loading">panorama_fish_eye</i>
        <p class="m-0"><?= $b['nama'] ?> <?= $b['dimensi']['panjang'] ?></p>
    </div>
    <?php } ?>
    <p id="status-proses-broadcast" class="d-none m-0" style="color: var(--merah);">Proses broadcast telah selesai</p>
</div>
<script>
const statusProsesBroadcastElm = document.getElementById('status-proses-broadcast');
const btnGantiLokasiElm = document.getElementById('btn-ganti-lokasi');
const btnMulaiElm = document.getElementById('btn-mulai');
const loadingElm = document.querySelectorAll('.loading');
const barang = JSON.parse('<?= $barangJson; ?>');
console.log(barang)
btnMulaiElm.addEventListener('click', () => {
    btnMulaiElm.innerHTML = 'Proses...';
    async function gantiSkuyyyy() {
        for (let i = 0; i < barang.length; i++) {
            loadingElm[i].innerHTML = 'data_usage'
            loadingElm[i].classList.add('muter')
            const barangCur = barang[i];
            const response = await fetch(`https://ilenafurniture.com/changepic/${barangCur.id}`)
            if (response.status == 200) {
                loadingElm[i].classList.remove('muter')
                loadingElm[i].innerHTML = 'done'
            }
            // const responseJson = await response.json();
        }
        statusProsesBroadcastElm.classList.remove('d-none');
    }
    gantiSkuyyyy();
})
btnGantiLokasiElm.addEventListener('click', () => {
    btnGantiLokasiElm.innerHTML = 'Proses...';
    async function gantiSkuyyyy() {
        for (let i = 0; i < barang.length; i++) {
            loadingElm[i].innerHTML = 'data_usage'
            loadingElm[i].classList.add('muter')
            const barangCur = barang[i];
            const response = await fetch(`/gantilokasi/${barangCur.id}`)
            if (response.status == 200) {
                loadingElm[i].classList.remove('muter')
                loadingElm[i].innerHTML = 'done'
            }
            // const responseJson = await response.json();
        }
        statusProsesBroadcastElm.classList.remove('d-none');
    }
    gantiSkuyyyy();
})
</script>
<?= $this->endSection(); ?>