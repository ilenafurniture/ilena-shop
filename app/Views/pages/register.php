<?= $this->extend("layout/template"); ?>
<?= $this->section("content"); ?>

<style>
.hp-wrap {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0 0 0 0) !important;
    clip-path: inset(50%) !important;
    border: 0 !important;
    white-space: nowrap !important;
}

#ab-msg {
    font-size: 12px;
    color: #b91c1c;
    display: none;
}

#ab-msg.show {
    display: block;
}
</style>

<div class="container d-flex justify-content-center align-items-center">
    <div class="konten d-flex justify-content-center align-items-center">
        <div class="card p-4 limapuluh-ke-seratus">
            <h1 class="mb-3 teks-sedang">Register Akun</h1>

            <?php if ($val['msg']) { ?>
            <div class="pemberitahuan my-1" role="alert">
                <?= $val['msg']; ?>
            </div>
            <?php } ?>

            <form id="registerForm" action="/actionregister" method="post" autocomplete="off">
                <!-- Honeypot (TANPA mengganggu backend). Jika bot isi ini, submit akan diblok di JS -->
                <div class="hp-wrap" aria-hidden="true">
                    <label>Website
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </label>
                    <label>Referral
                        <input type="text" name="ref_code" tabindex="-1" autocomplete="off">
                    </label>
                    <!-- token & timestamp non-krusial (front-end only) -->
                    <input type="text" name="abt_token" tabindex="-1" autocomplete="off">
                    <input type="text" name="abt_elapsed" tabindex="-1" autocomplete="off">
                </div>

                <div class="mb-3">
                    <label for="fullname" class="form-label">Nama Lengkap</label>
                    <input name="nama" type="text" class="form-control <?= ($val['val_nama']) ? "is-invalid" : ""; ?>"
                        placeholder="Masukkan Nama Lengkap" value="<?= old('nama'); ?>">
                    <div class="invalid-feedback">Mohon masukkan nama lengkap.</div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input name="email" type="email"
                        class="form-control <?= ($val['val_email']) ? "is-invalid" : ""; ?>"
                        placeholder="Masukkan Email" value="<?= old('email'); ?>">
                    <div class="invalid-feedback">Email tidak valid atau telah terdaftar.</div>
                </div>

                <div class="mb-3">
                    <label for="nohp" class="form-label">Nomor Telepon</label>
                    <input name="nohp" type="text" class="form-control <?= ($val['val_nohp']) ? "is-invalid" : ""; ?>"
                        placeholder="Masukkan Nomor Telepon" value="<?= old('nohp'); ?>">
                    <div class="invalid-feedback">Nomor telepon tidak valid atau telah terdaftar.</div>
                </div>

                <label for="sandi" class="form-label">Kata Sandi</label>
                <div class="input-group mb-3 has-validation">
                    <input name="sandi" type="password"
                        class="form-control <?= ($val['val_sandi']) ? "is-invalid" : ""; ?>" id="password"
                        placeholder="Masukkan Kata Sandi">
                    <span class="input-group-text d-flex justify-content-center align-items-center"
                        onclick="togglePassword(event)">
                        <i class="material-icons"
                            style="cursor: default; -webkit-user-select: none; -ms-user-select: none; user-select: none;">remove_red_eye</i>
                    </span>
                    <div class="invalid-feedback">Mohon masukkan kata sandi.</div>
                </div>

                <div class="d-flex gap-2 mb-3">
                    <input type="checkbox" id="validation-syarat" name="validasi-syarat" required>
                    <label for="validation-syarat" class="m-0">Dengan ini Anda menyetujui syarat dan ketentuan
                        pendaftaran.</label>
                </div>

                <div class="mb-3 d-flex w-100 justify-content-center">
                    <button id="btnSubmit" type="submit" class="btn btn-default btn-block" disabled>Register</button>
                </div>
                <div id="ab-msg" aria-live="polite"></div>

                <div>
                    <p class="text-center">Sudah punya akun?
                        <a href="/login" class="btn-teks-aja" style="display: inline;">Masuk sekarang</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(e) {
    var passwordField = document.querySelector('input[name="sandi"]');
    if (passwordField.type === "password") {
        passwordField.type = "text";
        e.target.style.color = 'var(--merah)'
    } else {
        passwordField.type = "password";
        e.target.style.color = 'black'
    }
}
(function() {
    const form = document.getElementById('registerForm');
    const btn = document.getElementById('btnSubmit');
    const msg = document.getElementById('ab-msg');
    const hp1 = form.querySelector('input[name="website"]');
    const hp2 = form.querySelector('input[name="ref_code"]');
    const tkn = form.querySelector('input[name="abt_token"]');
    const elps = form.querySelector('input[name="abt_elapsed"]');

    const start = Date.now();
    const minWaitMs = 4000;
    let interacted = false;
    let enabled = false;
    tkn.value = Math.random().toString(36).slice(2) + Date.now().toString(36);
    const markInteract = () => {
        interacted = true;
        maybeEnable();
    };
    form.addEventListener('mousemove', markInteract, {
        once: true
    });
    form.addEventListener('touchstart', markInteract, {
        once: true
    });
    form.addEventListener('keydown', markInteract, {
        once: true
    });
    form.addEventListener('scroll', markInteract, {
        once: true
    });

    function maybeEnable() {
        if (enabled) return;
        const waited = Date.now() - start;
        if (interacted && waited >= minWaitMs) {
            btn.removeAttribute('disabled');
            enabled = true;
            showMsg('');
        }
    }
    const iv = setInterval(() => {
        maybeEnable();
        if (enabled) clearInterval(iv);
    }, 250);

    function showMsg(text) {
        if (!text) {
            msg.classList.remove('show');
            msg.textContent = '';
            return;
        }
        msg.textContent = text;
        msg.classList.add('show');
    }
    const throttleKey = 'ab-reg-throttle';
    try {
        const last = Number(localStorage.getItem(throttleKey) || '0');
        if (Date.now() - last < 5000) {
            showMsg('Tunggu sebentar sebelum mencoba lagi.');
        }
    } catch (e) {}

    form.addEventListener('submit', function(e) {
        elps.value = String(Date.now() - start);
        if (hp1.value.trim() !== '' || hp2.value.trim() !== '') {
            e.preventDefault();
            showMsg('Pendaftaran diblokir. (indikasi bot)');
            return;
        }
        if (Date.now() - start < minWaitMs) {
            e.preventDefault();
            showMsg('Terlalu cepat. Mohon tunggu beberapa detik sebelum mengirim.');
            return;
        }
        if (!interacted) {
            e.preventDefault();
            showMsg('Mohon berinteraksi dengan form sebelum mengirim.');
            return;
        }
        if (btn.dataset.submitted === '1') {
            e.preventDefault();
            showMsg('Permintaan sedang diproses…');
            return;
        }
        btn.dataset.submitted = '1';
        btn.setAttribute('disabled', 'disabled');
        btn.textContent = 'Memproses…';

        try {
            localStorage.setItem(throttleKey, String(Date.now()));
        } catch (e) {}
    });
})();
</script>

<?= $this->endSection(); ?>