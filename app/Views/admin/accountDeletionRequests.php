<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>
<section class="container py-4" style="max-width:1200px">
    <header class="d-flex justify-content-between align-items-end gap-3 flex-wrap mb-4">
        <div><p class="text-uppercase small text-secondary mb-1">DEVELOPER</p><h1 class="h3 mb-0"><?= esc($title); ?></h1></div>
        <a class="btn btn-outline-secondary" href="/admin/customers">Kembali ke pelanggan</a>
    </header>
    <?php if (!empty($msg)): ?><div class="alert alert-success" role="status"><?= esc($msg); ?></div><?php endif; ?>
    <?php if (!empty($err)): ?><div class="alert alert-danger" role="alert"><?= esc($err); ?></div><?php endif; ?>
    <?php if (!empty($unavailable)): ?>
        <div class="alert alert-warning">Tabel belum tersedia. Jalankan migrasi `CreateCustomerAccountDeletionRequests` terlebih dahulu.</div>
    <?php else: ?>
        <div class="alert alert-info">Gunakan status selesai hanya setelah tindakan penghapusan/anonymisasi yang sesuai retensi telah dilakukan. Halaman ini mencatat permintaan dan keputusan; halaman ini tidak menghapus transaksi secara otomatis.</div>
        <?php if (empty($requests)): ?><p class="text-secondary">Belum ada permintaan penghapusan akun.</p><?php endif; ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>ID</th><th>Email akun</th><th>Status</th><th>Diajukan</th><th>Catatan admin</th><th>Tindakan</th></tr></thead>
                <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td>#<?= (int) $request['id']; ?></td>
                        <td><?= esc($request['user_email']); ?></td>
                        <td><?= esc($request['status']); ?></td>
                        <td><?= esc($request['requested_at']); ?></td>
                        <td><?= esc($request['resolution_note'] ?? '—'); ?></td>
                        <td style="min-width:330px">
                            <?php if (in_array((string) $request['status'], ['pending', 'reviewed'], true)): ?>
                                <form method="post" action="/admin/account-deletion-requests/<?= (int) $request['id']; ?>" class="d-grid gap-2">
                                    <?= csrf_field(); ?>
                                    <select name="status" class="form-select" required>
                                        <option value="reviewed">Sedang ditinjau</option>
                                        <option value="completed">Selesai setelah tindakan manual</option>
                                        <option value="retained">Data dipertahankan sesuai retensi</option>
                                    </select>
                                    <input name="resolution_note" class="form-control" maxlength="500" required placeholder="Catatan keputusan (min. 5 karakter)">
                                    <input name="current_password" class="form-control" type="password" autocomplete="current-password" maxlength="72" required placeholder="Kata sandi developer">
                                    <input name="confirmation" class="form-control" maxlength="8" required placeholder="REVIEWED / DONE / RETAINED">
                                    <button class="btn btn-dark" type="submit">Simpan keputusan</button>
                                </form>
                            <?php else: ?>
                                <small><?= esc($request['handled_by'] ?? $request['reviewed_by'] ?? '—'); ?><br><?= esc($request['handled_at'] ?? $request['reviewed_at'] ?? ''); ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection(); ?>
