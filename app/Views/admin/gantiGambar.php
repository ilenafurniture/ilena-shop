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
    <h1>Perbaikan File Gambar Produk</h1>
    <p class="mb-3" style="max-width: 760px; color: #6b7280;">
        Tool ini hanya memperbaiki file gambar fisik yang hilang di folder <b>img/barang</b>.
        Data gambar lama di database tidak ditimpa, jadi aman untuk dipakai saat ada gambar produk yang tidak tampil.
    </p>
    <div class="d-flex gap-1">
        <button class="btn-default mb-3" id="btn-ganti-lokasi">Mulai Perbaiki File Gambar</button>
    </div>
    <?php foreach ($barang as $b) { ?>
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
const loadingElm = document.querySelectorAll('.loading');
const barang = JSON.parse('<?= $barangJson; ?>');
console.log(barang)
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
            } else {
                loadingElm[i].classList.remove('muter')
                loadingElm[i].innerHTML = 'error'
                loadingElm[i].style.color = 'var(--merah)'
            }
        }
        statusProsesBroadcastElm.classList.remove('d-none');
        btnGantiLokasiElm.innerHTML = 'Selesai';
    }
    gantiSkuyyyy();
})
</script>
<?= $this->endSection(); ?>
