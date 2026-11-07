<?php $activeAccountTab = $activeAccountTab ?? ''; ?>
<aside class="tigapuluh-ke-seratus ilena-customer-account-sidebar">
    <nav class="card p-4 ilena-account-nav" aria-label="<?= esc(lang('Store.accountTitle'), 'attr'); ?>">
        <h2><?= esc(lang('Store.accountTitle')); ?></h2>
        <span class="garis my-2" aria-hidden="true"></span>
        <a href="<?= esc(ilena_store_path('/account'), 'attr'); ?>"
            class="btn-default-abu<?= $activeAccountTab === 'profile' ? ' is-active' : ''; ?>"
            <?= $activeAccountTab === 'profile' ? 'aria-current="page"' : ''; ?>>
            <span class="material-icons" aria-hidden="true">person</span><?= esc(lang('Store.profileNav')); ?>
        </a>
        <a href="<?= esc(ilena_store_path('/order'), 'attr'); ?>"
            class="btn-default-abu<?= $activeAccountTab === 'orders' ? ' is-active' : ''; ?>"
            <?= $activeAccountTab === 'orders' ? 'aria-current="page"' : ''; ?>>
            <span class="material-icons" aria-hidden="true">receipt_long</span><?= esc(lang('Store.ordersNav')); ?>
        </a>
        <a href="<?= esc(ilena_store_path('/wishlist'), 'attr'); ?>"
            class="btn-default-abu<?= $activeAccountTab === 'wishlist' ? ' is-active' : ''; ?>"
            <?= $activeAccountTab === 'wishlist' ? 'aria-current="page"' : ''; ?>>
            <span class="material-icons" aria-hidden="true">favorite_border</span><?= esc(lang('Store.wishlist')); ?>
        </a>
        <form action="<?= esc(ilena_store_path('/logout'), 'attr'); ?>" method="post">
            <?= csrf_field(); ?>
            <button type="submit" class="btn-default-merah"><span class="material-icons" aria-hidden="true">logout</span><?= esc(lang('Store.logout')); ?></button>
        </form>
    </nav>
</aside>
