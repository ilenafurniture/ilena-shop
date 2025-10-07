<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
/* Scoped hanya untuk blok ini (menggunakan kombinasi class yang sudah ada) */
.w-100.h-100.mx-auto.d-flex.justify-content-center.align-items-center {
    position: absolute;
    /* tetap sesuai urutan & struktur kamu */
    inset: 0;
    /* full-bleed cover */
    padding: 16px;
    background:
        radial-gradient(1000px 420px at 10% -10%, rgba(255, 71, 71, .06), transparent 65%),
        radial-gradient(900px 420px at 120% 110%, rgba(0, 0, 0, .05), transparent 60%);
}

/* Style gambar di dalam blok ini saja */
.w-100.h-100.mx-auto.d-flex.justify-content-center.align-items-center>img {
    width: clamp(220px, 52vw, 520px);
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 10px 24px rgba(0, 0, 0, .12));
    animation: floaty 6s ease-in-out infinite;
    will-change: transform;
}

/* Mobile fine-tuning */
@media (max-width: 500px) {
    .w-100.h-100.mx-auto.d-flex.justify-content-center.align-items-center {
        padding: 12px;
    }

    .w-100.h-100.mx-auto.d-flex.justify-content-center.align-items-center>img {
        width: min(84vw, 360px);
    }
}

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
    .w-100.h-100.mx-auto.d-flex.justify-content-center.align-items-center>img {
        animation: none;
    }
}

/* Dark mode friendly */
@media (prefers-color-scheme: dark) {
    .w-100.h-100.mx-auto.d-flex.justify-content-center.align-items-center {
        background:
            radial-gradient(1000px 420px at 10% -10%, rgba(255, 71, 71, .10), transparent 65%),
            radial-gradient(900px 420px at 120% 110%, rgba(255, 255, 255, .06), transparent 60%);
    }
}

/* Animasi lembut untuk ilustrasi */
@keyframes floaty {

    0%,
    100% {
        transform: translateY(0) rotate(0deg);
    }

    50% {
        transform: translateY(-6px) rotate(-1deg);
    }
}
</style>

<div class="w-100 h-100 mx-auto d-flex justify-content-center align-items-center" style="position: absolute;">
    <img src="<?= base_url('img/error/Oooops.png'); ?>" alt="Oops, terjadi kesalahan">
</div>

<?= $this->endSection(); ?>