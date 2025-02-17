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
                        <input type="file" name="image1" class="form-control mb-2">
                        <small class="form-text text-muted">Upload gambar untuk desktop.</small>
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/1" id="image1-hp" alt="Image 1 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image1-hp" class="form-control mb-2">
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
                        <input type="file" name="image2" class="form-control mb-2">
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/2" id="image2-hp" alt="Image 2 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image2-hp" class="form-control mb-2">
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
                        <input type="file" name="image3" class="form-control mb-2">
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/3" id="image3-hp" alt="Image 3 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image3-hp" class="form-control mb-2">
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
                        <input type="file" name="image4" class="form-control mb-2">
                    </div>
                    <div class="col-md-5">
                        <img src="/imgheaderhp/4" id="image4-hp" alt="Image 4 (Mobile)"
                            class="img-fluid img-preview mb-2">
                        <input type="file" name="image4-hp" class="form-control mb-2">
                    </div>
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
        gambarnya.src = blobUrl;
    })
})
</script>
<?= $this->endSection(); ?>