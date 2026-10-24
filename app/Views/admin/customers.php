<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<main class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="h3 mb-1">Akun Pelanggan</h1><p class="text-muted mb-0">Kelola status dan penangguhan akun pelanggan.</p></div>
        <form method="get" action="/admin/customers" class="d-flex gap-2">
            <input class="form-control" type="search" name="q" value="<?= esc($search); ?>" placeholder="Nama atau email" aria-label="Cari pelanggan">
            <button class="btn btn-dark" type="submit">Cari</button>
        </form>
    </div>
    <?php if (!empty($msg)): ?><div class="alert alert-success" role="status"><?= esc($msg); ?></div><?php endif; ?>
    <?php if (!empty($err)): ?><div class="alert alert-danger" role="alert"><?= esc($err); ?></div><?php endif; ?>
    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Nama / Email</th><th>Status</th><th>Penangguhan</th><th style="min-width:300px">Tindakan</th></tr></thead>
            <tbody>
            <?php foreach ($customers as $customer): ?>
                <?php $state = $customer['suspension']; ?>
                <tr>
                    <td><strong><?= esc($customer['nama'] ?: '—'); ?></strong><br><small><?= esc($customer['email']); ?></small></td>
                    <td><?= (string)$customer['active'] === '1' ? 'Aktif' : 'Belum verifikasi'; ?></td>
                    <td>
                        <?php if ($state): ?>
                            <span class="badge bg-danger">Ditangguhkan sampai <?= esc((new DateTimeImmutable($state['until'], new DateTimeZone('UTC')))->format('d M Y H:i')); ?> UTC</span>
                            <?php if ($state['reason'] !== ''): ?><div class="small text-muted mt-1"><?= esc($state['reason']); ?></div><?php endif; ?>
                        <?php else: ?><span class="badge bg-success">Tidak ditangguhkan</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($state): ?>
                            <form method="post" action="/admin/customers/reinstate" class="d-inline">
                                <?= csrf_field(); ?><input type="hidden" name="email" value="<?= esc($customer['email']); ?>">
                                <button class="btn btn-sm btn-outline-success" type="submit">Pulihkan akun</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="/admin/customers/suspend" class="d-flex flex-wrap gap-2">
                                <?= csrf_field(); ?><input type="hidden" name="email" value="<?= esc($customer['email']); ?>">
                                <select class="form-select form-select-sm" name="days" required aria-label="Durasi penangguhan">
                                    <option value="1">1 hari</option><option value="3">3 hari</option><option value="7">7 hari</option><option value="30">30 hari</option><option value="90">90 hari</option>
                                </select>
                                <input class="form-control form-control-sm" name="reason" maxlength="500" placeholder="Alasan (opsional)" aria-label="Alasan penangguhan">
                                <button class="btn btn-sm btn-danger" type="submit">Tangguhkan</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$customers): ?><tr><td colspan="4" class="text-center text-muted py-4">Tidak ada akun pelanggan pada halaman ini.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
        <nav class="mt-3" aria-label="Halaman pelanggan"><ul class="pagination">
            <?php for ($n = 1; $n <= $pages; $n++): ?>
                <li class="page-item <?= $n === $page ? 'active' : ''; ?>"><a class="page-link" href="<?= esc('/admin/customers?' . http_build_query(array_filter(['q' => $search, 'page' => $n], static fn($v) => $v !== ''))); ?>"><?= $n; ?></a></li>
            <?php endfor; ?>
        </ul></nav>
    <?php endif; ?>
</main>
<?= $this->endSection(); ?>
