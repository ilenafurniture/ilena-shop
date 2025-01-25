<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
    h5 {
        position: sticky;
        top: 0;
    }
</style>
<div style="padding: 2em;">
    <h1 class="teks-sedang m-0">Home Layout</h1>
    <p class="mb-3">Pengaturan layout pada home page</p>
    <?php if ($msg) { ?>
        <div class="pemberitahuan my-1" role="alert">
            <?= $msg; ?>
        </div>
    <?php } ?>
    <hr>
    <form method="post" action="/admin/homelayout" enctype="multipart/form-data">
        <div class="p-2 border border-danger">
            <h5 class="text-center bg-danger text-light py-2">Header Slider Atas</h5>
            <div class="d-flex gap-1 mb-2">
                <div>
                    <img src="/imgheader/1" id="image1" alt="" class="w-100 mb-1">
                    <input type="file" name="image1" class="form-control mb-2">
                </div>
                <div>
                    <img src="/imgheaderhp/1" id="image1-hp" alt="" class="w-100 mb-1">
                    <input type="file" name="image1-hp" class="form-control mb-2">
                </div>
            </div>
            <div class="d-flex gap-1 mb-2">
                <div>
                    <img src="/imgheader/2" id="image2" alt="" class="w-100 mb-1">
                    <input type="file" name="image2" class="form-control mb-2">
                </div>
                <div>
                    <img src="/imgheaderhp/2" id="image2-hp" alt="" class="w-100 mb-1">
                    <input type="file" name="image2-hp" class="form-control mb-2">
                </div>
            </div>
            <div class="d-flex gap-1 mb-2">
                <div>
                    <img src="/imgheader/3" id="image3" alt="" class="w-100 mb-1">
                    <input type="file" name="image3" class="form-control mb-2">
                </div>
                <div>
                    <img src="/imgheaderhp/3" id="image3-hp" alt="" class="w-100 mb-1">
                    <input type="file" name="image3-hp" class="form-control mb-2">
                </div>
            </div>
            <div class="d-flex gap-1 mb-2">
                <div>
                    <img src="/imgheader/4" id="image4" alt="" class="w-100 mb-1">
                    <input type="file" name="image4" class="form-control mb-2">
                </div>
                <div>
                    <img src="/imgheaderhp/4" id="image4-hp" alt="" class="w-100 mb-1">
                    <input type="file" name="image4-hp" class="form-control mb-2">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-2">
            <button type="submit" class="btn btn-default">Simpan</button>
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