<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<div class="p-5 gap-4 baris-ke-kolom">
    <div style="flex:1;" class="google-maps">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.687866383162!2d110.32868959999999!3d-7.0459182!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7061e23055c8ed%3A0xa875b119e04372d4!2sCV.Catur%20Bhakti%20Mandiri!5e0!3m2!1sen!2sid!4v1723450895314!5m2!1sen!2sid"
            width="600" height="450" style="border:0; border-rarius:8px;" allowfullscreen="" loading="lazy" zoom="80"
            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
    <div style="flex:1;">
        <h3 class="">Customer Servis Kami:</h3>
        <p>Email</p>
        <p>WhatsApp</p>
        <p></p>
    </div>
</div>



<?= $this->endSection(); ?>