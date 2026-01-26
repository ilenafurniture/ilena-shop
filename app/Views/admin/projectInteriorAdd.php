<?php
// app/Views/admin/projectInteriorAdd.php
?>

<?= $this->extend('admin/template'); ?>
<?= $this->section('content'); ?>

<style>
:root {
    --merah: #b31217;
    --merah-600: #a50e12;
    --slate-100: #f1f5f9;
    --slate-200: #e5e7eb;
    --ring: rgba(255, 180, 180, .35);
}

.card-soft {
    border-radius: 16px;
    border: 1px solid var(--slate-100);
    background: #fff;
    box-shadow: 0 10px 26px rgba(2, 8, 23, .06);
    overflow: hidden
}

.card-head {
    padding: 12px 14px;
    border-bottom: 1px solid var(--slate-100);
    background: linear-gradient(180deg, #fff, #fafafa);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px
}

.card-title {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #0f172a
}

.btn-ghost {
    background: #f3f4f6;
    border: 1px solid var(--slate-200);
    padding: .55em .9em;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px
}

.btn-ghost:hover {
    background: #e5e7eb
}

.btn-default-merah {
    border: 0;
    background: linear-gradient(180deg, var(--merah), var(--merah-600));
    color: #fff;
    font-weight: 900;
    padding: .75em 1.1em;
    border-radius: 12px;
    box-shadow: 0 10px 28px rgba(165, 14, 18, .22)
}

.btn-default-merah[disabled] {
    opacity: .55;
    pointer-events: none
}

.form-control,
.form-select {
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    height: 38px;
    padding: 8px 12px;
    background: #fff;
    font-weight: 600;
    font-size: 13px
}

.form-control:focus,
.form-select:focus {
    border-color: #ffb4b4;
    box-shadow: 0 0 0 4px var(--ring);
    outline: none
}

textarea.form-control {
    height: auto;
    min-height: 86px
}

.helper {
    margin-top: 8px;
    padding: 8px 12px;
    border-radius: 10px;
    border: 1px solid #cfe7ff;
    background: #e8f4ff;
    color: #1e4e79;
    font-size: 12px;
    line-height: 1.35
}

.helper.warn {
    border-color: #ffc9c9;
    background: #ffe8e8;
    color: #8b2a2b
}

.mono {
    font-variant-numeric: tabular-nums;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace
}

.addr-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px
}

@media(max-width:992px) {
    .addr-grid {
        grid-template-columns: 1fr
    }
}
</style>

<div style="padding:2em;" class="h-100 d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-1" style="font-weight:900;margin:0;letter-spacing:-.2px;">Tambah Project Interior</h1>
            <div style="font-size:12.5px;color:#64748b;">Multi item + ringkasan DPP/PPN otomatis.</div>
        </div>
        <a class="btn-ghost" href="<?= site_url('admin/project-interior') ?>">
            <i class="material-icons" style="font-size:16px;vertical-align:-3px;">arrow_back</i> Kembali
        </a>
    </div>

    <?php if (!empty($msg)) : ?>
    <div class="helper warn"><?= esc($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/project-interior/add') ?>" id="form-interior">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card-soft">
                    <div class="card-head">
                        <div class="card-title"><i class="material-icons"
                                style="font-size:18px;color:#64748b;">assignment</i> Data Project</div>
                    </div>
                    <div class="p-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Nama Project *</label>
                                <input class="form-control" name="nama_project" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. PO (opsional)</label>
                                <input class="form-control" name="no_po" placeholder="PO-001">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Customer / Client *</label>
                                <input class="form-control" name="nama_customer" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No HP *</label>
                                <input class="form-control" name="nohp" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Faktur *</label>
                                <select class="form-select" name="jenis_faktur" required>
                                    <option value="sale" selected>SJ (Surat Jalan)</option>
                                    <option value="nf">NF (Non Faktur)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mode PPN *</label>
                                <select class="form-select" id="ppn_mode" name="ppn_mode" required>
                                    <option value="non">Tanpa PPN (0%)</option>
                                    <option value="ppn10">PPN 10%</option>
                                    <option value="ppn11" selected>PPN 11%</option>
                                </select>
                                <div class="helper">Harga item = DPP (belum termasuk PPN).</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NPWP (opsional)</label>
                                <input class="form-control" name="npwp">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama NPWP (opsional)</label>
                                <input class="form-control" name="nama_npwp">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Alamat (pakai pilihan seperti Offline)</label>
                                <div class="addr-grid">
                                    <div>
                                        <div style="font-size:12px;font-weight:900;color:#64748b;margin-bottom:6px;">
                                            Pengiriman</div>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <select class="form-select" id="prov_pengiriman">
                                                    <option value="">Pilih Provinsi</option>
                                                    <?php foreach (($provinsi ?? []) as $p): ?>
                                                    <option value="<?= esc($p['id']) ?>"><?= esc($p['label']) ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <select class="form-select" id="kota_pengiriman" disabled>
                                                    <option value="">Pilih Kota/Kab</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <select class="form-select" id="kec_pengiriman" disabled>
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <input class="form-control mono" id="kodepos_pengiriman"
                                                    placeholder="Kode pos" inputmode="numeric">
                                            </div>
                                            <div class="col-12">
                                                <textarea class="form-control" id="detail_pengiriman" rows="2"
                                                    placeholder="Detail alamat (jalan/RT/RW/no rumah)"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="d-flex justify-content-between align-items-center"
                                            style="margin-bottom:6px;">
                                            <div style="font-size:12px;font-weight:900;color:#64748b;">Tagihan</div>
                                            <label class="d-flex gap-2 align-items-center"
                                                style="font-weight:800;font-size:12.5px;">
                                                <input type="checkbox" id="same-billing"> Samakan
                                            </label>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <select class="form-select" id="prov_tagihan">
                                                    <option value="">Pilih Provinsi</option>
                                                    <?php foreach (($provinsi ?? []) as $p): ?>
                                                    <option value="<?= esc($p['id']) ?>"><?= esc($p['label']) ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <select class="form-select" id="kota_tagihan" disabled>
                                                    <option value="">Pilih Kota/Kab</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <select class="form-select" id="kec_tagihan" disabled>
                                                    <option value="">Pilih Kecamatan</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <input class="form-control mono" id="kodepos_tagihan"
                                                    placeholder="Kode pos" inputmode="numeric">
                                            </div>
                                            <div class="col-12">
                                                <textarea class="form-control" id="detail_tagihan" rows="2"
                                                    placeholder="Detail alamat (jalan/RT/RW/no rumah)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- yang dikirim ke controller -->
                                <textarea name="alamat_pengiriman" id="alamat_pengiriman" class="d-none"></textarea>
                                <textarea name="alamat_tagihan" id="alamat_tagihan" class="d-none"></textarea>

                                <div class="helper" style="margin-top:10px;">
                                    Jika dropdown kota/kec tidak muncul, biasanya karena endpoint <span
                                        class="mono">/getkota</span> dll tidak bisa diakses dari admin (filter). Bilang
                                    ya, nanti aku buat route admin khusus.
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Catatan (opsional)</label>
                                <textarea class="form-control" rows="2" name="catatan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-soft mt-3">
                    <div class="card-head">
                        <div class="card-title"><i class="material-icons"
                                style="font-size:18px;color:#64748b;">inventory_2</i> Item Project (Multi Item)</div>
                        <button type="button" class="btn-ghost" id="btn-add-item">
                            <i class="material-icons" style="font-size:16px;vertical-align:-3px;">add</i> Tambah Item
                        </button>
                    </div>

                    <div class="p-3">
                        <div class="table-responsive">
                            <table class="table table-sm mb-2" id="items-table">
                                <thead>
                                    <tr>
                                        <th style="width:140px;">Kode *</th>
                                        <th>Nama Item *</th>
                                        <th style="width:150px" class="text-end">Harga Satuan (DPP) *</th>
                                        <th style="width:90px" class="text-center">Qty *</th>
                                        <th style="width:160px" class="text-end">Subtotal</th>
                                        <th style="width:54px"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body"></tbody>
                            </table>
                        </div>

                        <div class="helper">Qty total per item akan dikirim bertahap lewat SJ (per item).</div>
                        <input type="hidden" name="items_json" id="items_json" value="[]">
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-soft">
                    <div class="card-head">
                        <div class="card-title"><i class="material-icons"
                                style="font-size:18px;color:#64748b;">payments</i> Ringkasan &amp; Pajak</div>
                    </div>
                    <div class="p-3">
                        <div class="d-flex justify-content-between"><span>Subtotal DPP</span><b class="mono"
                                id="pv_dpp">Rp 0</b></div>
                        <div class="d-flex justify-content-between mt-1"><span>PPN</span><b class="mono" id="pv_ppn">Rp
                                0</b></div>
                        <div style="height:1px;background:var(--slate-100);margin:10px 0"></div>
                        <div class="d-flex justify-content-between"><span style="font-weight:900">Grand Total</span><b
                                class="mono" id="pv_grand" style="font-size:18px">Rp 0</b></div>

                        <div class="helper warn mt-2" id="warn" style="display:none">Item masih kosong / ada data tidak
                            valid.</div>

                        <div class="mt-3">
                            <button class="btn-default-merah w-100" id="btn-submit" type="submit">Simpan
                                Project</button>
                        </div>

                        <div class="helper" style="margin-top:10px;">
                            Alur: Create Project → input DP (minimal) → buat SJ Draft per item → finalize SJ per
                            pengiriman → Lunas → buat Invoice Akhir.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
(function() {
    const toNum = (v) => Number(String(v || '').replace(/[^\d]/g, '') || 0);
    const rupiah = (n) => 'Rp ' + (Number(n || 0)).toLocaleString('id-ID');

    // =========================
    // PPN
    // =========================
    const ppnMode = document.getElementById('ppn_mode');

    function ppnRate() {
        const m = (ppnMode?.value || 'non').toLowerCase();
        if (m === 'ppn10') return 10;
        if (m === 'ppn11') return 11;
        return 0;
    }

    // =========================
    // ADDRESS
    // =========================
    const outPeng = document.getElementById('alamat_pengiriman');
    const outTag = document.getElementById('alamat_tagihan');
    const sameBilling = document.getElementById('same-billing');

    const provPeng = document.getElementById('prov_pengiriman');
    const kotaPeng = document.getElementById('kota_pengiriman');
    const kecPeng = document.getElementById('kec_pengiriman');
    const kpPeng = document.getElementById('kodepos_pengiriman');
    const detPeng = document.getElementById('detail_pengiriman');

    const provTag = document.getElementById('prov_tagihan');
    const kotaTag = document.getElementById('kota_tagihan');
    const kecTag = document.getElementById('kec_tagihan');
    const kpTag = document.getElementById('kodepos_tagihan');
    const detTag = document.getElementById('detail_tagihan');

    function setOpts(sel, items, placeholder) {
        sel.innerHTML = '';
        const o0 = document.createElement('option');
        o0.value = '';
        o0.textContent = placeholder;
        sel.appendChild(o0);
        (items || []).forEach(it => {
            const o = document.createElement('option');
            o.value = String(it.id ?? it.kode ?? '');
            o.textContent = String(it.nama ?? it.label ?? it.name ?? '-');
            sel.appendChild(o);
        });
    }
    async function fetchJson(url) {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return await res.json();
    }
    async function loadKota(provId, kotaSel, kecSel) {
        if (!provId) {
            kotaSel.disabled = true;
            setOpts(kotaSel, [], 'Pilih Kota/Kab');
            kecSel.disabled = true;
            setOpts(kecSel, [], 'Pilih Kecamatan');
            return;
        }
        kotaSel.disabled = true;
        setOpts(kotaSel, [], 'Loading...');
        kecSel.disabled = true;
        setOpts(kecSel, [], 'Pilih Kecamatan');
        const data = await fetchJson('<?= base_url('getkota') ?>/' + encodeURIComponent(provId));
        const rows = Array.isArray(data) ? data : (data.kota || data.data || []);
        setOpts(kotaSel, rows, 'Pilih Kota/Kab');
        kotaSel.disabled = false;
    }
    async function loadKec(kotaId, kecSel) {
        if (!kotaId) {
            kecSel.disabled = true;
            setOpts(kecSel, [], 'Pilih Kecamatan');
            return;
        }
        kecSel.disabled = true;
        setOpts(kecSel, [], 'Loading...');
        const data = await fetchJson('<?= base_url('getkec') ?>/' + encodeURIComponent(kotaId));
        const rows = Array.isArray(data) ? data : (data.kec || data.data || []);
        setOpts(kecSel, rows, 'Pilih Kecamatan');
        kecSel.disabled = false;
    }
    async function loadKodePos(kecId, kpInput) {
        if (!kecId) {
            kpInput.value = '';
            return;
        }
        const data = await fetchJson('<?= base_url('getkode') ?>/' + encodeURIComponent(kecId));
        let kp = '';
        if (typeof data === 'string' || typeof data === 'number') kp = String(data);
        else if (Array.isArray(data) && data.length) kp = String(data[0].kodepos ?? data[0].kode_pos ?? '');
        else kp = String(data.kodepos ?? data.kode_pos ?? (data.data?.kodepos ?? data.data?.kode_pos ?? ''));
        kpInput.value = kp || '';
    }

    function pickText(sel) {
        return sel?.options?. [sel.selectedIndex]?.text ? String(sel.options[sel.selectedIndex].text) : '';
    }

    function compose(provSel, kotaSel, kecSel, kpEl, detEl) {
        const det = (detEl?.value || '').trim();
        const prov = pickText(provSel),
            kota = pickText(kotaSel),
            kec = pickText(kecSel);
        const kp = (kpEl?.value || '').trim();
        const lines = [];
        if (det) lines.push(det);
        const line2 = [kec, kota, prov].filter(Boolean).join(', ');
        if (line2) lines.push(line2);
        if (kp) lines.push('Kode Pos: ' + kp);
        return lines.join('\n');
    }

    function syncAddr() {
        if (outPeng) outPeng.value = compose(provPeng, kotaPeng, kecPeng, kpPeng, detPeng);
        if (outTag) {
            outTag.value = sameBilling?.checked ? (outPeng?.value || '') : compose(provTag, kotaTag, kecTag, kpTag,
                detTag);
        }
    }

    async function mirrorPengToTag() {
        if (!sameBilling?.checked) return;
        provTag.value = provPeng.value || '';
        await loadKota(provTag.value, kotaTag, kecTag);
        kotaTag.value = kotaPeng.value || '';
        await loadKec(kotaTag.value, kecTag);
        kecTag.value = kecPeng.value || '';
        await loadKodePos(kecTag.value, kpTag);
        kpTag.value = kpPeng.value || '';
        detTag.value = detPeng.value || '';
        [provTag, kotaTag, kecTag, kpTag, detTag].forEach(el => {
            if (el) el.disabled = true;
        });
        syncAddr();
    }

    function unlockTag() {
        [provTag, kotaTag, kecTag, kpTag, detTag].forEach(el => {
            if (el) el.disabled = false;
        });
    }

    if (provPeng) {
        provPeng.addEventListener('change', async () => {
            try {
                await loadKota(provPeng.value, kotaPeng, kecPeng);
            } catch (e) {
                console.warn(e);
            }
            syncAddr();
            await mirrorPengToTag();
        });
    }
    if (kotaPeng) {
        kotaPeng.addEventListener('change', async () => {
            try {
                await loadKec(kotaPeng.value, kecPeng);
            } catch (e) {
                console.warn(e);
            }
            syncAddr();
            await mirrorPengToTag();
        });
    }
    if (kecPeng) {
        kecPeng.addEventListener('change', async () => {
            try {
                await loadKodePos(kecPeng.value, kpPeng);
            } catch (e) {
                console.warn(e);
            }
            syncAddr();
            await mirrorPengToTag();
        });
    }
    [kpPeng, detPeng].forEach(el => el && el.addEventListener('input', async () => {
        syncAddr();
        await mirrorPengToTag();
    }));

    if (sameBilling) {
        sameBilling.addEventListener('change', async () => {
            if (sameBilling.checked) await mirrorPengToTag();
            else {
                unlockTag();
                syncAddr();
            }
        });
    }
    if (provTag) {
        provTag.addEventListener('change', async () => {
            if (sameBilling?.checked) return;
            try {
                await loadKota(provTag.value, kotaTag, kecTag);
            } catch (e) {}
            syncAddr();
        });
    }
    if (kotaTag) {
        kotaTag.addEventListener('change', async () => {
            if (sameBilling?.checked) return;
            try {
                await loadKec(kotaTag.value, kecTag);
            } catch (e) {}
            syncAddr();
        });
    }
    if (kecTag) {
        kecTag.addEventListener('change', async () => {
            if (sameBilling?.checked) return;
            try {
                await loadKodePos(kecTag.value, kpTag);
            } catch (e) {}
            syncAddr();
        });
    }
    [kpTag, detTag].forEach(el => el && el.addEventListener('input', () => {
        if (!sameBilling?.checked) syncAddr();
    }));

    syncAddr();

    // =========================
    // ITEMS + SUMMARY
    // =========================
    const body = document.getElementById('items-body');
    const btnAdd = document.getElementById('btn-add-item');
    const itemsJson = document.getElementById('items_json');
    const pvDpp = document.getElementById('pv_dpp');
    const pvPpn = document.getElementById('pv_ppn');
    const pvGrand = document.getElementById('pv_grand');
    const warn = document.getElementById('warn');
    const btnSubmit = document.getElementById('btn-submit');

    function rowTpl() {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td><input class="form-control mono" placeholder="KODE" data-k="kode" required></td>
      <td><input class="form-control" placeholder="Nama item" data-k="nama" required></td>
      <td class="text-end"><input class="form-control mono text-end" placeholder="0" data-k="harga" inputmode="numeric"></td>
      <td class="text-center"><input class="form-control mono text-center" placeholder="1" data-k="qty" value="1" inputmode="numeric"></td>
      <td class="text-end mono"><span data-k="sub">Rp 0</span></td>
      <td class="text-end"><button type="button" class="btn-ghost" data-act="del" title="Hapus">✕</button></td>
    `;
        return tr;
    }

    function getItems() {
        const rows = [...body.querySelectorAll('tr')];
        return rows.map(tr => {
            const kode = (tr.querySelector('[data-k="kode"]')?.value || '').trim();
            const nama = (tr.querySelector('[data-k="nama"]')?.value || '').trim();
            const harga = toNum(tr.querySelector('[data-k="harga"]')?.value || '');
            let qty = parseInt(tr.querySelector('[data-k="qty"]')?.value || '0', 10);
            if (!qty || qty < 0) qty = 0;
            return {
                kode_barang: kode,
                nama_barang: nama,
                harga_satuan: harga,
                qty: qty,
                subtotal: harga * qty
            };
        });
    }

    function syncSummary() {
        const items = getItems();
        [...body.querySelectorAll('tr')].forEach((tr, i) => {
            const sub = items[i]?.subtotal || 0;
            const el = tr.querySelector('[data-k="sub"]');
            if (el) el.textContent = rupiah(sub);
        });

        const dpp = items.reduce((a, b) => a + (b.subtotal || 0), 0);
        const ppn = Math.round(dpp * ppnRate() / 100);
        const grand = dpp + ppn;

        pvDpp.textContent = rupiah(dpp);
        pvPpn.textContent = rupiah(ppn);
        pvGrand.textContent = rupiah(grand);

        itemsJson.value = JSON.stringify(items);

        const valid = items.length > 0 && items.every(x => x.kode_barang && x.nama_barang && x.harga_satuan > 0 && x
            .qty > 0);
        warn.style.display = valid ? 'none' : 'block';
        btnSubmit.disabled = !valid;
        btnSubmit.style.opacity = valid ? '1' : '.6';
    }

    function addRow(prefill) {
        const tr = rowTpl();
        body.appendChild(tr);

        if (prefill && typeof prefill === 'object') {
            tr.querySelector('[data-k="kode"]').value = (prefill.kode_barang || '');
            tr.querySelector('[data-k="nama"]').value = (prefill.nama_barang || '');
            tr.querySelector('[data-k="harga"]').value = String(toNum(prefill.harga_satuan || 0));
            tr.querySelector('[data-k="qty"]').value = String(parseInt(prefill.qty || 1, 10) || 1);
        }

        tr.addEventListener('input', (e) => {
            if (!e.target || !e.target.dataset) return;
            if (e.target.dataset.k === 'harga' || e.target.dataset.k === 'qty') {
                e.target.value = String(toNum(e.target.value));
            }
            syncSummary();
        });

        tr.querySelector('[data-act="del"]').addEventListener('click', () => {
            tr.remove();
            syncSummary();
        });

        syncSummary();
    }

    btnAdd && btnAdd.addEventListener('click', () => addRow());
    ppnMode && ppnMode.addEventListener('change', syncSummary);

    // 1 row awal
    addRow();

    // sebelum submit: pastikan alamat ter-compose
    const form = document.getElementById('form-interior');
    form && form.addEventListener('submit', () => {
        syncAddr();
        if (sameBilling?.checked && outPeng && outTag) outTag.value = outPeng.value;
    });

    syncSummary();
})();
</script>

<?= $this->endSection(); ?>