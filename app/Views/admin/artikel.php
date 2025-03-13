<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
    th {
        color: #9098a9;
        font-size: small;
        letter-spacing: -1px;
    }

    tr>* {
        padding-block: 5px;
    }
</style>
<div style="padding: 2em;">
    <div class="d-flex justify-content-between mb-2">
        <div class="d-flex justify-content-between w-100">
            <h1 class="teks-sedang">Artikel Ilena</h1>
            <a href="/admin/addarticle" class="btn-default-merah">Tambah</a>
        </div>
    </div>
    <?php if ($msg) { ?>
        <div class="pemberitahuan my-1 w-100 mb-2" style="width: fit-content;" role="alert">
            <?= $msg; ?>
        </div>
    <?php } ?>
    <div class="container-table">
        <table class="w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($artikel as $ind_a => $a) { ?>
                    <tr>
                        <td><?= $ind_a + 1; ?></td>
                        <td><?= $a['judul']; ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($a['waktu'])); ?></td>
                        <td><?= $a['penulis']; ?></td>
                        <td><?= $a['kategori']; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a class="btn-teks-aja" href="/article/<?= $a['path']; ?>"><i class="material-icons">open_in_new</i></a>
                                <a class="btn-teks-aja" href="/admin/editarticle/<?= $a['id']; ?>"><i class="material-icons">edit</i></a>
                                <div class="btn-teks-aja" onclick="triggerToast('Hapus artikel <?= $a['judul']; ?>?', '/admin/deletearticle/<?= $a['id']; ?>')"><i class="material-icons">delete</i></div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>