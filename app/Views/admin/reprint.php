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
            <div style="flex: 2;">tanggal</div>
            <div style="flex: 2;">Atas nama</div>
            <div style="flex: 2;">Keperluan</div>
            <div style="flex: 2;">Kendala</div>
            <div style="flex: 2;">Status</div>
            <div style="flex: 2;">Action</div>
        </div>

        <div class="isi-table">
            <div style="flex: 1;">1</div>
            <div style="flex: 2;">5 Juni 2024</div>
            <div style="flex: 2;">Udin samsudin</div>
            <div style="flex: 2;">Pengajuan Print Ulang</div>
            <div style="flex: 2;">Mesin Print Tidak bisa ngeprint</div>
            <div style="flex: 2;">Menunggu Persetujuan</div>
            <div style="flex: 2;" class="gap-2">
                <a class="btn-default" href="/editproduct/" <i class=" material-icons">Konfirmasi</i></a>
                <a class="btn-default-merah" href="/editproduct" /<i class=" material-icons">Tolak</i></a>
            </div>
        </div>

        <div class="isi-table">
            <div style="flex: 1;">2</div>
            <div style="flex: 2;">6 Juni 2024</div>
            <div style="flex: 2;">Imannuel Amarullah</div>
            <div style="flex: 2;">Pengajuan Print Ulang</div>
            <div style="flex: 2;">Mesin Print Error</div>
            <div style="flex: 2; color:red;">Di Tolak</div>
            <div style="flex: 2;">Selesai</div>
        </div>

        <div class="isi-table">
            <div style="flex: 1;">3</div>
            <div style="flex: 2;">6 Juni 2024</div>
            <div style="flex: 2;">Anjas Mabar</div>
            <div style="flex: 2;">Pengajuan Print Ulang</div>
            <div style="flex: 2;">Mesin Print Error</div>
            <div style="flex: 2; color:green;">Di Terima</div>
            <div style="flex: 2;">Selesai</div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>