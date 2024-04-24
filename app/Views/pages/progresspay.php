<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<h1 class="teks-besar text-center mb-3 mt-4">Menunggu Pembayaran</h1>
<div class="py-1 text-light w-100 text-center" style="background-color: var(--dark); letter-spacing: -1px;">ID
    Pesanan :
    <b><?= $pemesanan['id_midtrans']; ?></b>
</div>
<div class="konten mx-auto" style="width: calc(100% - 500px)">
    <div class="baris-ke-kolom justify-content-between mb-2 W-100 mb-3 border-bottom pb-3">
        <div>
            <p class="m-0">Jumlah Tagihan</p>
            <div class="d-flex align-items-end gap-2">
                <h3 class="m-0" style="font-size: 40px; letter-spacing: -3px; font-weight:600;">Rp <?= number_format($dataMid['gross_amount'], 0, ',', '.'); ?></h3>
                <button class="btn-teks-aja hitam mb-1" onclick="copytext('<?= (int)$dataMid['gross_amount']; ?>')"><i class="material-icons">content_copy</i></button>
            </div>
        </div>
        <div class="d-flex flex-column align-items-end">
            <p class="m-0">Waktu Pembayaran</p>
            <h3 class="m-0" style="font-size: 40px; letter-spacing: -3px; font-weight:600;" id="waktu"><?= $waktu; ?></h3>
            <p class="m-0">Selesaikan pembayaran Anda sebelum</p>
            <p class="m-0"><?= $waktuExpire; ?> WIB</p>
        </div>
    </div>
    <img class="mb-2" src="/img/pembayaran/<?= $bank; ?>.png" alt="">
    <div>
        <p class="m-0">Nomor Virtual Account</p>
        <div class="d-flex align-items-end gap-2">
            <h3 class="m-0" style="font-size: 40px; letter-spacing: -3px; font-weight:600;"><?= $va_number; ?></h3>
            <button class="btn-teks-aja hitam mb-1" onclick="copytext('<?= $va_number; ?>')"><i class="material-icons">content_copy</i></button>
        </div>
    </div>
    <p class="mb-3">Simpan nomor virtual account diatas untuk melakukan pembayaran sesuai bank yang telah dipilih</p>
    <p class="text-center" style="font-size: 20px; letter-spacing: -1px; font-weight:600;">Petunjuk Pembayaran</p>
    <div class="accordion accordion-flush" id="accordionFlushExample">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse1" aria-expanded="false" aria-controls="flush-collapse1">
                    ATM Mandiri
                </button>
            </h2>
            <div id="flush-collapse1" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <p class="mb-0 fw-bold">Bagaimana cara mengetahui barang ready?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Ketersediaan stok bisa langsung dengan cara mengecek pada kolom kuantitas di
                                spesifikasi produk. Apabila telah berhasil melakukan checkout, dapat
                                dipastikan ketersediaan produk tersebut untuk Anda.
                            </p>
                        </li>
                    </ul>
                    <p class="mb-0 mt-1 fw-bold">Bagaimana cara melihat promosi terupdate?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Promo produk yang lagi diadakan selalu tersedia dan dapat dilihat pada
                                website Kami. Selain itu, promosi juga selalu Kami update di akun sosial
                                media dan juga katalog yang Kami beri di WhatsApp serta email saat Anda
                                berlangganan dengan layanan email dan WhatsApp Kami.
                            </p>
                        </li>
                    </ul>
                    <p class="mb-0 mt-1 fw-bold">Darimana pengiriman produk Jasmine Furniture?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Pengiriman produk Jasmine Furniture berasal dari Kota Semarang, Jawa Tengah
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse2" aria-expanded="false" aria-controls="flush-collapse2">
                    Mobile Banking Mandiri
                </button>
            </h2>
            <div id="flush-collapse2" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <p class="mb-0 fw-bold">Bagaimana cara mengetahui barang ready?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Ketersediaan stok bisa langsung dengan cara mengecek pada kolom kuantitas di
                                spesifikasi produk. Apabila telah berhasil melakukan checkout, dapat
                                dipastikan ketersediaan produk tersebut untuk Anda.
                            </p>
                        </li>
                    </ul>
                    <p class="mb-0 mt-1 fw-bold">Bagaimana cara melihat promosi terupdate?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Promo produk yang lagi diadakan selalu tersedia dan dapat dilihat pada
                                website Kami. Selain itu, promosi juga selalu Kami update di akun sosial
                                media dan juga katalog yang Kami beri di WhatsApp serta email saat Anda
                                berlangganan dengan layanan email dan WhatsApp Kami.
                            </p>
                        </li>
                    </ul>
                    <p class="mb-0 mt-1 fw-bold">Darimana pengiriman produk Jasmine Furniture?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Pengiriman produk Jasmine Furniture berasal dari Kota Semarang, Jawa Tengah
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse3" aria-expanded="false" aria-controls="flush-collapse3">
                    Teller Mandiri
                </button>
            </h2>
            <div id="flush-collapse3" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                <div class="accordion-body">
                    <p class="mb-0 fw-bold">Bagaimana cara mengetahui barang ready?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Ketersediaan stok bisa langsung dengan cara mengecek pada kolom kuantitas di
                                spesifikasi produk. Apabila telah berhasil melakukan checkout, dapat
                                dipastikan ketersediaan produk tersebut untuk Anda.
                            </p>
                        </li>
                    </ul>
                    <p class="mb-0 mt-1 fw-bold">Bagaimana cara melihat promosi terupdate?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Promo produk yang lagi diadakan selalu tersedia dan dapat dilihat pada
                                website Kami. Selain itu, promosi juga selalu Kami update di akun sosial
                                media dan juga katalog yang Kami beri di WhatsApp serta email saat Anda
                                berlangganan dengan layanan email dan WhatsApp Kami.
                            </p>
                        </li>
                    </ul>
                    <p class="mb-0 mt-1 fw-bold">Darimana pengiriman produk Jasmine Furniture?</p>
                    <ul>
                        <li>
                            <p class="mb-0">
                                Pengiriman produk Jasmine Furniture berasal dari Kota Semarang, Jawa Tengah
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="w-100 d-flex justify-content-center mt-4">
        <a href="" class="btn-default-merah">Saya telah membayar</a>
    </div>
</div>
<script>
    const expiryTimeElm = document.getElementById("waktu");
    const de = new Date('<?= $dataMid['expiry_time']; ?>');
    const expireTime = de.getTime();
    const dc = new Date();

    setInterval(() => {
        const currTime = new Date().getTime();
        let dselisih = expireTime - currTime;

        const hours = String(Math.floor(dselisih / (1000 * 60 * 60))).padStart(2, '0');
        dselisih %= (1000 * 60 * 60);
        const minutes = String(Math.floor(dselisih / (1000 * 60))).padStart(2, '0');
        dselisih %= (1000 * 60);
        const seconds = String(Math.floor(dselisih / 1000)).padStart(2, '0');

        expiryTimeElm.innerHTML = `${hours}: ${minutes}: ${seconds}`;
    }, 1000);

    function copytext(teks) {
        navigator.clipboard.writeText(teks);
    }
</script>
<?= $this->endSection(); ?>