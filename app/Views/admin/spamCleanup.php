<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<?php
$candidates = $candidates ?? [];
$inactiveDays = (int)($inactiveDays ?? 7);
?>

<style>
.spam-page { max-width: 1100px; margin: 0 auto; }
.spam-card {
    background:#fff; border:1px solid #e9ecef; border-radius:18px;
    box-shadow:0 10px 24px rgba(15,23,42,.06);
}
.spam-title { font-weight:800; letter-spacing:-1px; }
.spam-muted { color:#6b7280; }
.spam-email { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size:13px; word-break:break-all; }
.spam-badge { border-radius:999px; padding:6px 10px; background:#fee2e2; color:#991b1b; font-weight:800; font-size:12px; }
</style>

<div class="spam-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="spam-title mb-1">Cleanup Spam Akun</h1>
            <p class="spam-muted mb-0">Preview akun register mencurigakan, payload injection, tempmail, dan akun belum aktif yang OTP-nya sudah lama kadaluarsa.</p>
        </div>
        <span class="spam-badge"><?= count($candidates); ?> kandidat spam</span>
    </div>

    <?php if (!empty($msg)): ?><div class="alert alert-success"><?= esc($msg); ?></div><?php endif; ?>
    <?php if (!empty($err)): ?><div class="alert alert-danger"><?= esc($err); ?></div><?php endif; ?>

    <form method="get" action="/admin/spam-cleanup" class="spam-card p-4 mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-8">
                <label for="inactive_days" class="form-label fw-bold">Tampilkan akun belum aktif</label>
                <select id="inactive_days" name="inactive_days" class="form-select">
                    <?php foreach ([1, 7, 30, 90] as $day): ?>
                        <option value="<?= $day; ?>" <?= $inactiveDays === $day ? 'selected' : ''; ?>>
                            OTP kadaluarsa lebih dari <?= $day; ?> hari
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Akun customer role 0, active 0, dan OTP sudah lewat akan masuk kandidat cleanup.</div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-dark w-100">Refresh Kandidat</button>
            </div>
        </div>
    </form>

    <form method="post" action="/admin/spam-cleanup" class="spam-card p-4">
        <?php if (empty($candidates)): ?>
            <div class="alert alert-success mb-0">Tidak ada kandidat akun spam terdeteksi.</div>
        <?php else: ?>
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                <button type="button" class="btn btn-light border" onclick="document.querySelectorAll('.spam-check').forEach(el => el.checked = true)">Pilih Semua</button>
                <button type="button" class="btn btn-light border" onclick="document.querySelectorAll('.spam-check').forEach(el => el.checked = false)">Kosongkan</button>
                <button type="submit" class="btn btn-danger ms-auto" onclick="return confirm('Hapus akun spam yang dipilih?')">Hapus Terpilih</button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width:44px"></th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Active</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidates as $row): ?>
                            <tr>
                                <td><input class="spam-check" type="checkbox" name="emails[]" value="<?= esc($row['email']); ?>" checked></td>
                                <td class="spam-email"><?= esc($row['email']); ?></td>
                                <td><?= esc($row['role']); ?></td>
                                <td><?= esc($row['active']); ?></td>
                                <td><?= esc($row['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </form>
</div>

<?= $this->endSection(); ?>
