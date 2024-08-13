<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container">
    <div class="konten">
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Kontak Kami
                </li>
            </ol>
        </nav>
        <h1 class="text-center">Kontak Kami</h1>
        <h5 class="mb-4 text-center" style="color: var(--hijau)">Ilena Furniture</h5>
        <hr class="my-3">
        <div class="py-4 gap-4 baris-ke-kolom">
            <div style="flex:1;">
                <!-- <h5><strong>Lokasi Kami</strong></h5> -->
                <div style="border-radius:1em; overflow:hidden; height:100%;" class="mb-2">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.687866383162!2d110.32868959999999!3d-7.0459182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7061e23055c8ed%3A0xa875b119e04372d4!2sCV.Catur%20Bhakti%20Mandiri!5e0!3m2!1sen!2sid!4v1723450895314!5m2!1sen!2sid"
                        style="border:0; width:100%; height:100%;  " allowfullscreen="" loading="lazy" zoom="80"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <p class="fw-normal" style="font-size:12px; color:red;"><strong>Lokasi:</strong> Jalan Lingkar Taman Industri,
                    Jatibarang,
                    Mijen,
                    Kota
                    Semarang, Jawa Tengah</p>
            </div>
            <div style="flex:1;">
                <h5><strong>Customer Service</strong></h5>
                <p class="my-2" style="font-size:14px;">Silakan ajukan pertanyaan Anda dengan menghubungi layanan
                    customer service
                    kami di bawah ini:
                </p>
                <div class="d-flex gap-2 mb-4">
                    <div>
                        <p style="color:black; font-size: 14px; padding:0;" class="m-0">Email</p>
                        <p style="color:black; font-size: 14px; padding:0;" class="m-0">No. WhatsApp</p>
                    </div>
                    <div>
                        <a class="d-block" style="color:black; font-size: 14px; padding:0; text-decoration:none;" href="mailto:cs@ilenafurniture.com">: cs@ilenafurniture.com</a>
                        <a class="d-block" style="color:black; font-size: 14px; padding:0; text-decoration:none;" href="https://wa.me/+628112938158">: 08112938158</a>
                    </div>
                </div>

                <h5><strong>Temukan jawaban cepat sekarang</strong></h5>

                <div>
                    <a style="color:black; font-size: 14px;" href="<?= base_url('/faq#faqkedua') ?>"><strong>Apa metode
                            pembayaran yang
                            bisa
                            digunakan?</strong></a>
                </div>
                <div>
                    <a style="color:black; font-size: 14px;" href="<?= base_url('/faq#faqpertama') ?>"><strong>Bagaimana
                            cara melakukan
                            pemesanan & pembelian di website
                            Ilena?</strong></a>
                </div>
                <div>
                    <a href="<?= base_url('/faq') ?>" style="color:red; text-decoration:none;">Lihat Semua FAQ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>