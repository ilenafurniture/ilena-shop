<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<!-- Modal ganti sandi -->
<div id="edit-sandi"
    style="position: fixed; width: 100vw; height: 100svh; background-color: rgba(0,0,0,0.7); z-index: 100; top: 0; left: 0;"
    class="<?= $msgSandi ? 'd-flex' : 'd-none'; ?> justify-content-center align-items-center">
    <div class="p-4 bg-light ilena-account-password-modal">
        <h2 style="letter-spacing: -1px"><?= lang('Store.changePassword'); ?></h2>
        <?php if ($msgSandi) { ?>
        <div class="pemberitahuan my-1 mx-auto" style="width: fit-content;" role="alert">
            <?= $msgSandi; ?>
        </div>
        <?php } ?>
        <form action="<?= esc(ilena_store_path('/editsandi/account'), 'attr'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="mb-1">
                <label for="current-password-account"><?= lang('Store.currentPassword'); ?></label>
                <input id="current-password-account" type="password" name="sandiLama" class="form-control" autocomplete="current-password" maxlength="72" required>
            </div>
            <div class="mb-1">
                <label for=""><?= lang('Store.newPassword'); ?></label>
                <input type="password" name="sandi" class="form-control" autocomplete="new-password" minlength="12" maxlength="72" required>
            </div>
            <div class="mb-2">
                <label for=""><?= lang('Store.confirmPassword'); ?></label>
                <input type="password" class="form-control" oninput="konfirmasiSandi(event)" required
                    name="sandiKonfirm">
                <div class="invalid-feedback"><?= lang('Store.passwordMismatch'); ?></div>
            </div>
            <div class="d-flex justify-content-center gap-1">
                <button type="submit" class="btn-default"><?= lang('Store.update'); ?></button>
                <button class="btn-teks-aja" onclick="closeEditSandi()"><?= lang('Store.cancel'); ?></button>
            </div>
        </form>
    </div>
</div>

<style>
:root {
    --ink: #111827;
    --muted: #6b7280;
    --line: #e5e7eb;
    --card: #fff;
    --card-weak: #fafafa;
    --brand: var(--merah);
    --fs-base: 15px;
    --fs-small: 13.5px;
    --fs-xsmall: 13px;
}

.konten {
    max-width: 1100px
}

.baris-ke-kolom {
    display: flex;
    gap: 18px;
    align-items: flex-start
}

.baris-ke-kolom>.tigapuluh-ke-seratus {
    width: 360px
}

.baris-ke-kolom>div[style*="flex:1"] {
    flex: 1
}

@media (max-width:992px) {
    .baris-ke-kolom {
        flex-direction: column
    }

    .baris-ke-kolom>.tigapuluh-ke-seratus {
        width: 100%
    }
}

/* Card & buttons */
.card {
    border: 1px solid var(--line);
    border-radius: 14px;
    background: var(--card);
    box-shadow: 0 1px 10px rgba(0, 0, 0, .03)
}

.garis {
    display: block;
    height: 1px;
    background: var(--line)
}

.teks-sedang {
    font-weight: 800;
    letter-spacing: -.02em
}

.btn-default-merah {
    background: var(--brand);
    border: 1px solid var(--brand);
    color: #fff;
    border-radius: 10px;
    padding: .6rem 1rem;
    font-weight: 700
}

.btn-default-abu {
    background: #f3f4f6;
    border: 1px solid var(--line);
    color: #111827;
    border-radius: 10px;
    padding: .6rem 1rem;
    font-weight: 700
}

.btn-teks-aja {
    border: none;
    background: transparent;
    color: var(--brand);
    font-weight: 600
}

/* Address list */
.container-address {
    display: flex;
    flex-direction: column;
    gap: 10px
}

.item-address {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 12px;
    border: 1px solid var(--line);
    border-radius: 12px;
    background: #fff;
    transition: .15s
}

.item-address .nama {
    font-weight: 800;
    letter-spacing: -.01em;
    font-size: clamp(1rem, 0.95rem + .3vw, 1.08rem);
    margin-bottom: .25rem
}

.item-address:hover {
    background: #fafafa
}

/* Voucher grid */
.vch-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px
}

@media (max-width:768px) {
    .vch-grid {
        grid-template-columns: 1fr
    }
}

.vch-card {
    border: 1px dashed var(--line);
    border-radius: 12px;
    padding: 12px;
    background: var(--card-weak)
}

.vch-hd {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px
}

.vch-code {
    font-weight: 900;
    letter-spacing: .06em;
    font-size: 1rem
}

.vch-badge {
    border-radius: 999px;
    padding: .2rem .5rem;
    font-size: .75rem;
    font-weight: 700;
    background: #eef2ff;
    color: #4338ca;
    border: 1px dashed #c7d2fe
}

.vch-body {
    font-size: .92rem;
    color: #374151;
    margin-top: 6px
}

.vch-meta {
    font-size: .8rem;
    color: var(--muted)
}

.vch-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px
}

.btn-chip {
    border: 1px solid var(--line);
    border-radius: 999px;
    padding: .4rem .7rem;
    font-weight: 700;
    background: #fff
}

.btn-chip.primary {
    border-color: var(--brand);
    color: var(--brand)
}

.path {
    font-size: .95rem;
    color: var(--muted)
}

.path a {
    text-decoration: none
}

/* Base font */
.container,
.card {
    font-size: var(--fs-base);
}

/* ===== Mobile: kecilkan font & hilangkan bold ===== */
@media (max-width:768px) {

    .container,
    .card {
        font-size: var(--fs-small);
    }

    .btn-default-merah,
    .btn-default-abu {
        font-size: .92rem;
        padding: .55rem .9rem;
    }

    /* turunkan bold di HP */
    .fw-bold,
    .teks-sedang,
    .item-address .nama,
    .vch-code,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    b,
    strong {
        font-weight: 500 !important;
    }
}

@media (max-width:360px) {

    .container,
    .card {
        font-size: var(--fs-xsmall);
    }

    .btn-default-merah,
    .btn-default-abu {
        font-size: .9rem
    }
}
</style>

<div class="container konten baris-ke-kolom ilena-account-page">
    <div class="tigapuluh-ke-seratus">
        <div class="card p-4 ilena-account-nav">
            <h2 style="letter-spacing: -1px"><?= lang('Store.accountTitle'); ?></h2>
            <div class="mt-2 d-flex justify-content-between py-1">
                <p class="m-0">Email</p>
                <p class="fw-bold m-0"><?= esc($email); ?></p>
            </div>
            <div class="d-flex justify-content-between py-1">
                <p class="m-0"><?= lang('Store.password'); ?></p>
                <a class="btn-teks-aja" onclick="openGantiSandi()"><?= lang('Store.changePassword'); ?></a>
            </div>
            <span class="garis my-2"></span>
            <a href="<?= esc(ilena_store_path('/account'), 'attr'); ?>" class="mb-1 btn-default-abu"><?= lang('Store.profileNav'); ?></a>
            <a href="<?= esc(ilena_store_path('/order'), 'attr'); ?>" class="mb-1 btn-default-abu"><?= lang('Store.ordersNav'); ?></a>
            <a href="<?= esc(ilena_store_path('/wishlist'), 'attr'); ?>" class="mb-1 btn-default-abu"><?= esc(lang('Store.wishlist')); ?></a>
            <form action="<?= esc(ilena_store_path('/logout'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?><button type="submit" class="btn-default-merah"><?= lang('Store.logout'); ?></button>
            </form>
        </div>
    </div>

    <div class="ilena-account-main" style="flex:1;">
        <header class="ilena-account-heading">
            <div>
                <p class="ilena-account-heading__eyebrow"><?= service('request')->getLocale() === 'en' ? 'ACCOUNT' : 'AKUN'; ?> <span>/</span> <?= lang('Store.profileNav'); ?></p>
                <h1><?= lang('Store.profileHeading'); ?></h1>
                <p class="ilena-account-heading__description"><?= lang('Store.profileDescription'); ?></p>
            </div>
        </header>

        <div class="ilena-account-profile-row mt-2 d-flex justify-content-between py-1">
            <p class="m-0">Email</p>
            <p class="fw-bold m-0"><?= esc($email); ?></p>
        </div>
        <section class="ilena-account-profile-row mb-3" aria-labelledby="email-change-heading">
            <h2 id="email-change-heading" class="h5"><?= esc(lang('Store.changeAccountEmail')); ?></h2>
            <form action="<?= esc(ilena_store_path('/account/email/change'), 'attr'); ?>" method="post" class="d-flex gap-2 flex-wrap">
                <?= csrf_field(); ?>
                <label class="visually-hidden" for="account-new-email"><?= esc(lang('Store.newEmailAddress')); ?></label>
                <input id="account-new-email" name="new_email" type="email" maxlength="191" autocomplete="email" class="form-control" required placeholder="<?= esc(lang('Store.newEmailAddress'), 'attr'); ?>">
                <label class="visually-hidden" for="account-email-password"><?= esc(lang('Store.currentPassword')); ?></label>
                <input id="account-email-password" name="current_password" type="password" maxlength="72" autocomplete="current-password" class="form-control" required placeholder="<?= esc(lang('Store.currentPassword'), 'attr'); ?>">
                <button type="submit" class="btn-default-abu"><?= esc(lang('Store.sendEmailConfirmation')); ?></button>
            </form>
        </section>
        <?php if (!empty($msg)): ?><div class="pemberitahuan my-3" role="status"><?= esc($msg); ?></div><?php endif; ?>
        <form action="<?= esc(ilena_store_path('/account/profile'), 'attr'); ?>" method="post" class="ilena-account-profile-form my-3">
            <?= csrf_field(); ?>
            <div class="mb-3"><label for="account-name" class="form-label"><?= esc(lang('Store.fullNameLabel')); ?></label>
                <input id="account-name" name="nama" class="form-control" type="text" maxlength="100" autocomplete="name" required value="<?= esc(old('nama', (string)$nama), 'attr'); ?>"></div>
            <div class="mb-3"><label for="account-phone" class="form-label"><?= esc(lang('Store.phoneNumber')); ?></label>
                <input id="account-phone" name="nohp" class="form-control" type="tel" inputmode="numeric" maxlength="15" autocomplete="tel" value="<?= esc(old('nohp', (string)($nohp ?? '')), 'attr'); ?>" aria-describedby="account-phone-hint">
                <small id="account-phone-hint" class="text-secondary"><?= esc(lang('Store.phoneDigitsHint')); ?></small></div>
            <button type="submit" class="btn-default-merah"><?= esc(lang('Store.saveProfile')); ?></button>
        </form>
        <section class="ilena-account-profile-row mb-4" aria-labelledby="phone-verification-heading">
            <h2 id="phone-verification-heading" class="h5"><?= esc(lang('Store.phoneVerificationStatus')); ?></h2>
            <p class="mb-2" role="status"><?= esc($phoneVerified ? lang('Store.phoneVerifiedLabel') : lang('Store.phoneNotVerifiedLabel')); ?></p>
            <?php if (empty($phoneVerified)): ?>
                <form action="<?= esc(ilena_store_path('/account/phone/send'), 'attr'); ?>" method="post" class="mb-2">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn-default-abu" <?= trim((string)($nohp ?? '')) === '' ? 'disabled' : ''; ?>><?= esc(lang('Store.sendPhoneCode')); ?></button>
                </form>
                <form action="<?= esc(ilena_store_path('/account/phone/verify'), 'attr'); ?>" method="post" class="d-flex gap-2 flex-wrap">
                    <?= csrf_field(); ?>
                    <label class="visually-hidden" for="account-phone-code"><?= esc(lang('Store.phoneCodePlaceholder')); ?></label>
                    <input id="account-phone-code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" class="form-control" style="max-width:12rem" placeholder="<?= esc(lang('Store.phoneCodePlaceholder'), 'attr'); ?>" required>
                    <button type="submit" class="btn-default-merah"><?= esc(lang('Store.verifyPhoneCode')); ?></button>
                </form>
            <?php endif; ?>
        </section>
        <?php if (!empty($googleOAuthEnabled)): ?><div class="ilena-account-profile-row d-flex align-items-center justify-content-between gap-3 py-2">
            <span><?= esc($googleLinked ? lang('Store.googleConnected') : lang('Store.googleNotConnected')); ?></span>
            <?php if (!$googleLinked): ?><form method="post" action="<?= esc(ilena_store_path('/auth/google/link'), 'attr'); ?>" class="m-0">
                <?= csrf_field(); ?><button type="submit" class="btn-default-abu"><?= esc(lang('Store.connectGoogle')); ?></button>
            </form><?php else: ?><form method="post" action="<?= esc(ilena_store_path('/auth/google/unlink'), 'attr'); ?>" class="m-0 d-flex gap-2 align-items-center">
                <?= csrf_field(); ?>
                <input type="password" name="current_password" class="form-control" autocomplete="current-password" maxlength="72" required aria-label="<?= esc(lang('Store.currentPassword'), 'attr'); ?>" placeholder="<?= esc(lang('Store.currentPassword'), 'attr'); ?>">
                <button type="submit" class="btn-default-abu"><?= esc(lang('Store.disconnectGoogle')); ?></button>
            </form><?php endif; ?>
        </div><?php endif; ?>
        <div class="ilena-account-profile-row ilena-account-password-row mb-4 d-flex justify-content-between py-1">
            <p class="m-0"><?= lang('Store.password'); ?></p>
            <a class="btn-teks-aja" onclick="openGantiSandi()"><?= lang('Store.changePassword'); ?></a>
        </div>
        <form action="<?= esc(ilena_store_path('/account/sessions/revoke'), 'attr'); ?>" method="post" class="mb-3">
            <?= csrf_field(); ?>
            <button type="submit" class="btn-default-abu"><?= esc(lang('Store.signOutOtherDevices')); ?></button>
        </form>
        <p class="mb-4"><a href="<?= esc(ilena_store_path('/account/export'), 'attr'); ?>"><?= esc(lang('Store.downloadAccountData')); ?></a></p>
        <section class="ilena-account-profile-row my-4" aria-labelledby="account-deletion-heading">
            <h2 id="account-deletion-heading" class="h5"><?= esc(lang('Store.accountDeletionTitle')); ?></h2>
            <p><?= esc(lang('Store.accountDeletionNotice')); ?></p>
            <?php if (!empty($deletionRequestPending)): ?>
                <p role="status"><?= esc(($deletionRequest['status'] ?? '') === 'reviewed' ? lang('Store.accountDeletionInReview') : lang('Store.accountDeletionPending')); ?></p>
                <form action="<?= esc(ilena_store_path('/account/deletion-request/cancel'), 'attr'); ?>" method="post" class="d-flex gap-2 flex-wrap">
                    <?= csrf_field(); ?>
                    <input type="password" name="current_password" class="form-control" autocomplete="current-password" maxlength="72" required aria-label="<?= esc(lang('Store.currentPassword'), 'attr'); ?>" placeholder="<?= esc(lang('Store.currentPassword'), 'attr'); ?>">
                    <input type="text" name="confirmation" class="form-control" maxlength="6" required autocomplete="off" aria-label="<?= esc(lang('Store.typeCancel'), 'attr'); ?>" placeholder="<?= esc(lang('Store.typeCancel'), 'attr'); ?>">
                    <button type="submit" class="btn-default-abu"><?= esc(lang('Store.cancelAccountDeletion')); ?></button>
                </form>
            <?php else: ?>
                <form action="<?= esc(ilena_store_path('/account/deletion-request'), 'attr'); ?>" method="post" class="d-flex gap-2 flex-wrap">
                    <?= csrf_field(); ?>
                    <input type="password" name="current_password" class="form-control" autocomplete="current-password" maxlength="72" required aria-label="<?= esc(lang('Store.currentPassword'), 'attr'); ?>" placeholder="<?= esc(lang('Store.currentPassword'), 'attr'); ?>">
                    <input type="text" name="confirmation" class="form-control" maxlength="6" required autocomplete="off" aria-label="<?= esc(lang('Store.typeDelete'), 'attr'); ?>" placeholder="<?= esc(lang('Store.typeDelete'), 'attr'); ?>">
                    <button type="submit" class="btn-default-abu"><?= esc(lang('Store.requestAccountDeletion')); ?></button>
                </form>
            <?php endif; ?>
        </section>
        <span class="garis"></span>

        <?php if (session()->get('active') != '0') { ?>
        <div class="ilena-account-section-heading my-4">
            <h2><?= lang('Store.shippingAddresses'); ?></h2>
            <p><?= count($alamat) <= 0 ? lang('Store.noSavedAddresses') : count($alamat) . ' ' . lang('Store.savedAddresses'); ?></p>
        </div>

        <div class="container-address my-4">
            <?php foreach ($alamat as $ind_a => $a) { ?>
            <div class="item-address">
                <div style="flex: 1;">
                    <p class="mb-1 nama"><?= esc($a['nama_penerima']) ?></p>
                    <p class="mb-1"><?= esc($a['alamat_lengkap']) ?></p>
                    <p class="mb-1"><?= esc($a['nohp_penerima']) ?></p>
                    <p class="mb-1"><b style="font-weight: 600;"><?= lang('Store.ordererInfo'); ?></b> : <?= esc($a['email_pemesan']) ?></p>
                </div>
                <div style="width: 100px" class="d-flex gap-3 justify-content-end align-items-start">
                    <a class="btn-teks-aja text-dark" data-bs-toggle="modal" data-bs-target="#editModal" type="button"
                        onclick="openEditModal(<?= (int)$ind_a; ?>)"><?= lang('Store.edit'); ?></a>
                    <form action="<?= esc(ilena_store_path('/deleteaddress/' . (int)$ind_a . '/account'), 'attr'); ?>" method="post" class="m-0">
                        <?= csrf_field(); ?><button type="submit" class="btn-teks-aja"><?= lang('Store.delete'); ?></button>
                    </form>
                </div>
            </div>
            <?php } ?>
        </div>

        <button type="button" class="btn-teks-aja ilena-account-add-address" data-bs-toggle="modal" data-bs-target="#exampleModal">
            <i class="material-icons">add</i> <?= lang('Store.addAddress'); ?>
        </button>

        <!-- ====== Voucher Saya ====== -->
        <?php
          $voucherUser = isset($voucherUser) && is_array($voucherUser) ? $voucherUser : [];
          $claimedIds = (array)(session()->get('voucher_claimed') ?? []);
          $voucherClaimedDetail = isset($voucherClaimedDetail) && is_array($voucherClaimedDetail) ? $voucherClaimedDetail : [];
          $showVoucherSection = (count($voucherUser) > 0) || (count($voucherClaimedDetail) > 0) || (count($claimedIds) > 0);
        ?>

        <?php if ($showVoucherSection): ?>
        <div class="ilena-account-section-heading my-4">
            <h2><?= lang('Store.voucherTitle'); ?></h2>
            <p><?= lang('Store.voucherDescription'); ?></p>

            <div class="vch-grid">
                <?php if (count($voucherUser) > 0): ?>
                <?php foreach ($voucherUser as $v): 
                $kode = $v['kode'] ?? '';
                $nama = $v['nama'] ?? 'Voucher';
                $tipe = ($v['tipe'] ?? ($v['satuan'] ?? 'persen')) === 'persen' ? 'persen' : 'nominal';
                $nilai= (float)($v['nilai'] ?? ($v['nominal'] ?? 0));
                $target = strtolower($v['target'] ?? 'semua');
                $aktif = !empty($v['aktif']);
                $mulai = !empty($v['mulai']) ? substr($v['mulai'],0,10) : '';
                $akhir = !empty($v['berakhir']) ? substr($v['berakhir'],0,10) : '';
              ?>
                <div class="vch-card">
                    <div class="vch-hd">
                        <div class="vch-code"><?= esc($kode) ?></div>
                        <?php if($target==='baru'): ?><span class="vch-badge"><?= lang('Store.newMember'); ?></span><?php endif; ?>
                    </div>
                    <div class="vch-body"><b><?= esc($nama) ?></b></div>
                    <div class="vch-meta">
                        <?= lang('Store.voucherType'); ?>:
                        <?= $tipe==='persen' ? (number_format($nilai,0).'%') : ('Rp '.number_format($nilai,0,',','.')) ?>
                        <?php if($mulai || $akhir): ?> · <?= lang('Store.period'); ?>: <?= esc($mulai) ?> – <?= esc($akhir) ?><?php endif; ?>
                        · <?= lang('Store.status'); ?>: <?= $aktif ? lang('Store.statusActive') : lang('Store.statusInactive'); ?>
                    </div>
                    <div class="vch-actions">
                        <button class="btn-chip primary" type="button"
                            onclick="salinKode('<?= esc($kode, 'attr') ?>')"><?= lang('Store.copyCode'); ?></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($voucherClaimedDetail) > 0): ?>
                <?php foreach ($voucherClaimedDetail as $v):
                $kode = $v['kode'] ?? '';
                $nama = $v['nama'] ?? 'Voucher';
                $tipe = ($v['tipe'] ?? ($v['satuan'] ?? 'persen')) === 'persen' ? 'persen' : 'nominal';
                $nilai= (float)($v['nilai'] ?? ($v['nominal'] ?? 0));
                $mulai = !empty($v['mulai']) ? substr($v['mulai'],0,10) : '';
                $akhir = !empty($v['berakhir']) ? substr($v['berakhir'],0,10) : '';
              ?>
                <div class="vch-card">
                    <div class="vch-hd">
                        <div class="vch-code"><?= esc($kode) ?></div>
                        <span class="vch-badge"><?= lang('Store.claimed'); ?></span>
                    </div>
                    <div class="vch-body"><b><?= esc($nama) ?></b></div>
                    <div class="vch-meta">
                        <?= lang('Store.voucherType'); ?>:
                        <?= $tipe==='persen' ? (number_format($nilai,0).'%') : ('Rp '.number_format($nilai,0,',','.')) ?>
                        <?php if($mulai || $akhir): ?> · <?= lang('Store.period'); ?>: <?= esc($mulai) ?> – <?= esc($akhir) ?><?php endif; ?>
                    </div>
                    <div class="vch-actions">
                        <button class="btn-chip primary" type="button"
                            onclick="salinKode('<?= esc($kode, 'attr') ?>')"><?= lang('Store.copyCode'); ?></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php elseif (count($claimedIds) > 0 && count($voucherClaimedDetail) === 0 && count($voucherUser)===0): ?>
                <?php foreach ($claimedIds as $cid): ?>
                <div class="vch-card">
                    <div class="vch-hd">
                        <div class="vch-code">#<?= (int)$cid ?></div>
                        <span class="vch-badge"><?= lang('Store.claimed'); ?></span>
                    </div>
                    <div class="vch-body"><b><?= lang('Store.claimedVoucher'); ?></b></div>
                    <div class="vch-meta"><?= lang('Store.voucherDetailsAtCheckout'); ?></div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <!-- ====== End Voucher ====== -->

        <!-- Add Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form action="<?= esc(ilena_store_path('/addaddress'), 'attr'); ?>" method="post">
                <?= csrf_field(); ?>
                <input type="text" value="account" name="checkpage" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title" id="exampleModalLabel"><?= lang('Store.newAddress'); ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="pb-3 border-bottom">
                                <h5 class="mb-2"><?= lang('Store.ordererInfo'); ?></h5>
                                <div class="form-floating mb-1">
                            <input type="email" class="form-control" placeholder="<?= esc(lang('Store.addressEmailPlaceholder'), 'attr'); ?>" name="emailPem"
                                        required value="<?= esc($email); ?>">
                                    <label for="floatingInput"><?= lang('Store.email'); ?></label>
                                </div>
                            </div>

                            <div class="py-3">
                                <h5 class="mb-2"><?= lang('Store.recipientInfo'); ?></h5>
                                <div class="form-floating mb-1">
                            <input type="text" class="form-control" placeholder="<?= esc(lang('Store.addressContactNamePlaceholder'), 'attr'); ?>" name="nama" required
                                        value="<?= esc($nama); ?>">
                                    <label for="floatingInput"><?= lang('Store.fullName'); ?></label>
                                </div>
                                <div class="form-floating mb-1">
                            <input type="number" class="form-control" placeholder="<?= esc(lang('Store.addressPhonePlaceholder'), 'attr'); ?>" name="nohp"
                                        required value="<?= esc($nohp); ?>">
                                    <label for="floatingInput"><?= lang('Store.mobileNumber'); ?></label>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="provinsi"
                                            required>
                                            <option value=""><?= lang('Store.chooseProvince'); ?></option>
                                            <?php if (!empty($provinsi) && is_array($provinsi)): ?>
                                            <?php foreach ($provinsi as $p):
                                                    $pid   = $p['province_id'] ?? ($p['id'] ?? ($p['value'] ?? ''));
                                                    $pname = $p['province']    ?? ($p['label'] ?? ($p['name'] ?? ''));
                                                    $pname = explode('/', (string)$pname)[0];
                                                ?>
                                            <option value="<?= esc($pid) ?>-<?= esc($pname) ?>">
                                                <?= esc($pname) ?>
                                            </option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.province'); ?></label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kota"
                                            required>
                                            <option value=""><?= lang('Store.chooseCity'); ?></option>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.city'); ?></label>
                                    </div>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kecamatan"
                                            required>
                                            <option value=""><?= lang('Store.chooseDistrict'); ?></option>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.district'); ?></label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kodepos"
                                            required>
                                            <option value=""><?= lang('Store.chooseVillage'); ?></option>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.village'); ?></label>
                                    </div>
                                </div>

                                <div class="form-alamat form-floating mb-1">
                            <input type="text" class="form-control" placeholder="<?= esc(lang('Store.addressStreetPlaceholder'), 'attr'); ?>" name="alamat_add"
                                        required>
                                    <label for="floatingInput"><?= lang('Store.streetAddress'); ?></label>
                                    <div class="invalid-feedback"><?= lang('Store.invalidAddressSlash'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal"><?= lang('Store.cancel'); ?></button>
                            <button type="submit" class="btn btn-lonjong"><?= lang('Store.add'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <form method="post" id="formEdit">
                <?= csrf_field(); ?>
                <input type="text" value="<?= esc(service('request')->getUri()->getPath(), 'attr'); ?>" class="d-none" name="url">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title" id="exampleModalLabel"><?= lang('Store.editAddress'); ?></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="pb-3 border-bottom">
                                <h5 class="mb-2"><?= lang('Store.ordererInfo'); ?></h5>
                                <div class="form-floating mb-1">
                            <input type="email" class="form-control" placeholder="<?= esc(lang('Store.addressEmailPlaceholder'), 'attr'); ?>" name="emailPem"
                                        required id="inputEmail">
                                    <label for="floatingInput"><?= lang('Store.email'); ?></label>
                                </div>
                            </div>

                            <div class="py-3">
                                <h5 class="mb-2"><?= lang('Store.recipientInfo'); ?></h5>
                                <div class="form-floating mb-1">
                            <input type="text" class="form-control" placeholder="<?= esc(lang('Store.addressContactNamePlaceholder'), 'attr'); ?>" name="nama" required
                                        id="inputNama">
                                    <label for="floatingInput"><?= lang('Store.fullName'); ?></label>
                                </div>
                                <div class="form-floating mb-1">
                            <input type="number" class="form-control" placeholder="<?= esc(lang('Store.addressPhonePlaceholder'), 'attr'); ?>" name="nohp"
                                        required id="inputNohp">
                                    <label for="floatingInput"><?= lang('Store.mobileNumber'); ?></label>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example"
                                            name="provinsiEdit" required>
                                            <option value=""><?= lang('Store.chooseProvince'); ?></option>
                                            <?php if (!empty($provinsi) && is_array($provinsi)): ?>
                                            <?php foreach ($provinsi as $p):
                                                    $pid   = $p['province_id'] ?? ($p['id'] ?? ($p['value'] ?? ''));
                                                    $pname = $p['province']    ?? ($p['label'] ?? ($p['name'] ?? ''));
                                                    $pname = explode('/', (string)$pname)[0];
                                                ?>
                                            <option value="<?= esc($pid) ?>-<?= esc($pname) ?>">
                                                <?= esc($pname) ?>
                                            </option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.province'); ?></label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example" name="kotaEdit"
                                            required>
                                            <option value=""><?= lang('Store.chooseCity'); ?></option>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.city'); ?></label>
                                    </div>
                                </div>

                                <div class="form-alamat d-flex mb-1 gap-1">
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example"
                                            name="kecamatanEdit" required>
                                            <option value=""><?= lang('Store.chooseDistrict'); ?></option>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.district'); ?></label>
                                    </div>
                                    <div class="form-floating w-50">
                                        <select class="form-select" aria-label="Default select example"
                                            name="kodeposEdit" required>
                                            <option value=""><?= lang('Store.chooseVillage'); ?></option>
                                        </select>
                                        <label for="floatingProvinsi"><?= lang('Store.village'); ?></label>
                                    </div>
                                </div>

                                <div class="form-alamat form-floating mb-1">
                            <input type="text" class="form-control" placeholder="<?= esc(lang('Store.addressStreetPlaceholder'), 'attr'); ?>" name="alamat_add"
                                        required>
                                    <label for="floatingInput"><?= lang('Store.streetAddress'); ?></label>
                                    <div class="invalid-feedback"><?= lang('Store.invalidAddressSlash'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-teks-aja" data-bs-dismiss="modal"><?= lang('Store.cancel'); ?></button>
                            <button type="submit" class="btn btn-lonjong"><?= lang('Store.save'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>
</div>

<script>
const provElm = document.querySelector('select[name="provinsi"]');
const kotaElm = document.querySelector('select[name="kota"]');
const kecElm = document.querySelector('select[name="kecamatan"]');
const kodeElm = document.querySelector('select[name="kodepos"]');

const provElmEdit = document.querySelector('select[name="provinsiEdit"]');
const kotaElmEdit = document.querySelector('select[name="kotaEdit"]');
const kecElmEdit = document.querySelector('select[name="kecamatanEdit"]');
const kodeElmEdit = document.querySelector('select[name="kodeposEdit"]');

const inputEmailElm = document.getElementById("inputEmail");
const inputNamaElm = document.getElementById("inputNama");
const inputNohpElm = document.getElementById("inputNohp");
const formEditElm = document.getElementById("formEdit");

const addressCopy = <?= json_encode([
    'chooseCity' => lang('Store.chooseCity'), 'chooseDistrict' => lang('Store.chooseDistrict'), 'chooseVillage' => lang('Store.chooseVillage'),
    'loadingCity' => lang('Store.addressLoadingCity'), 'loadingDistrict' => lang('Store.addressLoadingDistrict'), 'loadingVillage' => lang('Store.addressLoadingVillage'),
    'loadCityFailed' => lang('Store.addressLoadCityFailed'), 'loadDistrictFailed' => lang('Store.addressLoadDistrictFailed'), 'loadVillageFailed' => lang('Store.addressLoadVillageFailed'),
    'cityListFailed' => lang('Store.addressCityListFailed'), 'districtListFailed' => lang('Store.addressDistrictListFailed'), 'villageListFailed' => lang('Store.addressVillageListFailed'),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const alamatJson = JSON.parse(<?= json_encode($alamatJson, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
const storeRoutePrefix = <?= json_encode(service('request')->getLocale() === 'en' ? '/en' : ''); ?>;
const sandiElm = document.querySelector('input[name="sandi"]');
const editSandiElm = document.getElementById('edit-sandi');

function openGantiSandi() {
    editSandiElm.classList.remove('d-none');
    editSandiElm.classList.add('d-flex');
}

function closeEditSandi() {
    editSandiElm.classList.add('d-none');
    editSandiElm.classList.remove('d-flex');
}

function konfirmasiSandi(e) {
    if (sandiElm.value != e.target.value) e.target.classList.add('is-invalid');
    else e.target.classList.remove('is-invalid');
}

function titleCase(str = '') {
    return String(str).toLowerCase().split(' ').map(s => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
}

/* ======= Helper: Salin Kode Voucher ======= */
async function salinKode(k) {
    try {
        await navigator.clipboard.writeText(k);
    alert(<?= json_encode(lang('Store.addressCopySuccess')) ?>.replace('%s', k));
    } catch (e) {
    alert(<?= json_encode(lang('Store.addressCopyFailed')) ?>);
    }
}

/* ---------- FETCH DAERAH (ADD) ---------- */
/* gunakan path absolut supaya tidak gagal saat berada di sub-route */
async function getKota(idprov) {
    try {
        const res = await fetch(`/getkota/${idprov}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kotaElm.innerHTML = `<option value="">${addressCopy.chooseCity}</option>`;
        kecElm.innerHTML = `<option value="">${addressCopy.chooseDistrict}</option>`;
        kodeElm.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
        hasil.forEach(el => {
            const id = el.city_id ?? el.id ?? el.value;
            const nama0 = el.city_name ?? el.label ?? el.name ?? '';
            const nama = String(nama0).split('/')[0];
            const type = el.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = id + "-" + nama;
            opt.innerHTML = el.city_name ? `${nama}${type}` : nama;
            kotaElm.appendChild(opt);
        });
    } catch (e) {
        kotaElm.innerHTML = `<option value="">${addressCopy.loadCityFailed}</option>`;
        console.error(e);
    }
}
async function getKec(idkota) {
    try {
        const res = await fetch(`/getkec/${idkota}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kecElm.innerHTML = `<option value="">${addressCopy.chooseDistrict}</option>`;
        kodeElm.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
        hasil.forEach(el => {
            const id = el.subdistrict_id ?? el.id ?? el.value;
            const nama = String(el.subdistrict_name ?? el.label ?? el.name ?? '').split('/')[0];
            const opt = document.createElement("option");
            opt.value = id + "-" + nama; // simpan "id-nama"
            opt.innerHTML = nama;
            kecElm.appendChild(opt);
        });
    } catch (e) {
        kecElm.innerHTML = `<option value="">${addressCopy.loadDistrictFailed}</option>`;
        console.error(e);
    }
}
async function getKode(kecId) {
    try {
        const res = await fetch(`/getkode/${kecId}`); // kirim ID kecamatan
        const json = await res.json();
        const hasil = json?.results ?? json?.data?.results ?? json ?? [];
        kodeElm.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
        hasil.forEach(el => {
            const desa = titleCase(String(el.DesaKelurahan ?? el.desa ?? el.label ?? el.name ?? '').split(
                '/')[0]);
            const kp = String(el.KodePos ?? el.kodepos ?? el.zip ?? '');
            const opt = document.createElement("option");
            opt.value = desa + "-" + kp;
            opt.innerHTML = desa;
            kodeElm.appendChild(opt);
        });
    } catch (e) {
        kodeElm.innerHTML = `<option value="">${addressCopy.loadVillageFailed}</option>`;
        console.error(e);
    }
}
provElm?.addEventListener("change", (e) => {
    kotaElm.innerHTML = `<option value="">${addressCopy.loadingCity}</option>`;
    kecElm.innerHTML = `<option value="">${addressCopy.chooseDistrict}</option>`;
    kodeElm.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
    const idprov = Number(String(e.target.value || '').split("-")[0]);
    if (idprov > 0) getKota(idprov);
});
kotaElm?.addEventListener("change", (e) => {
    kecElm.innerHTML = `<option value="">${addressCopy.loadingDistrict}</option>`;
    kodeElm.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
    const idkota = Number(String(e.target.value || '').split("-")[0]);
    if (idkota > 0) getKec(idkota);
});
kecElm?.addEventListener("change", (e) => {
    kodeElm.innerHTML = `<option value="">${addressCopy.loadingVillage}</option>`;
    const idkec = String(e.target.value || '').split("-")[0]; // kirim ID kecamatan ke endpoint
    if (idkec) getKode(idkec);
});

/* ---------- FETCH DAERAH (EDIT) ---------- */
async function getKotaEdit(idprov) {
    try {
        const res = await fetch(`/getkota/${idprov}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kotaElmEdit.innerHTML = `<option value="">${addressCopy.chooseCity}</option>`;
        kecElmEdit.innerHTML = `<option value="">${addressCopy.chooseDistrict}</option>`;
        kodeElmEdit.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
        hasil.forEach(el => {
            const id = el.city_id ?? el.id ?? el.value;
            const nama0 = el.city_name ?? el.label ?? el.name ?? '';
            const nama = String(nama0).split('/')[0];
            const type = el.type === 'Kota' ? ' Kota' : '';
            const opt = document.createElement("option");
            opt.value = id + "-" + nama;
            opt.innerHTML = el.city_name ? `${nama}${type}` : nama;
            kotaElmEdit.appendChild(opt);
        });
    } catch (e) {
        kotaElmEdit.innerHTML = `<option value="">${addressCopy.loadCityFailed}</option>`;
        console.error(e);
    }
}
async function getKecEdit(idkota) {
    try {
        const res = await fetch(`/getkec/${idkota}`);
        const json = await res.json();
        const hasil = json?.rajaongkir?.results ?? json?.results ?? json?.data?.results ?? [];
        kecElmEdit.innerHTML = `<option value="">${addressCopy.chooseDistrict}</option>`;
        kodeElmEdit.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
        hasil.forEach(el => {
            const id = el.subdistrict_id ?? el.id ?? el.value;
            const nama = String(el.subdistrict_name ?? el.label ?? el.name ?? '').split('/')[0];
            const opt = document.createElement("option");
            opt.value = id + "-" + nama; // simpan "id-nama"
            opt.innerHTML = nama;
            kecElmEdit.appendChild(opt);
        });
    } catch (e) {
        kecElmEdit.innerHTML = `<option value="">${addressCopy.loadDistrictFailed}</option>`;
        console.error(e);
    }
}
async function getKodeEdit(kecId) {
    try {
        const res = await fetch(`/getkode/${kecId}`);
        const json = await res.json();
        const hasil = json?.results ?? json?.data?.results ?? json ?? [];
        kodeElmEdit.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
        hasil.forEach(el => {
            const desa = titleCase(String(el.DesaKelurahan ?? el.desa ?? el.label ?? el.name ?? '').split(
                '/')[0]);
            const kp = String(el.KodePos ?? el.kodepos ?? el.zip ?? '');
            const opt = document.createElement("option");
            opt.value = desa + "-" + kp;
            opt.innerHTML = desa;
            kodeElmEdit.appendChild(opt);
        });
    } catch (e) {
        kodeElmEdit.innerHTML = `<option value="">${addressCopy.loadVillageFailed}</option>`;
        console.error(e);
    }
}
provElmEdit?.addEventListener("change", (e) => {
    kotaElmEdit.innerHTML = `<option value="">${addressCopy.loadingCity}</option>`;
    kecElmEdit.innerHTML = `<option value="">${addressCopy.chooseDistrict}</option>`;
    kodeElmEdit.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
    const idprov = Number(String(e.target.value || '').split("-")[0]);
    if (idprov > 0) getKotaEdit(idprov);
});
kotaElmEdit?.addEventListener("change", (e) => {
    kecElmEdit.innerHTML = `<option value="">${addressCopy.loadingDistrict}</option>`;
    kodeElmEdit.innerHTML = `<option value="">${addressCopy.chooseVillage}</option>`;
    const idkota = Number(String(e.target.value || '').split("-")[0]);
    if (idkota > 0) getKecEdit(idkota);
});
kecElmEdit?.addEventListener("change", (e) => {
    kodeElmEdit.innerHTML = `<option value="">${addressCopy.loadingVillage}</option>`;
    const idkec = String(e.target.value || '').split("-")[0]; // kirim ID kecamatan
    if (idkec) getKodeEdit(idkec);
});

/* ---------- EDIT modal bind ---------- */
function openEditModal(ind_add) {
    const a = alamatJson[ind_add] || {};
    inputEmailElm.value = a.email_pemesan || '';
    inputNamaElm.value = a.nama_penerima || '';
    inputNohpElm.value = a.nohp_penerima || '';
    formEditElm.action = storeRoutePrefix + "/editaddress/" + ind_add;
}
</script>

<?= $this->endSection(); ?>
