<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
h5 {
    position: sticky;
    top: 0;
}

.card-header {
    background-color: #dc3545;
    color: #fff;
}

.form-group label {
    font-weight: bold;
}

.form-control {
    border-radius: .25rem;
}

.btn-submit {
    background-color: #28a745;
    color: white;
    font-weight: bold;
}

.btn-submit:hover {
    background-color: #218838;
}

.img-preview {
    max-height: 200px;
    object-fit: contain;
}
</style>

<div style="padding: 2em;">
    <h1 class="teks-sedang m-0">Home Layout</h1>
    <p class="mb-3">Pengaturan layout pada home page</p>
    <?php if ($msg) { ?>
    <div class="alert alert-success" role="alert">
        <?= $msg; ?>
    </div>
    <?php } ?>
    <hr>

    <form method="post" action="/admin/homelayout" enctype="multipart/form-data">
        <?= csrf_field(); ?>
        <div class="card">
            <div class="card-header text-center">
                <h5>Header Slider Atas</h5>
            </div>
            <div class="card-body">

                <!-- Gambar 1 -->
                <div class="form-group py-4 d-flex flex-column">
                    <label for="url1">URL Gambar 1 (Opsional)</label>
                    <small class="form-text text-muted">Masukkan URL yang akan dituju ketika gambar ini diklik.<br>
                        Contoh:
                        <code>https://ilenafurniture.com/blablabla</code>
                        <input type="text" name="url1" class="form-control" value="<?= $gambarHeader['url1'] ?>"
                            placeholder="https://ilenafurniture.com/product?koleksi=industrial&jenis=coffee-table">
                    </small>
                </div>
                <div class="form-group row">
                    <label for="image1" class="col-md-2 col-form-label">Gambar 1</label>
                    <div class="col-md-5">
                        <img src="/imgheader/1" id="image1" alt="Image 1" class="img-fluid img-preview mb-2">
                        <input type="file" name="image1" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                        <small class="form-text text-muted">Upload gambar untuk desktop.</small>
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/1" id="image1-hp" alt="Image 1 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image1-hp" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                        <small class="form-text text-muted">Upload gambar untuk tampilan mobile.</small>
                    </div>
                </div>


                <!-- Gambar 2 -->
                <div class="form-group py-4 d-flex flex-column">
                    <label for="url2">URL Gambar 2 (Opsional)</label>
                    <small class="form-text text-muted">Masukkan URL yang akan dituju ketika gambar ini diklik.<br>
                        Contoh:
                        <code>https://ilenafurniture.com/blablabla</code>
                        <input type="text" name="url2" class="form-control" value="<?= $gambarHeader['url2'] ?>"
                            placeholder="https://ilenafurniture.com/register">
                    </small>
                </div>
                <div class="form-group row">
                    <label for="image2" class="col-md-2 col-form-label">Gambar 2</label>
                    <div class="col-md-5">
                        <img src="/imgheader/2" id="image2" alt="Image 2" class="img-fluid img-preview mb-2">
                        <input type="file" name="image2" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/2" id="image2-hp" alt="Image 2 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image2-hp" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                </div>

                <!-- Gambar 3 -->
                <div class="form-group py-4 d-flex flex-column">
                    <label for="url3">URL Gambar 3 (Opsional)</label>
                    <small class="form-text text-muted">Masukkan URL yang akan dituju ketika gambar ini diklik.<br>
                        Contoh:
                        <code>https://ilenafurniture.com/blablabla</code>
                        <input type="text" name="url3" class="form-control" value="<?= $gambarHeader['url3'] ?>"
                            placeholder="https://ilenafurniture.com/product">
                    </small>
                </div>
                <div class="form-group row">
                    <label for="image3" class="col-md-2 col-form-label">Gambar 3</label>
                    <div class="col-md-5">
                        <img src="/imgheader/3" id="image3" alt="Image 3" class="img-fluid img-preview mb-2">
                        <input type="file" name="image3" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/3" id="image3-hp" alt="Image 3 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image3-hp" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                </div>


                <!-- Gambar 4 -->
                <div class="form-group py-4 d-flex flex-column">
                    <label for="url4">URL Gambar 4 (Opsional)</label>
                    <small class="form-text text-muted">Masukkan URL yang akan dituju ketika gambar ini diklik.<br>
                        Contoh:
                        <code>https://ilenafurniture.com/blablabla</code><input type="text" name="url4"
                            value="<?= $gambarHeader['url4'] ?>" class="form-control"
                            placeholder="https://ilenafurniture.com">
                    </small>
                </div>
                <div class="form-group row">
                    <label for="image4" class="col-md-2 col-form-label">Gambar 4</label>
                    <div class="col-md-5">
                        <img src="/imgheader/4" id="image4" alt="Image 4" class="img-fluid img-preview mb-2">
                        <input type="file" name="image4" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/4" id="image4-hp" alt="Image 4 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image4-hp" class="form-control mb-2" accept="image/jpeg,image/png,image/webp,image/avif">
                    </div>
                </div>

            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header text-center">
                <h5>Foto & Latar Home</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Unggah satu gambar per penyimpanan. File sumber maksimal 8 MB, 6000 × 6000 px, dan 25 MP; server mengubahnya menjadi WebP maksimal 1600 px dan 900 KB, lalu mengirimkannya ke R2/CDN. Jika R2 belum terkonfigurasi, gambar lama tetap aktif dan penyimpanan ditolak.</p>
                <div class="row g-3">
                    <?php foreach (($homeAssetCatalog ?? []) as $filename => $label): ?>
                    <?php $previewUrl = $homeAssetUrls[$filename] ?? base_url('home-image/' . rawurlencode($filename)); ?>
                    <?php $assetStorage = $homeAssetRecords[$filename]['storage'] ?? 'API lama (belum dimigrasikan)'; ?>
                    <?php $inputId = 'home-asset-' . pathinfo($filename, PATHINFO_FILENAME); ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="border rounded p-3 h-100">
                            <label class="form-label fw-bold" for="<?= esc($inputId, 'attr') ?>"><?= esc($label) ?></label>
                            <img src="<?= esc($previewUrl, 'attr') ?>" id="<?= esc($inputId . '-preview', 'attr') ?>" alt="<?= esc($label, 'attr') ?>" class="img-preview d-block w-100 mb-2" loading="lazy" decoding="async">
                            <small class="d-block text-muted mb-2"><?= esc($filename) ?></small>
                            <small class="d-block mb-2">Storage: <?= esc($assetStorage === 'r2' ? 'R2/CDN' : ($assetStorage === 'local' ? 'Berkas statis lokal' : $assetStorage)) ?></small>
                            <input class="form-control" type="file" id="<?= esc($inputId, 'attr') ?>"
                                name="home_asset_<?= esc(pathinfo($filename, PATHINFO_FILENAME), 'attr') ?>"
                                accept="image/jpeg,image/png,image/webp,image/avif"
                                data-home-preview="<?= esc($inputId . '-preview', 'attr') ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-2">
            <button type="submit" class="btn btn-submit">Simpan</button>
        </div>
    </form>
</div>

<script>
const inputFileElm = document.querySelectorAll('input[type="file"]');
inputFileElm.forEach((element) => {
    element.addEventListener('change', (e) => {
        console.log(e.srcElement.name)
        const gambarnya = document.getElementById(e.srcElement.name)
        const file = element.files[0];
        const blobFile = new Blob([file], {
            type: file.type
        });
        var blobUrl = URL.createObjectURL(blobFile);
        if (gambarnya) gambarnya.src = blobUrl;
    })
})

document.querySelectorAll('input[data-home-preview]').forEach((input) => {
    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        const preview = document.getElementById(input.dataset.homePreview);
        if (file && preview) preview.src = URL.createObjectURL(file);
    });
});
</script>
<?= $this->endSection(); ?>
