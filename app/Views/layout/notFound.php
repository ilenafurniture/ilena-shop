<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<style>
img {
    height: 80%;
    width: auto;
}

@media (max-width: 500px) {
    img {
        height: auto;
        width: 80%;
    }
}
</style>
<div class="w-100 h-100 mx-auto d-flex justify-content-center align-items-center" style="position: absolute;">
    <img src="<?= base_url('img/error/Oooops.png'); ?>" alt="">
</div>

<?= $this->endSection(); ?>