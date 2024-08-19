<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>
<div class="container d-flex justify-content-center">
    <div class="konten">
        <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    Visi dan Misi
                </li>
            </ol>
        </nav>
        <h1 class="text-center">Visi dan Misi</h1>
        <h5 class="mb-4 text-center" style="color: var(--hijau)">Ilena Furniture</h5>
        <hr class="my-5">
    </div>
</div>
<?= $this->endSection(); ?>