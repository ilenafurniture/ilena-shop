<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<section class="admin-records-page admin-customers-page">
    <header class="admin-records-heading">
        <div>
            <h1>Akun Pelanggan</h1>
            <p><?= number_format((int) ($total ?? count($customers)), 0, ',', '.'); ?> akun · 50 baris per halaman</p>
        </div>
        <form method="get" action="/admin/customers" class="admin-records-search">
            <label class="visually-hidden" for="customer-search">Cari nama atau email</label>
            <input id="customer-search" class="form-control" type="search" name="q" value="<?= esc($search); ?>" placeholder="Cari nama atau email…">
            <button class="btn btn-primary" type="submit">Cari</button>
            <?php if ($search !== ''): ?><a class="btn btn-outline-secondary" href="/admin/customers">Hapus filter</a><?php endif; ?>
        </form>
    </header>

    <?php if (!empty($msg)): ?><div class="alert alert-success" role="status"><?= esc($msg); ?></div><?php endif; ?>
    <?php if (!empty($err)): ?><div class="alert alert-danger" role="alert"><?= esc($err); ?></div><?php endif; ?>

    <p class="admin-records-note">Pilih “Tangguhkan” pada akun yang perlu dibatasi. Form alasan dan durasi hanya terbuka saat diperlukan.</p>

    <div class="admin-data-table-wrap">
        <table class="table admin-data-table align-middle">
            <thead><tr><th>Pelanggan</th><th>Akun</th><th>Penangguhan</th><th>Tindakan</th></tr></thead>
            <tbody>
            <?php foreach ($customers as $customer): ?>
                <?php
                    $state = $customer['suspension'] ?? null;
                    $isActive = (string) ($customer['active'] ?? '0') === '1';
                    $customerKey = substr(sha1((string) $customer['email']), 0, 12);
                ?>
                <tr>
                    <td data-label="Pelanggan">
                        <strong><?= esc($customer['nama'] ?: 'Tanpa nama'); ?></strong><br>
                        <span class="admin-records-muted"><?= esc($customer['email']); ?></span>
                    </td>
                    <td data-label="Akun">
                        <span class="admin-status-pill admin-status-pill--<?= $isActive ? 'active' : 'unverified'; ?>"><?= $isActive ? 'Aktif' : 'Belum verifikasi'; ?></span>
                    </td>
                    <td data-label="Penangguhan">
                        <?php if ($state): ?>
                            <span class="admin-status-pill admin-status-pill--suspended">Aktif</span>
                            <div class="admin-records-muted mt-1">Sampai <?= esc((new DateTimeImmutable($state['until'], new DateTimeZone('UTC')))->format('d M Y H:i')); ?> UTC</div>
                            <?php if ($state['reason'] !== ''): ?><div class="admin-records-muted mt-1">Alasan: <?= esc($state['reason']); ?></div><?php endif; ?>
                        <?php else: ?>
                            <span class="admin-records-muted">Tidak ada</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Tindakan">
                        <?php if ($state): ?>
                            <form method="post" action="/admin/customers/reinstate" class="admin-customer-action">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="email" value="<?= esc($customer['email'], 'attr'); ?>">
                                <button class="btn btn-sm btn-outline-secondary" type="submit">Pulihkan</button>
                            </form>
                        <?php else: ?>
                            <details class="admin-customer-suspend-details">
                                <summary>Tangguhkan</summary>
                                <form method="post" action="/admin/customers/suspend" class="admin-customer-suspend-form">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="email" value="<?= esc($customer['email'], 'attr'); ?>">
                                    <label for="suspend-days-<?= esc($customerKey, 'attr'); ?>">Durasi</label>
                                    <select id="suspend-days-<?= esc($customerKey, 'attr'); ?>" class="form-select form-select-sm" name="days" required>
                                        <option value="1">1 hari</option><option value="3">3 hari</option><option value="7">7 hari</option><option value="30">30 hari</option><option value="90">90 hari</option>
                                    </select>
                                    <label for="suspend-reason-<?= esc($customerKey, 'attr'); ?>">Alasan <span>(opsional)</span></label>
                                    <input id="suspend-reason-<?= esc($customerKey, 'attr'); ?>" class="form-control form-control-sm" name="reason" maxlength="500" placeholder="Contoh: aktivitas mencurigakan…">
                                    <button class="btn btn-sm btn-danger" type="submit">Terapkan</button>
                                </form>
                            </details>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$customers): ?><tr><td colspan="4" class="admin-records-empty"><?= $search !== '' ? 'Tidak ada akun yang cocok. Coba kata kunci lain.' : 'Belum ada akun pelanggan.'; ?></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
        $rangeStart = $total > 0 ? (($page - 1) * 50) + 1 : 0;
        $rangeEnd = min($page * 50, $total);
        $query = ['q' => $search];
    ?>
    <nav class="admin-records-pagination admin-customer-pagination" aria-label="Halaman pelanggan">
        <span class="admin-customer-range">Menampilkan <?= (int) $rangeStart; ?>–<?= (int) $rangeEnd; ?> dari <?= number_format((int) $total, 0, ',', '.'); ?> akun</span>
        <div class="admin-customer-page-controls">
            <a class="btn btn-sm btn-outline-secondary <?= $page <= 1 ? 'disabled' : ''; ?>" href="<?= esc('/admin/customers?' . http_build_query($query + ['page' => max(1, $page - 1)])); ?>" <?= $page <= 1 ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>Sebelumnya</a>
            <span>Halaman <?= (int) $page; ?> dari <?= (int) $pages; ?></span>
            <a class="btn btn-sm btn-outline-secondary <?= $page >= $pages ? 'disabled' : ''; ?>" href="<?= esc('/admin/customers?' . http_build_query($query + ['page' => min($pages, $page + 1)])); ?>" <?= $page >= $pages ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>Berikutnya</a>
        </div>
    </nav>
</section>
<?= $this->endSection(); ?>
