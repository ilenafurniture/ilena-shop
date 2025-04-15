<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
    [data-bs-toggle="tooltip"] {
        color: gray;
    }
</style>
<div style="padding: 2em;">
    <div class="mb-4 d-flex align-items-center justify-content-between gap-2">
        <div>
            <div class="d-flex gap-2">
                <h1 class="teks-sedang" style="text-wrap: nowrap;">Pesanan Offline</h1>
                <select class="form-select" onchange="selectJenis(event)">
                    <option value="sale" class="fw-bold" <?= $jenis == 'sale' ? 'selected' : ''; ?>>Sale</option>
                    <option value="display" class="fw-bold" <?= $jenis == 'display' ? 'selected' : ''; ?>>Display</option>
                </select>
            </div>
            <p style="color: grey;"><?= count($pesanan) <= 0 ? 'Tidak Ada' : count($pesanan) ?> Pesanan</p>
        </div>
        <a class="btn-default-merah" href="/admin/order-offline/add">Tambah</a>
    </div>
    <table class="w-100">
        <thead>
            <tr>
                <th>No</th>
                <th>ID Order</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pesanan as $ind_p => $p) { ?>
                <tr>
                    <td><?= $ind_p + 1; ?></td>
                    <td><?= $p['id_pesanan']; ?></td>
                    <td><?= $p['tanggal']; ?></td>
                    <td><?= $p['nama']; ?></td>
                    <td><?= $p['status']; ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <?php if ($jenis == 'sale') { ?>
                                <a href="/admin/suratjalan-offline/<?= $p['id_pesanan']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Surat Jalan"><i class="material-icons">local_shipping</i></a>
                                <a href="/invoice-offline/<?= $p['id_pesanan']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice"><i class="material-icons">description</i></a>
                            <?php } else { ?>
                                <a href="/admin/suratpengantar-offline/<?= $p['id_pesanan']; ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Surat Pengantar"><i class="material-icons">description</i></a>
                            <?php } ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
<script>
    function selectJenis(event) {
        window.location.replace(`/admin/order/offline/${event.target.value}`)
    }
</script>
<?= $this->endSection(); ?>