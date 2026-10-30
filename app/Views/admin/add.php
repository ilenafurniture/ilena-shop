<!-- app/Views/admin/add.php -->
<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
/* ====== Layout & Cards ====== */
.page-wrap {
    max-width: 1480px;
    margin: 0 auto;
    padding: clamp(16px, 2.2vw, 32px);
}

.form-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 16px;
}

/* Edit form: make the long product workflow scannable without changing its data flow. */
.edit-guide {
    margin: 4px 0 20px;
    padding: 14px 0 12px;
    border-top: 1px solid #e8e5e1;
    border-bottom: 1px solid #e8e5e1;
}
.edit-guide p { margin: 0 0 10px; color: #6b6259; font-size: 13px; }
.edit-guide__steps { display: flex; flex-wrap: wrap; gap: 8px; }
.edit-guide__steps a {
    display: inline-flex; align-items: center; gap: 7px; min-height: 38px;
    padding: 7px 12px; border: 1px solid #e7e2dc; border-radius: 999px;
    color: #302a24; background: #fff; text-decoration: none; font-size: 13px;
    transition: border-color .18s ease, background .18s ease, color .18s ease;
}
.edit-guide__steps a:hover, .edit-guide__steps a:focus-visible {
    border-color: #9a7955; background: #f8f5f1; color: #654a2d; outline: none;
}
.edit-guide__steps b { color: #9a7955; font-size: 11px; }
.edit-anchor { scroll-margin-top: 96px; }

@media (max-width: 992px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}

.card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, .035);
    border: 1px solid #edf0f3;
}

.card-header {
    padding: 15px 18px;
    border-bottom: 1px solid rgba(0, 0, 0, .06);
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    letter-spacing: .2px;
}

.card-body {
    padding: 16px 18px;
}

.section-title {
    margin: 18px 0 10px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #6b7280;
    font-weight: 700;
}

/* ====== Table-ish inputs ====== */
.table-input {
    width: 100%;
    border-collapse: collapse;
}

.table-input td {
    padding: 8px 0;
    vertical-align: top;
}

.table-input td:first-child {
    width: 36%;
    color: #374151;
    font-weight: 500;
    padding-right: 14px;
}

.form-control,
.form-select,
.input-group-text,
.btn-default,
.btn-default-merah,
.btn-teks-aja {
    border-radius: 12px !important;
}

.form-control,
.form-select {
    border: 1px solid #e5e7eb;
    transition: box-shadow .2s, border-color .2s;
}

.form-control:focus,
.form-select:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, .1);
}

.input-group {
    display: flex;
    align-items: stretch;
}

.input-group .form-control {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.input-group .input-group-text {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-left: 0;
    display: inline-flex;
    align-items: center;
    padding: 0 12px;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}

/* ====== Buttons ====== */
.btn-row {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-default {
    background: #111827;
    color: #fff;
    border: none;
    padding: 10px 16px;
    font-weight: 600;
    transition: transform .05s ease, opacity .2s ease;
}

.btn-default:hover {
    opacity: .92;
}

.btn-default:active {
    transform: translateY(1px);
}

.btn-default[disabled] {
    opacity: .6;
    cursor: not-allowed;
}

.btn-default-merah {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 8px 12px;
    font-weight: 600;
}

.btn-teks-aja {
    padding: 6px 8px;
    border: none;
    background: transparent;
    color: #ef4444;
    font-weight: 600;
}

.page-title {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: .3px;
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ====== Images & Variants ====== */
.container-gambar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.item-gambar {
    position: relative;
    width: 96px;
    height: 96px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    cursor: pointer;
    transition: transform .15s;
}

.item-gambar:hover {
    transform: translateY(-2px);
}

.item-gambar.selected {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .18);
}

.item-gambar.dragging {
    opacity: .55;
}

.item-gambar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.item-gambar p {
    position: absolute;
    top: 6px;
    right: 6px;
    margin: 0;
    background: rgba(17, 24, 39, .85);
    color: #fff;
    font-weight: 700;
    border-radius: 10px;
    padding: 2px 8px;
    font-size: 12px;
    opacity: 0;
    transition: opacity .2s;
}

.item-gambar:hover p {
    opacity: 1;
}

.item-gambar .img-no {
    position: absolute;
    left: 6px;
    top: 6px;
    background: rgba(255, 255, 255, .92);
    color: #111827;
    border-radius: 999px;
    padding: 2px 7px;
    font-size: 11px;
    font-weight: 800;
    box-shadow: 0 1px 4px rgba(15, 23, 42, .18);
}

.item-gambar .quick-delete {
    position: absolute;
    right: 6px;
    bottom: 6px;
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 999px;
    background: rgba(220, 38, 38, .92);
    color: #fff;
    font-size: 16px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    cursor: pointer;
    transition: opacity .15s, transform .15s;
}

.item-gambar:hover .quick-delete,
.item-gambar:focus-within .quick-delete,
.item-gambar.selected .quick-delete {
    opacity: 1;
}

.item-gambar .quick-delete:hover {
    transform: scale(1.06);
}

.image-actions {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 6px;
    margin-top: 6px;
}

.image-actions button,
.image-actions label {
    min-height: 44px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: #374151;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.image-actions button:hover,
.image-actions label:hover {
    border-color: #3b82f6;
    color: #2563eb;
}

.image-actions .danger {
    color: #dc2626;
}

.image-actions button:disabled {
    opacity: .45;
    cursor: not-allowed;
}

.missing-image-state {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    text-align: center;
    color: #7f1d1d;
    background: #fff7f7;
    font-size: 12px;
    font-weight: 700;
}

.missing-image-state[hidden] {
    display: none !important;
}

.image-order-note {
    margin: 0 0 10px;
    color: #64748b;
    font-size: 12px;
}

.image-workflow-note { margin: 0 0 14px; padding: 10px 12px; border: 1px solid #eee4d7; border-radius: 10px; background: #fbf8f3; color: #655647; font-size: 12px; line-height: 1.55; }
.image-workflow-note strong { color:#34291f; }
.image-workflow-note ol { margin:5px 0 0; padding-left:18px; }
.image-tile { transition: transform .16s ease, opacity .16s ease, box-shadow .16s ease; }
.image-tile.is-drop-target { transform: translateY(-3px); box-shadow: 0 0 0 2px rgba(164,126,84,.45); border-radius: 12px; }
.image-tile.is-dragging { opacity:.42; }
.drag-handle { min-height:32px; display:flex; align-items:center; justify-content:center; gap:5px; color:#76624c; font-size:11px; font-weight:700; cursor:grab; touch-action:manipulation; user-select:none; }
.drag-handle:active { cursor:grabbing; }
.drag-handle .material-icons { font-size:16px; }
.cover-chip { position:absolute; z-index:2; left:6px; bottom:6px; padding:3px 7px; border-radius:999px; background:rgba(31,41,55,.88); color:#fff; font-size:10px; font-weight:800; }
.image-order-status { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }

.variant-tools {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
}

.variant-help {
    color: #64748b;
    font-size: 12px;
    line-height: 1.45;
}

.image-tile {
    width: 112px;
}

.image-tile input[type=file] {
    display: none;
}

.add-thumb {
    width: 96px;
    height: 96px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1px dashed #cbd5e1;
    color: #64748b;
    background: #f8fafc;
    font-weight: 700;
    cursor: pointer;
    transition: border-color .2s, color .2s, transform .15s;
}

.add-thumb:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-2px);
}

.preview-hover {
    width: 88px;
    height: 88px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
}

.media-help {
    margin: 0 0 10px;
    color: #64748b;
    font-size: 12px;
}

@media (max-width: 640px) {
    .page-wrap { padding: 14px 12px 88px; }
    .edit-guide { margin-bottom: 14px; }
    .edit-guide__steps { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 4px; }
    .edit-guide__steps a { flex: 0 0 auto; }
    .card-header, .card-body { padding: 14px; }
    .table-input td { display: block; width: 100% !important; padding: 4px 0; }
    .table-input td:first-child { padding: 8px 0 2px; }
    .image-tile { width: 108px; }
    .item-gambar { width: 100px; height: 100px; }
    .image-actions { gap: 4px; }
    .image-actions button, .image-actions label { min-height: 42px; padding: 3px; }
}

/* ====== Chips ====== */
.chk-row {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.chk {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #fff;
    cursor: pointer;
    user-select: none;
}

.chk input {
    accent-color: #111827;
}

/* ====== Sticky bottom actions ====== */
.bottom-bar {
    position: sticky;
    bottom: 0;
    margin-top: 18px;
    padding: 12px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #fff 30%);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* ====== Loading Overlay ====== */
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, .45);
    backdrop-filter: blur(2px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-overlay.show {
    display: flex;
}

.loading-card {
    background: #111827;
    color: #fff;
    padding: 20px;
    border-radius: 14px;
    display: flex;
    flex-direction:column;
    align-items: center;
    gap: 12px;
    width:min(390px,calc(100vw - 32px));
    justify-content: center;
    text-align:center;
}

.spinner {
    width: 18px;
    height: 18px;
    border: 3px solid rgba(255, 255, 255, .25);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
.save-progress { width:min(340px,calc(100vw - 68px)); height:7px; overflow:hidden; border-radius:999px; background:rgba(255,255,255,.18); }
.save-progress__fill { display:block; height:100%; border-radius:inherit; background:#d8b98e; transition:width .2s ease; }
.save-progress__fill.is-indeterminate { width:38%; animation:save-progress-slide 1.15s ease-in-out infinite alternate; }
@keyframes save-progress-slide { from { transform:translateX(0); } to { transform:translateX(165%); } }
.image-resize-progress { margin:12px 0 16px; padding:12px 14px; border:1px solid #e8dfd3; border-radius:10px; background:#fffdf9; color:#514438; }
.image-resize-progress__top { display:flex; align-items:center; justify-content:space-between; gap:12px; font-size:12px; font-weight:700; }
.image-resize-progress__label { display:inline-flex; align-items:center; gap:8px; }
.image-resize-progress__spinner { width:14px; height:14px; flex:0 0 14px; border:2px solid #e4ddd4; border-top-color:#8b6747; border-radius:50%; animation:spin .8s linear infinite; }
.image-resize-progress__track { height:6px; margin:8px 0 6px; overflow:hidden; border-radius:999px; background:#eee9e2; }
.image-resize-progress__fill { height:100%; border-radius:inherit; background:#9b7653; transition:width .2s ease; }
.image-resize-progress small { display:block; color:#75695d; font-size:11px; line-height:1.45; }
.resize-live-toast { position:fixed; z-index:990; right:22px; bottom:22px; width:min(390px,calc(100vw - 32px)); padding:15px 17px 14px; border:1px solid #e6dccb; border-radius:12px; background:rgba(255,253,249,.97); color:#3e352b; box-shadow:0 18px 48px rgba(40,32,23,.17); animation:resize-toast-in .28s cubic-bezier(.2,.8,.2,1) both; }
.resize-live-toast__head { display:flex; align-items:center; gap:12px; }
.resize-live-toast__icon { position:relative; display:grid; place-items:center; width:42px; height:42px; flex:0 0 42px; border-radius:50%; color:#fff; background:#66583f; }
.resize-live-toast__icon:before,.resize-live-toast__icon:after { content:""; position:absolute; inset:-5px; border:1px solid rgba(162,142,107,.5); border-radius:50%; animation:resize-orbit 2.2s ease-out infinite; }
.resize-live-toast__icon:after { animation-delay:1.1s; }
.resize-live-toast__icon i { font-size:20px; animation:resize-camera 1.8s ease-in-out infinite; }
.resize-live-toast__copy { min-width:0; }
.resize-live-toast__title { display:block; color:#332b22; font-size:13px; font-weight:700; line-height:1.35; }
.resize-live-toast__message { display:block; margin-top:3px; color:#776957; font-size:12px; line-height:1.45; }
.resize-live-toast__filename { overflow:hidden; margin:9px 0 0 54px; color:#8b7959; font-size:11px; text-overflow:ellipsis; white-space:nowrap; }
.resize-live-toast__track { position:relative; height:5px; margin:10px 0 0 54px; overflow:hidden; border-radius:999px; background:#ebe4d8; }
.resize-live-toast__fill { position:relative; height:100%; border-radius:inherit; background:linear-gradient(90deg,#8b7959,#c5a777); transition:width .35s ease; }
.resize-live-toast__fill.is-working:after { content:""; position:absolute; inset:0; background:linear-gradient(90deg,transparent,rgba(255,255,255,.75),transparent); transform:translateX(-100%); animation:resize-shimmer 1.15s ease-in-out infinite; }
@keyframes resize-toast-in { from { opacity:0; transform:translateY(9px) scale(.985); } to { opacity:1; transform:translateY(0) scale(1); } }
@keyframes resize-orbit { 0% { transform:scale(.82); opacity:.7; } 100% { transform:scale(1.45); opacity:0; } }
@keyframes resize-camera { 0%,100% { transform:rotate(-7deg) scale(.96); } 50% { transform:rotate(7deg) scale(1.08); } }
@keyframes resize-shimmer { to { transform:translateX(100%); } }
@media(max-width:640px){ .resize-live-toast { right:12px; bottom:12px; width:calc(100vw - 24px); padding:13px 14px; } }
@media(prefers-reduced-motion:reduce){ .resize-live-toast,.resize-live-toast__icon:before,.resize-live-toast__icon:after,.resize-live-toast__icon i,.resize-live-toast__fill.is-working:after { animation:none; } .resize-live-toast__fill { transition:none; } }
@media (prefers-reduced-motion:reduce) { .save-progress__fill,.image-resize-progress__fill { transition:none; } .save-progress__fill.is-indeterminate { animation:none; width:70%; } .image-resize-progress__spinner,.spinner { animation:none; } }

/* Product editor: compact editorial layout, aligned with Ilena admin styling. */
.page-wrap { max-width: 1420px; color:#302d25; font-family:Inter,"Segoe UI",Arial,sans-serif; font-size:13.5px; }
.page-wrap > .page-title { font-size:25px; font-weight:620; letter-spacing:-.035em; color:#29271f; margin-bottom:7px; }
.edit-page-intro { color:#77736c!important; font-size:13px!important; margin:0 0 18px!important; }
.form-grid { gap:26px; align-items:start; }
.card { border:0; border-radius:0; background:transparent; box-shadow:none; }
.form-grid > .card { padding:0; }
.form-grid > .card + .card { padding-left:28px; border-left:1px solid #eae5db; }
.card-header { padding:0 0 10px; border:0; border-bottom:1px solid #ded9cf; background:transparent; }
.card-header h2 { color:#302d25; font-size:16px; font-weight:650; letter-spacing:-.015em; }
.card-body { padding:11px 0 16px; }
#container-varian > .card { padding:12px 0 15px; border:0!important; border-top:1px solid #eae5db!important; }
#container-varian > .card:first-child { padding-top:0; border-top:0!important; }
#container-varian > .card .card-body { padding:0; }
.section-title { color:#6f675a; letter-spacing:.08em; font-size:11px; }
.table-input td:first-child { color:#625b50; font-size:12.5px; }
.form-control,.form-select { border-color:#e5e0d6; border-radius:7px!important; color:#302d25; background:#fff; font-size:13px; }
.form-control:focus,.form-select:focus { border-color:#a28e6b; box-shadow:0 0 0 3px rgba(162,142,107,.13); }
.btn-default { background:#66583f; border-radius:7px!important; }
.btn-default:hover { background:#51452f; }
.image-workflow-note { border:0; border-left:2px solid #a28e6b; border-radius:0; background:#f8f6f1; padding:10px 13px; }
.image-tile { width:126px; padding:5px; border:1px solid #e8e3da; border-radius:8px; background:#fff; }
.item-gambar { width:114px; height:114px; border-radius:5px; }
.cover-chip { background:#66583f; }
.image-actions { grid-template-columns:1fr 38px; }
.image-actions button,.image-actions label { min-height:34px; border-radius:6px; font-size:11px; }
.image-actions button:hover,.image-actions label:hover { border-color:#a28e6b; color:#66583f; }
.image-actions .make-primary { grid-column:1 / -1; color:#66583f; border-color:#ded4c4; }
.batch-upload-label { min-width:126px; height:156px; padding:12px; border:1px dashed #cfc4b2; border-radius:8px; background:#fbfaf7; color:#66583f; text-align:center; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:5px; cursor:pointer; }
.batch-upload-label:hover { border-color:#8b7959; background:#f5f1e9; }
.batch-upload-label.is-disabled { opacity:.55; cursor:wait; }
.batch-upload-label small { color:#817b70; font-weight:400; }
.variant-tools { border:0; padding-bottom:0; margin-bottom:10px; }
.bottom-bar { background:linear-gradient(180deg,rgba(255,255,255,0),#fff 35%); }
@media(max-width:992px){ .form-grid > .card + .card { padding:18px 0 0; border-left:0; border-top:1px solid #ded9cf; } }
@media(max-width:640px){ .form-grid{gap:16px}.image-tile{width:112px}.item-gambar{width:100px;height:100px}.batch-upload-label{min-width:112px;height:142px}.card-header,.card-body{padding-left:0;padding-right:0} }
.variant-panel { padding:0 0 16px; background:transparent; border:0; border-bottom:1px solid #e9e4dc; border-radius:0!important; box-shadow:none; }
.variant-panel:last-child { border-bottom:0; }
.variant-panel .card-body { padding:8px 0 16px; }
.variant-tools { padding:0 0 10px; border-bottom:1px solid #f0ece6; }
.variant-heading { display:flex; align-items:center; gap:10px; min-width:0; }
.variant-number { display:inline-flex; width:30px; height:30px; flex:0 0 30px; align-items:center; justify-content:center; border-radius:50%; background:#f2eee8; color:#725b43; font-size:12px; font-weight:700; }
.variant-heading strong { overflow:hidden; color:#342f29; font-size:14px; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
.variant-help { display:inline-flex; align-items:center; gap:4px; margin-left:2px; padding:4px 7px; border-radius:999px; background:#f7f5f1; font-size:11px; white-space:nowrap; }
.variant-help .material-icons { font-size:15px; }
.variant-icon-button { display:inline-flex; width:44px; height:44px; flex:0 0 44px; align-items:center; justify-content:center; padding:0; border:1px solid #e8e2d9; border-radius:50%; background:#fff; color:#70675d; cursor:pointer; transition:color .16s ease,border-color .16s ease,background .16s ease; }
.variant-icon-button:hover,.variant-icon-button:focus-visible { border-color:#b9a68e; background:#f8f5f0; color:#604a33; outline:2px solid #d6c5ad; outline-offset:2px; }
.variant-icon-button--danger:hover,.variant-icon-button--danger:focus-visible { border-color:#d9a8a2; background:#fff7f6; color:#a63d34; }
.drag-handle { width:44px; min-height:44px; margin:0 auto; }
.drag-handle .material-icons { font-size:20px; }
.image-actions button,.image-actions label { width:44px; min-width:44px; min-height:44px; justify-content:center; padding:0; }
.image-actions .make-primary { grid-column:auto; }
.image-actions .material-icons { margin:0; font-size:19px; }
@media(max-width:640px){ .variant-panel .card-body{padding:8px 0 14px}.variant-heading{gap:7px}.variant-number{width:28px;height:28px;flex-basis:28px}.variant-icon-button{width:44px;height:44px} }

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Badge status */
.badge {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 12px;
    border: 1px solid transparent;
}

.badge-aktif {
    background: rgba(16, 185, 129, .12);
    color: #065f46;
    border-color: rgba(16, 185, 129, .35);
}

.badge-non {
    background: rgba(107, 114, 128, .12);
    color: #374151;
    border-color: rgba(107, 114, 128, .35);
}
</style>

<div class="page-wrap">
    <h1 class="page-title"><?= isset($idProduct) ? 'Edit' : 'Tambah' ?> Produk</h1>
    <div id="container-react"></div>
</div>

<script type="text/babel">
    const { useState, useEffect, useRef } = React;

// ==== Data dari PHP (pastikan admin/template sudah load React + Babel UMD) ====
const koleksi = <?= $koleksiJson ?>;
const jenis   = <?= $jenisJson ?>;
const idProduct = <?= \App\Services\ScriptJson::encode((string) ($idProduct ?? '')) ?>;
const produk   = <?= isset($produkJson) ? $produkJson : 'null' ?>;
const PRODUCT_IMAGE_VERSIONS = <?= isset($productImageVersionsJson) ? $productImageVersionsJson : '{}' ?>;

// URL preview gambar lama saat edit. Wajib lewat route storage, bukan /img/barang,
// supaya gambar R2/uploads langsung terbaca dan tidak balik ke asset Git lama.
const PRODUCT_IMAGE_VERSION = <?= \App\Services\ScriptJson::encode((string) (($produk && !empty($produk['tgl_update'])) ? strtotime((string) $produk['tgl_update']) : time())) ?>;
const URL_HOVER = <?= \App\Services\ScriptJson::encode(base_url('viewpichover/' . ($produk['id'] ?? '')) . '?proxy=1') ?>;
const PRODUCT_IMAGE_PREVIEW_URLS = <?= isset($productImagePreviewUrlsJson) ? $productImagePreviewUrlsJson : '{}' ?>;
function productCoverPreviewUrl(slot) {
  const normalizedSlot = String(slot || '1');
  if (PRODUCT_IMAGE_PREVIEW_URLS[normalizedSlot]) return PRODUCT_IMAGE_PREVIEW_URLS[normalizedSlot];
  const baseUrl = <?= \App\Services\ScriptJson::encode(base_url('product-cover/' . ($produk['id'] ?? '')) . '?proxy=1&size=300&slot=') ?>;
  const version = PRODUCT_IMAGE_VERSIONS[normalizedSlot] || PRODUCT_IMAGE_VERSION;
  return `${baseUrl}${encodeURIComponent(normalizedSlot)}&v=${encodeURIComponent(version)}`;
}
function productCoverProxyPreviewUrl(slot) {
  const normalizedSlot = String(slot || '1');
  const baseUrl = <?= \App\Services\ScriptJson::encode(base_url('product-cover/' . ($produk['id'] ?? '')) . '?proxy=1&size=300&slot=') ?>;
  const version = PRODUCT_IMAGE_VERSIONS[normalizedSlot] || PRODUCT_IMAGE_VERSION;
  return `${baseUrl}${encodeURIComponent(normalizedSlot)}&v=${encodeURIComponent(version)}`;
}

// === Helper: format waktu untuk input[type=datetime-local] ===
function toInputDateTime(val) {
  if (!val) return '';
  const v = (val + '').trim().replace(' ', 'T');
  const [d, t=''] = v.split('T');
  const [hh='00', mm='00'] = t.split(':');
  return `${d}T${hh.padStart(2,'0')}:${mm.padStart(2,'0')}`;
}

// === Helper: cache buster untuk preview gambar lama ===
function withCacheBuster(url) {
  if (!url) return url;
  return `${url}${url.includes('?') ? '&' : '?'}v=${PRODUCT_IMAGE_VERSION}`;
}

function makeImageItem(src, file = null, slot = null) {
  return { src, file, slot: slot ? Number(slot) : null };
}

function getImageSrc(item) {
  return (item && typeof item === 'object') ? item.src : item;
}

function getImageFile(item) {
  return (item && typeof item === 'object') ? item.file : null;
}

function getImageSlot(item) {
  return (item && typeof item === 'object') ? item.slot : null;
}

const MAX_UPLOAD_MB = 4;
const MAX_PRODUCT_IMAGES = 25;
const ALLOWED_IMAGE_TYPES = ['image/webp', 'image/avif'];
const PREVIEW_REVOKE_DELAY = 1500;

function fileTooLarge(file) {
  return file && file.size > MAX_UPLOAD_MB * 1024 * 1024;
}

function fileTypeInvalid(file) {
  return file && !ALLOWED_IMAGE_TYPES.includes(file.type);
}

function resizeImageBeforeUpload(file, maxSize = 3000, quality = 0.82, onProgress = () => {}) {
  return new Promise((resolve) => {
    if (!file || !file.type || !file.type.startsWith('image/')) return resolve(file);
    const isAvif = file.type === 'image/avif';
    const img = new Image();
    const url = URL.createObjectURL(file);
    onProgress(8, 'Membuka gambar…');
    img.onload = () => {
      try {
        onProgress(25, 'Memeriksa dimensi gambar…');
        const ratio = Math.min(1, maxSize / Math.max(img.width, img.height));
        if (!isAvif && ratio >= 1 && file.size <= 2 * 1024 * 1024) {
          URL.revokeObjectURL(url);
          onProgress(100, 'Ukuran gambar sudah sesuai.');
          resolve(file);
          return;
        }
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(img.width * ratio));
        canvas.height = Math.max(1, Math.round(img.height * ratio));
        const ctx = canvas.getContext('2d');
        if (!ctx) {
          URL.revokeObjectURL(url);
          onProgress(100, 'Browser tidak dapat menyiapkan area resize.');
          resolve(isAvif ? null : file);
          return;
        }
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        onProgress(58, 'Dimensi gambar disesuaikan…');
        onProgress(72, 'Mengompres dan mengubah ke WebP…');
        canvas.toBlob((blob) => {
          URL.revokeObjectURL(url);
          if (!blob || blob.type !== 'image/webp') {
            onProgress(100, 'Konversi WebP tidak berhasil.');
            return resolve(isAvif ? null : file);
          }
          const safeName = (file.name || 'product-image').replace(/\.[^.]+$/, '') + '.webp';
          onProgress(100, 'Resize selesai; gambar siap disimpan.');
          resolve(new File([blob], safeName, { type: 'image/webp', lastModified: Date.now() }));
        }, 'image/webp', quality);
      } catch (e) {
        URL.revokeObjectURL(url);
        onProgress(100, 'Tidak dapat memproses gambar.');
        resolve(isAvif ? null : file);
      }
    };
    img.onerror = () => { URL.revokeObjectURL(url); onProgress(100, 'Browser tidak dapat membaca gambar.'); resolve(isAvif ? null : file); };
    img.src = url;
  });
}

const App = () => {
  const firstRender = useRef(true);
  const [formData, setFormData] = useState({
    id: idProduct || '',
    nama: '',
    harga: '',
    deskripsi: {
      deskripsi: '',
      nama_en: '',
      deskripsi_en: '',
      perawatan_en: '',
      dimensi: {
        asli:  { panjang:"", lebar:"", tinggi:"", berat:"" },
        paket: { panjang:"", lebar:"", tinggi:"", berat:"" }
      },
      perawatan: ''
    },
    kategori: '',
    subkategori: '',
    diskon: '',
    // Jadwal diskon
    pakai_jadwal_diskon: false,
    diskon_mulai: '',
    diskon_selesai: '',
    varian: [],
    shopee: '',
    tokped: '',
    tiktok: '',
    ruang_tamu: false,
    ruang_keluarga: false,
    ruang_tidur: false
  });
  const [eror, setEror] = useState('');
  const [hoverSrc, setHoverSrc] = useState(null);
  const [hoverFile, setHoverFile] = useState(null);
  // gambarSrc berisi object: { src, file, slot }. slot = nomor file fisik (1,2,3...) agar foto lama bisa dipindah/ditukar tanpa upload ulang.
  const [gambarSrc, setGambarSrc] = useState([]);
  const [gambarFile, setGambarFile] = useState([]); // dipertahankan untuk kompatibilitas state lama
  const [draggedImage, setDraggedImage] = useState(null);
  const [imageOrderMessage, setImageOrderMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const [resizeProgress, setResizeProgress] = useState({ active: false, percent: 0, message: '', fileName: '' });
  const [uploadProgress, setUploadProgress] = useState({ phase: 'idle', percent: null });
  const resizeBusyRef = useRef(false);
  const idStr = useRef("1-00-000-XX");

  // Keep the resize state visible briefly after completion so the admin can confirm the image is ready.
  useEffect(() => {
    document.body.style.cursor = loading ? 'wait' : '';
    return () => { document.body.style.cursor = ''; };
  }, [loading]);

  useEffect(() => {
    if (resizeProgress.active || !resizeProgress.message) return undefined;
    const timeout = setTimeout(() => setResizeProgress({ active: false, percent: 0, message: '' }), 3500);
    return () => clearTimeout(timeout);
  }, [resizeProgress.active, resizeProgress.message]);

  useEffect(() => {
    if (loading) {
      document.documentElement.setAttribute('aria-busy', 'true');
    } else {
      document.documentElement.removeAttribute('aria-busy');
    }
    return () => document.documentElement.removeAttribute('aria-busy');
  }, [loading]);

  // ADD: generate ID berdasarkan kategori/jenis
  useEffect(() => {
    if (idProduct) return;
    let arr = idStr.current.split("-");
    arr[1] = (formData.kategori ?? "").toString().padStart(2, '0');
    idStr.current = arr.join("-");
    setFormData(prev => ({ ...prev, id: arr.join("") }));
  }, [formData.kategori]);

  useEffect(() => {
    if (idProduct) return;
    let arr = idStr.current.split("-");
    arr[2] = (formData.subkategori ?? "").toString().padStart(3, '0');
    idStr.current = arr.join("-");
    setFormData(prev => ({ ...prev, id: arr.join("") }));
  }, [formData.subkategori]);

  // EDIT: preload data & gambar
  useEffect(() => {
    if (!firstRender.current) return;
    firstRender.current = false;

    if (idProduct && produk) {
      // Prefill form
      setFormData(prev => ({
        ...prev,
        id: idProduct,
        nama: produk.nama ?? '',
        harga: produk.harga ?? '',
        deskripsi: produk.deskripsi ?? prev.deskripsi,
        kategori: produk.kategori ?? '',
        subkategori: produk.subkategori ?? '',
        diskon: produk.diskon ?? '',
        // Jadwal
        pakai_jadwal_diskon:
          (produk.pakai_jadwal_diskon === 1 || produk.pakai_jadwal_diskon === '1') ||
          (!!produk.diskon_mulai && !!produk.diskon_selesai),
        diskon_mulai: toInputDateTime(produk.diskon_mulai),
        diskon_selesai: toInputDateTime(produk.diskon_selesai),
        varian: produk.varian ?? [],
        shopee: produk.shopee ?? '',
        tokped: produk.tokped ?? '',
        tiktok: produk.tiktok ?? '',
        ruang_tamu: (produk.ruang_tamu === '1' || produk.ruang_tamu === 1 || produk.ruang_tamu === true),
        ruang_keluarga: (produk.ruang_keluarga === '1' || produk.ruang_keluarga === 1 || produk.ruang_keluarga === true),
        ruang_tidur: (produk.ruang_tidur === '1' || produk.ruang_tidur === 1 || produk.ruang_tidur === true),
      }));

      // Hover preview
      setHoverSrc(withCacheBuster(URL_HOVER));

      // Preview 1000px untuk tiap varian
      const src = (produk.varian || []).map(v =>
        (v.urutan_gambar || '')
          .split(',')
          .filter(Boolean)
          .map(n => makeImageItem(productCoverPreviewUrl(n), null, n))
      );
      setGambarSrc(src);
      setGambarFile(src.map(group => group.map(item => item.file)));
    }
  }, []);

  // Sinkronkan urutan gambar dari posisi kartu. Foto lama memakai slot asli, foto baru diberi slot saat submit.
  useEffect(() => {
    setFormData(prev => ({
      ...prev,
      varian: (prev.varian || []).map((v, ind_v) => ({
        ...v,
        urutan_gambar: (gambarSrc[ind_v] || []).map(getImageSlot).filter(Boolean).join(',')
      }))
    }));
  }, [gambarSrc]);

  const handleSubmit = () => {
    // Validasi
    if (!/^[A-Za-z0-9_-]{1,64}$/.test(String(formData.id || ''))) { setEror("ID produk hanya boleh berisi huruf, angka, garis bawah, atau tanda hubung."); return; }
    if (!String(formData.nama || '').trim() || !String(formData.harga || '').trim()) { setEror("Nama dan harga produk wajib diisi."); return; }
    if (!Number.isFinite(Number(formData.harga)) || Number(formData.harga) < 0) { setEror("Harga produk harus berupa angka nol atau lebih."); return; }
    if (!Number.isFinite(Number(formData.diskon || 0)) || Number(formData.diskon || 0) < 0 || Number(formData.diskon || 0) > 100) { setEror("Diskon harus berada di antara 0 sampai 100 persen."); return; }
    if (!formData.kategori || !formData.subkategori) { setEror("Pilih koleksi dan jenis produk."); return; }
    if ((formData.varian || []).length === 0) { setEror("Minimal 1 varian harus ditambahkan."); return; }
    const variantNames = new Set();
    for (let i = 0; i < formData.varian.length; i++) {
      const variant = formData.varian[i] || {};
      const normalizedName = String(variant.nama || '').trim().toLocaleLowerCase();
      if (!normalizedName || !String(variant.kode || '').trim() || !/^\d+$/.test(String(variant.stok ?? ''))) {
        setEror(`Varian ke-${i + 1} wajib memiliki nama, kode, dan stok bilangan bulat nol atau lebih.`); return;
      }
      if (variantNames.has(normalizedName)) { setEror("Nama setiap varian harus unik."); return; }
      variantNames.add(normalizedName);
    }
    const dimensionFields = ['panjang', 'lebar', 'tinggi', 'berat'];
    for (const dimensionType of ['asli', 'paket']) {
      for (const field of dimensionFields) {
        const value = formData.deskripsi?.dimensi?.[dimensionType]?.[field];
        if (value === '' || value === null || value === undefined || !Number.isFinite(Number(value)) || Number(value) < 0) {
          setEror("Dimensi dan berat produk harus diisi dengan angka nol atau lebih."); return;
        }
      }
    }
    const totalFotoProduk = (gambarSrc || []).reduce((total, group) => total + ((group || []).length), 0);
    if (totalFotoProduk > MAX_PRODUCT_IMAGES) {
      setEror(`Maksimal ${MAX_PRODUCT_IMAGES} foto produk dalam 1 produk. Saat ini ada ${totalFotoProduk} foto.`);
      return;
    }
    for (let i = 0; i < formData.varian.length; i++) {
      if (!(gambarSrc[i] || []).length) { setEror(`Varian ke-${i + 1} belum memiliki gambar.`); return; }
    }
    if (formData.pakai_jadwal_diskon) {
      if (!formData.diskon_mulai || !formData.diskon_selesai) { setEror("Mohon isi tanggal mulai & selesai jadwal diskon."); return; }
      if (new Date(formData.diskon_mulai) >= new Date(formData.diskon_selesai)) { setEror("Waktu mulai harus lebih awal dari waktu selesai."); return; }
    }

    const form = new FormData();
    form.append("id", formData.id);
    form.append("nama", formData.nama);
    form.append("harga", formData.harga);
    form.append("diskon", formData.diskon || "0");
    form.append("kategori", formData.kategori);
    form.append("subkategori", formData.subkategori);
    form.append("shopee", formData.shopee || "");
    form.append("tokped", formData.tokped || "");
    form.append("tiktok", formData.tiktok || "");
    form.append("ruang_tamu", formData.ruang_tamu ? "1" : "0");
    form.append("ruang_keluarga", formData.ruang_keluarga ? "1" : "0");
    form.append("ruang_tidur", formData.ruang_tidur ? "1" : "0");
    form.append("deskripsi", JSON.stringify(formData.deskripsi || {}));

    if (hoverFile) form.append("gambar_hover", hoverFile);

    // Simpan sesuai urutan visual. Foto lama tetap memakai nomor file aslinya,
    // foto baru/replacement dikirim ke slot tepat: gambar_0 => foto 1, gambar_4 => foto 5, dst.
    const usedSlots = new Set();
    (gambarSrc || []).forEach(group => (group || []).forEach(item => {
      const slot = getImageSlot(item);
      if (slot) usedSlots.add(Number(slot));
    }));
    let nextSlot = Math.max(0, ...Array.from(usedSlots)) + 1;
    const takeSlot = () => {
      while (usedSlots.has(nextSlot)) nextSlot++;
      usedSlots.add(nextSlot);
      return nextSlot++;
    };

    const varianPayload = (formData.varian || []).map((v, ind_v) => {
      const images = (gambarSrc[ind_v] || []).map(item => {
        const existingSlot = getImageSlot(item);
        return { ...item, slot: existingSlot || takeSlot() };
      });
      images.forEach(item => {
        const file = getImageFile(item);
        if (file instanceof File) form.append(`gambar_${Number(item.slot) - 1}`, file);
      });
      return { ...v, id: String(ind_v + 1), urutan_gambar: images.map(item => item.slot).join(',') };
    });
    form.append("varian", JSON.stringify(varianPayload));

    // Jadwal Diskon
    form.append("pakai_jadwal_diskon", formData.pakai_jadwal_diskon ? "1" : "0");
    form.append("diskon_mulai", formData.diskon_mulai || "");
    form.append("diskon_selesai", formData.diskon_selesai || "");

    // CSRF
    const csrfName = '<?= csrf_token() ?>';
    const csrfHash = '<?= csrf_hash() ?>';
    form.append(csrfName, csrfHash);

    setLoading(true);
    setEror("");
    setUploadProgress({ phase: 'uploading', percent: 0 });

    try {
      const url = `<?= rtrim(base_url(), '/'); ?>${idProduct ? `/admin/editproduct/${idProduct}` : `/admin/product`}`;
      const xhr = new XMLHttpRequest();
      xhr.open('POST', url, true);
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.upload.addEventListener('progress', (event) => {
        if (event.lengthComputable && event.total > 0) {
          setUploadProgress({ phase: 'uploading', percent: Math.min(99, Math.floor((event.loaded / event.total) * 100)) });
        } else {
          setUploadProgress({ phase: 'uploading', percent: null });
        }
      });
      xhr.upload.addEventListener('load', () => setUploadProgress({ phase: 'processing', percent: 100 }));
      xhr.addEventListener('load', () => {
        const rawText = xhr.responseText || '';
        let result;
        try { result = JSON.parse(rawText); } catch { result = { pesan: rawText }; }
        setLoading(false);

        if (xhr.status < 200 || xhr.status >= 300) {
          setEror(result?.pesan || `Error ${xhr.status}. ${rawText.slice(0, 500)}`);
          console.error('Response error:', { status: xhr.status, result });
          return;
        }

        window.IlenaDialog.alert(`Produk berhasil ${idProduct ? 'diubah' : 'ditambahkan'}.`, { title: 'Berhasil' })
          .then(() => { window.location.href = `<?= base_url('admin/product'); ?>`; });
      });
      xhr.addEventListener('error', () => {
        setLoading(false);
        setEror('Gagal menghubungi server. Periksa koneksi lalu coba lagi.');
      });
      xhr.addEventListener('abort', () => {
        setLoading(false);
        setEror('Pengiriman dibatalkan sebelum selesai.');
      });
      xhr.send(form);
    } catch (err) {
      console.error('Gagal mengirim data:', err);
      setLoading(false);
      setEror('Gagal menghubungi server.');
    }
  };

  const syncLegacyGambarFile = (nextSrc) => {
    setGambarFile((nextSrc || []).map(group => (group || []).map(getImageFile)));
  };

  const updateVariantImages = (variantIndex, updater) => {
    const nextSrc = (gambarSrc || []).map(group => [...(group || [])]);
    nextSrc[variantIndex] = updater(nextSrc[variantIndex] || []);
    setGambarSrc(nextSrc);
    syncLegacyGambarFile(nextSrc);
  };

  const moveImage = (variantIndex, imageIndex, direction) => {
    moveImageToPosition(variantIndex, imageIndex, imageIndex + direction);
  };

  const moveImageToPosition = (variantIndex, fromIndex, toIndex) => {
    const current = gambarSrc?.[variantIndex] || [];
    if (fromIndex < 0 || toIndex < 0 || fromIndex >= current.length || toIndex >= current.length || fromIndex === toIndex) return;
    updateVariantImages(variantIndex, list => {
      const copy = [...list];
      const [item] = copy.splice(fromIndex, 1);
      copy.splice(toIndex, 0, item);
      return copy;
    });
    setImageOrderMessage(`Foto dipindahkan ke urutan ${toIndex + 1}. ${toIndex === 0 ? 'Foto ini menjadi cover katalog.' : ''}`);
  };

  const moveImageToFirst = (variantIndex, imageIndex) => {
    moveImageToPosition(variantIndex, imageIndex, 0);
  };

  const startImageDrag = (event, variantIndex, imageIndex) => {
    setDraggedImage({ variantIndex, imageIndex });
    setImageOrderMessage(`Mulai memindahkan foto ${imageIndex + 1}. Lepaskan pada posisi baru.`);
    if (event.dataTransfer) {
      event.dataTransfer.effectAllowed = 'move';
      event.dataTransfer.setData('text/plain', `${variantIndex}:${imageIndex}`);
    }
  };

  const dropImage = (event, variantIndex, targetIndex) => {
    event.preventDefault();
    if (!draggedImage || draggedImage.variantIndex !== variantIndex) return;
    moveImageToPosition(variantIndex, draggedImage.imageIndex, targetIndex);
    setDraggedImage(null);
  };

  const resizeForAdmin = async (file, maxSize = 3000, quality = 0.82) => {
    if (resizeBusyRef.current) {
      setEror('Tunggu resize gambar sebelumnya selesai sebelum memilih gambar lain.');
      return undefined;
    }
    resizeBusyRef.current = true;
    setResizeProgress({ active: true, percent: 3, message: 'Menyiapkan gambar…', fileName: file?.name || 'Foto produk' });
    try {
      return await resizeImageBeforeUpload(file, maxSize, quality, (percent, message) => {
        setResizeProgress({ active: true, percent, message, fileName: file?.name || 'Foto produk' });
      });
    } catch (error) {
      console.error('Resize gambar gagal:', error);
      setResizeProgress({ active: true, percent: 100, message: 'Resize gambar gagal.' });
      setEror('Gambar belum berhasil diproses. Pilih gambar lain atau coba kembali.');
      return undefined;
    } finally {
      resizeBusyRef.current = false;
      setResizeProgress(current => ({ ...current, active: false }));
    }
  };

  const removeImage = (variantIndex, imageIndex) => {
    updateVariantImages(variantIndex, list => list.filter((_, i) => i !== imageIndex));
    setImageOrderMessage('Foto dihapus dari rancangan. File tersimpan baru dihapus setelah Anda menekan Simpan.');
  };

  const addOrReplaceImage = async (variantIndex, file, imageIndex = null) => {
    if (!file) return;
    if (fileTypeInvalid(file)) {
      setEror('Format foto hanya WebP atau AVIF.');
      return;
    }
    if (fileTooLarge(file)) {
      setEror(`Ukuran foto maksimal ${MAX_UPLOAD_MB}MB. Kompres dulu fotonya atau pilih file yang lebih kecil.`);
      return;
    }
    setEror('');
    const optimizedFile = await resizeForAdmin(file);
    if (optimizedFile === undefined) return;
    if (!optimizedFile) {
      setEror('Browser tidak dapat mengonversi AVIF. Simpan gambar sebagai WebP lalu unggah kembali.');
      return;
    }
    const previewUrl = URL.createObjectURL(optimizedFile);
    updateVariantImages(variantIndex, list => {
      const oldSrc = imageIndex === null ? null : getImageSrc(list[imageIndex]);
      const item = makeImageItem(previewUrl, optimizedFile, imageIndex === null ? null : getImageSlot(list[imageIndex]));
      if (oldSrc && oldSrc.startsWith('blob:')) setTimeout(() => URL.revokeObjectURL(oldSrc), PREVIEW_REVOKE_DELAY);
      if (imageIndex === null) return [...list, item];
      return list.map((oldItem, i) => i === imageIndex ? item : oldItem);
    });
  };

  // Select several photos at once, optimize one at a time to keep browser memory predictable.
  const addMultipleImages = async (variantIndex, fileList) => {
    const files = Array.from(fileList || []);
    if (!files.length) return;
    const existingCount = (gambarSrc || []).reduce((total, group) => total + (group || []).length, 0);
    const room = Math.max(0, MAX_PRODUCT_IMAGES - existingCount);
    if (room === 0) { setEror(`Batas ${MAX_PRODUCT_IMAGES} foto untuk satu produk sudah tercapai.`); return; }
    const accepted = files.slice(0, room);
    if (files.length > room) setEror(`Hanya ${room} foto yang dapat ditambahkan; batas produk adalah ${MAX_PRODUCT_IMAGES} foto.`);
    const ready = [];
    for (const file of accepted) {
      if (fileTypeInvalid(file)) { setEror(`“${file.name}” dilewati: pilih file WebP atau AVIF.`); continue; }
      if (fileTooLarge(file)) { setEror(`“${file.name}” dilewati: ukuran melebihi ${MAX_UPLOAD_MB} MB.`); continue; }
      const optimized = await resizeForAdmin(file);
      if (!optimized) { setEror(`“${file.name}” tidak dapat diproses menjadi WebP dan dilewati.`); continue; }
      ready.push(makeImageItem(URL.createObjectURL(optimized), optimized, null));
    }
    if (ready.length) {
      const readyFiles = ready.map(getImageFile);
      setGambarSrc(previous => {
        const next = (previous || []).map(group => [...(group || [])]);
        next[variantIndex] = [...(next[variantIndex] || []), ...ready];
        return next;
      });
      setGambarFile(previous => {
        const next = (previous || []).map(group => [...(group || [])]);
        next[variantIndex] = [...(next[variantIndex] || []), ...readyFiles];
        return next;
      });
      setImageOrderMessage(`${ready.length} foto ditambahkan. Foto pertama pada varian ini menjadi foto utama setelah disimpan.`);
    }
  };

  // Badge status jadwal diskon
  const renderBadgeJadwal = () => {
    if (!formData.pakai_jadwal_diskon || !formData.diskon_mulai || !formData.diskon_selesai) return null;
    const now = new Date();
    const start = new Date(formData.diskon_mulai);
    const end = new Date(formData.diskon_selesai);
    const aktif = now >= start && now <= end;
    return (
      <span className={`badge ${aktif ? 'badge-aktif' : 'badge-non'}`}>
        {aktif ? 'Aktif sekarang' : 'Belum aktif / di luar jadwal'}
      </span>
    );
  };

  return (
    <>
      {resizeProgress.active && !loading && <aside className="resize-live-toast" role="status" aria-live="polite" aria-atomic="true">
        <div className="resize-live-toast__head">
          <span className="resize-live-toast__icon" aria-hidden="true"><i className="material-icons">auto_awesome</i></span>
          <span className="resize-live-toast__copy">
            <strong className="resize-live-toast__title">Sedang merapikan foto produkmu</strong>
            <span className="resize-live-toast__message">Sabar ya, foto sedang dikompres supaya tampil tajam dan lebih ringan ✨</span>
          </span>
        </div>
        <div className="resize-live-toast__filename" title={resizeProgress.fileName}>{resizeProgress.message} · {resizeProgress.fileName}</div>
        <div className="resize-live-toast__track" role="progressbar" aria-label="Proses resize dan kompres foto" aria-valuemin="0" aria-valuemax="100" aria-valuenow={resizeProgress.percent} aria-valuetext={`${resizeProgress.percent}% — ${resizeProgress.message}`}>
          <div className={`resize-live-toast__fill ${resizeProgress.percent >= 58 && resizeProgress.percent < 100 ? 'is-working' : ''}`} style={{ width: `${resizeProgress.percent}%` }} />
        </div>
      </aside>}
      {loading && <div className="loading-overlay show" role="dialog" aria-modal="true" aria-live="polite" aria-label="Status penyimpanan produk">
        <div className="loading-card">
          <span className="spinner" aria-hidden="true" />
          <strong>{uploadProgress.phase === 'processing' ? 'Server sedang memproses produk' : 'Sedang mengunggah produk'}</strong>
          <div className="save-progress" role="progressbar" aria-label={uploadProgress.phase === 'processing' ? 'Server sedang memproses' : 'Progress unggah'} aria-valuemin="0" aria-valuemax="100" {...(uploadProgress.phase === 'uploading' && uploadProgress.percent !== null ? { 'aria-valuenow': uploadProgress.percent } : {})}>
            <span className={`save-progress__fill ${uploadProgress.phase === 'processing' || uploadProgress.percent === null ? 'is-indeterminate' : ''}`} style={uploadProgress.phase === 'processing' || uploadProgress.percent === null ? undefined : { width: `${uploadProgress.percent}%` }} />
          </div>
          <span>{uploadProgress.phase === 'processing'
            ? 'Unggahan selesai. Server sedang menyimpan produk dan menyiapkan gambar WebP.'
            : uploadProgress.percent === null ? 'Mengunggah data dan foto…' : `Mengunggah data dan foto: ${uploadProgress.percent}%`}</span>
        </div>
      </div>}
      {Boolean(idProduct) && <nav className="edit-guide" aria-label="Navigasi bagian edit produk">
        <div className="edit-guide__steps">
          <a href="#product-basic">Info</a>
          <a href="#product-description">Deskripsi</a>
          <a href="#product-images">Foto</a>
          <a href="#product-variants">Varian</a>
        </div>
      </nav>}
      <div className="form-grid" aria-busy={loading || resizeProgress.active}>
        {/* Kiri */}
        <div className="card">
          <div className="card-header"><h2>Data Produk</h2></div>
          <div className="card-body">
            <table className="table-input w-100">
              <tbody>
                <tr>
                  <td id="product-basic" className="edit-anchor">Nama Produk</td>
                  <td>
                    <input type="text" className="form-control"
                      value={formData.nama}
                      onChange={e => setFormData({ ...formData, nama: e.target.value })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>Harga Produk</td>
                  <td>
                    <input type="number" className="form-control"
                      value={formData.harga}
                      onChange={e => setFormData({ ...formData, harga: e.target.value })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>Diskon Produk</td>
                  <td>
                    <div className="input-group">
                      <input type="number" className="form-control" step="any"
                        value={formData.diskon}
                        onChange={e => setFormData({ ...formData, diskon: e.target.value })}
                        required />
                      <span className="input-group-text">%</span>
                    </div>
                  </td>
                </tr>

                {/* Jadwal Diskon */}
                <tr>
                  <td>Jadwalkan Diskon</td>
                  <td>
                    <label className="chk">
                      <input
                        type="checkbox"
                        checked={formData.pakai_jadwal_diskon}
                        onChange={(e) => setFormData({ ...formData, pakai_jadwal_diskon: e.target.checked })}
                      />
                      Aktifkan jadwal
                    </label>
                    {formData.pakai_jadwal_diskon && (
                      <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:'8px', marginTop:'8px' }}>
                        <div>
                          <small className="section-title" style={{ margin:'0 0 6px' }}>Mulai</small>
                          <input
                            type="datetime-local"
                            className="form-control"
                            value={formData.diskon_mulai}
                            onChange={(e) => setFormData({ ...formData, diskon_mulai: e.target.value })}
                          />
                        </div>
                        <div>
                          <small className="section-title" style={{ margin:'0 0 6px' }}>Selesai</small>
                          <input
                            type="datetime-local"
                            className="form-control"
                            value={formData.diskon_selesai}
                            onChange={(e) => setFormData({ ...formData, diskon_selesai: e.target.value })}
                          />
                        </div>
                        <div style={{ gridColumn:'1 / -1' }}>{renderBadgeJadwal()}</div>
                      </div>
                    )}
                  </td>
                </tr>

                {!idProduct && (
                  <>
                    <tr>
                      <td>Koleksi</td>
                      <td>
                        <select className="form-select" value={formData.kategori}
                          onChange={e => setFormData({ ...formData, kategori: e.target.value })} required>
                          <option value="">-- Pilih koleksi --</option>
                          {koleksi.map((k, i) => <option key={i} value={k.id}>{k.nama}</option>)}
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td>Jenis</td>
                      <td>
                        <select className="form-select" value={formData.subkategori}
                          onChange={e => setFormData({ ...formData, subkategori: e.target.value })} required>
                          <option value="">-- Pilih jenis --</option>
                          {jenis.map((j, i) => <option key={i} value={j.id}>{j.nama}</option>)}
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td>ID Produk</td>
                      <td>
                        <input type="text" className="form-control"
                          value={formData.id}
                          onChange={e => setFormData({ ...formData, id: e.target.value })}
                          required />
                      </td>
                    </tr>
                  </>
                )}

                <tr><td colSpan="2" className="section-title">Kanal penjualan</td></tr>
                <tr>
                  <td>Link Shopee</td>
                  <td><input type="text" className="form-control"
                    value={formData.shopee} onChange={e => setFormData({ ...formData, shopee: e.target.value })} /></td>
                </tr>
                <tr>
                  <td>Link Tokopedia</td>
                  <td><input type="text" className="form-control"
                    value={formData.tokped} onChange={e => setFormData({ ...formData, tokped: e.target.value })} /></td>
                </tr>
                <tr>
                  <td>Link Tiktok</td>
                  <td><input type="text" className="form-control"
                    value={formData.tiktok} onChange={e => setFormData({ ...formData, tiktok: e.target.value })} /></td>
                </tr>

                <tr id="product-description" className="edit-anchor"><td colSpan="2" className="section-title">Deskripsi & ukuran produk</td></tr>
                <tr>
                  <td>Deskripsi</td>
                  <td>
                    <textarea className="form-control" rows="3"
                      value={formData.deskripsi.deskripsi}
                      onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, deskripsi: e.target.value } })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>Spesifikasi</td>
                  <td>
                    <textarea className="form-control" rows="3"
                      value={formData.deskripsi.perawatan}
                      onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, perawatan: e.target.value } })}
                      required />
                  </td>
                </tr>
                <tr>
                  <td>English product name (optional)</td>
                  <td><input className="form-control" maxLength="180" value={formData.deskripsi.nama_en || ''}
                    onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, nama_en: e.target.value } })} /></td>
                </tr>
                <tr>
                  <td>English description (optional)</td>
                  <td><textarea className="form-control" rows="3" maxLength="3000"
                    value={formData.deskripsi.deskripsi_en || ''}
                    onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, deskripsi_en: e.target.value } })} /></td>
                </tr>
                <tr>
                  <td>English care / specifications (optional)</td>
                  <td><textarea className="form-control" rows="3" maxLength="3000"
                    value={formData.deskripsi.perawatan_en || ''}
                    onChange={e => setFormData({ ...formData, deskripsi: { ...formData.deskripsi, perawatan_en: e.target.value } })} /></td>
                </tr>

                <tr><td colSpan="2" className="section-title">Bentuk Paket</td></tr>
                <tr>
                  <td>Panjang (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.paket.panjang}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, panjang: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Lebar (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.paket.lebar}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, lebar: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Tinggi (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.paket.tinggi}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, tinggi: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Berat (kg)</td>
                  <td><input type="number" step="any" className="form-control"
                    value={formData.deskripsi.dimensi.paket.berat}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          paket: { ...formData.deskripsi.dimensi.paket, berat: e.target.value } } }
                    })} required /></td>
                </tr>

                <tr><td colSpan="2" className="section-title">Bentuk Asli</td></tr>
                <tr>
                  <td>Panjang (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.asli.panjang}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, panjang: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Lebar (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.asli.lebar}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, lebar: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Tinggi (cm)</td>
                  <td><input className="form-control" value={formData.deskripsi.dimensi.asli.tinggi}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, tinggi: e.target.value } } }
                    })} required /></td>
                </tr>
                <tr>
                  <td>Berat (kg)</td>
                  <td><input type="number" step="any" className="form-control"
                    value={formData.deskripsi.dimensi.asli.berat}
                    onChange={e => setFormData({
                      ...formData, deskripsi: { ...formData.deskripsi,
                        dimensi: { ...formData.deskripsi.dimensi,
                          asli: { ...formData.deskripsi.dimensi.asli, berat: e.target.value } } }
                    })} required /></td>
                </tr>

                <tr><td colSpan="2" className="section-title">Ruangan</td></tr>
                <tr>
                  <td>Tag Ruangan</td>
                  <td>
                    <div className="chk-row">
                      <label className="chk"><input type="checkbox" checked={formData.ruang_tamu}
                        onChange={e => setFormData({ ...formData, ruang_tamu: e.target.checked })} />Ruang Tamu</label>
                      <label className="chk"><input type="checkbox" checked={formData.ruang_keluarga}
                        onChange={e => setFormData({ ...formData, ruang_keluarga: e.target.checked })} />Ruang Keluarga</label>
                      <label className="chk"><input type="checkbox" checked={formData.ruang_tidur}
                        onChange={e => setFormData({ ...formData, ruang_tidur: e.target.checked })} />Ruang Tidur</label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div className="bottom-bar">
              <div className="btn-row">
                <button className="btn-default" disabled={loading || resizeProgress.active} onClick={handleSubmit}>
                  <span style={{display:'inline-flex',alignItems:'center',gap:'8px'}}>
                    {(loading || resizeProgress.active) && <span className="spinner" style={{borderColor:'rgba(255,255,255,.35)', borderTopColor:'#fff'}}></span>}
                    {loading ? 'Menyimpan...' : resizeProgress.active ? 'Menunggu resize...' : 'Simpan'}
                  </span>
                </button>
              </div>
            </div>
            {eror && <p style={{ color: '#ef4444', marginTop: '8px', fontWeight: 600 }}>{eror}</p>}
          </div>
        </div>

        {/* Kanan */}
        <div className="card edit-anchor" id="product-images">
          <div className="card-header"><h2>Media & Varian</h2></div>
          <div className="card-body">
            <p className="media-help">WebP / AVIF · maks. {MAX_UPLOAD_MB} MB per foto · {MAX_PRODUCT_IMAGES} foto per produk</p>
            <div className="section-title">Foto hover</div>
            <div style={{display:'flex',alignItems:'center',gap:'12px',marginBottom:'18px'}}>
              <img className="preview-hover" src={hoverSrc || "/img/nopic.jpg"} alt="Pratinjau gambar hover" />
              <input
                onChange={async (e) => {
                  const file = e.target.files[0];
                  if (fileTypeInvalid(file)) {
                    setEror('Format foto hover hanya WebP atau AVIF.');
                    e.target.value = '';
                    return;
                  }
                  if (fileTooLarge(file)) {
                    setEror(`Ukuran foto maksimal ${MAX_UPLOAD_MB}MB. Kompres dulu fotonya atau pilih file yang lebih kecil.`);
                    e.target.value = '';
                    return;
                  }
                  if (file) {
                    const optimizedFile = await resizeForAdmin(file, 1200, 0.82);
                    if (optimizedFile === undefined) {
                      e.target.value = '';
                      return;
                    }
                    if (!optimizedFile) {
                      setEror('Browser tidak dapat mengonversi AVIF. Simpan gambar hover sebagai WebP lalu unggah kembali.');
                      e.target.value = '';
                      return;
                    }
                    setHoverFile(optimizedFile);
                    const oldHover = hoverSrc;
                    const nextHover = URL.createObjectURL(optimizedFile);
                    setHoverSrc(nextHover);
                    if (oldHover && oldHover.startsWith('blob:')) setTimeout(() => URL.revokeObjectURL(oldHover), PREVIEW_REVOKE_DELAY);
                  } else { setHoverFile(null); }
                }}
                name="gambar_hover" type="file" accept="image/webp,image/avif" className="form-control" />
            </div>

            <div className="section-title edit-anchor" id="product-variants">Varian</div>
            <p className="media-help">Foto pertama varian pertama menjadi cover. Seret untuk mengurutkan.</p>
            <span className="image-order-status" role="status" aria-live="polite">{imageOrderMessage}</span>
            {(resizeProgress.active || resizeProgress.message) && <div className="image-resize-progress" role="status" aria-live="polite">
              <div className="image-resize-progress__top"><span className="image-resize-progress__label">{resizeProgress.active && <span className="image-resize-progress__spinner" aria-hidden="true" />}{resizeProgress.active ? 'Sedang menyiapkan gambar' : 'Status gambar'}</span><strong>{resizeProgress.percent}%</strong></div>
              <div className="image-resize-progress__track" role="progressbar" aria-label="Progress resize gambar" aria-valuemin="0" aria-valuemax="100" aria-valuenow={resizeProgress.percent} aria-valuetext={`${resizeProgress.percent}% tahapan — ${resizeProgress.message}`}>
                <div className="image-resize-progress__fill" style={{ width: `${resizeProgress.percent}%` }} />
              </div>
              <small>{resizeProgress.message}</small>
            </div>}
            <div id="container-varian" style={{ display:'flex', flexDirection:'column', gap:'16px' }}>
              {(formData.varian || []).map((v, ind_v) => (
                <div key={ind_v} className="variant-panel">
                  <div className="card-body">
                    <div className="variant-tools">
                      <div className="variant-heading">
                        <span className="variant-number" aria-hidden="true">{String(ind_v + 1).padStart(2, '0')}</span>
                        <strong>{String(v.nama || '').trim() || `Varian ${ind_v + 1}`}</strong>
                        <span className="variant-help" title={`${(gambarSrc[ind_v] || []).length} foto`}><i className="material-icons" aria-hidden="true">photo_library</i>{(gambarSrc[ind_v] || []).length}</span>
                      </div>
                      <button type="button" className="variant-icon-button variant-icon-button--danger" title="Hapus varian" aria-label={`Hapus varian ${v.nama || ind_v + 1}`}
                        onClick={() => {
                          setFormData({ ...formData, varian: formData.varian.filter((_, i) => i !== ind_v) });
                          setGambarFile(gambarFile.filter((_, i) => i !== ind_v));
                          setGambarSrc(gambarSrc.filter((_, i) => i !== ind_v));
                        }}><i className="material-icons" aria-hidden="true">delete_outline</i></button>
                    </div>
                    <div className="container-gambar">
                      {gambarSrc.length > 0 && (gambarSrc[ind_v] || []).map((g, ind_g) => {
                        return (
                        <div key={ind_g} className={`image-tile ${draggedImage?.variantIndex === ind_v && draggedImage?.imageIndex === ind_g ? 'is-dragging' : ''} ${draggedImage?.variantIndex === ind_v && draggedImage?.imageIndex !== ind_g ? 'is-drop-target' : ''}`}
                          onDragOver={(e) => { e.preventDefault(); if (e.dataTransfer) e.dataTransfer.dropEffect = 'move'; }}
                          onDrop={(e) => dropImage(e, ind_v, ind_g)}>
                          <input id={`replace-file-${ind_v}-${ind_g}`} type="file" accept="image/webp,image/avif" disabled={resizeProgress.active || loading} onChange={(e) => { addOrReplaceImage(ind_v, e.target.files[0], ind_g); e.target.value = ''; }} />
                          <span className="drag-handle" draggable="true" role="group" tabIndex="0" aria-keyshortcuts="ArrowLeft ArrowRight ArrowUp ArrowDown Home"
                            aria-label={`Seret foto ${ind_g + 1} varian ${v.nama || ind_v + 1} untuk mengubah urutan`}
                            title="Seret foto untuk mengubah urutan"
                            onDragStart={(e) => startImageDrag(e, ind_v, ind_g)}
                            onDragEnd={() => setDraggedImage(null)}
                            onKeyDown={(e) => {
                              if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { e.preventDefault(); moveImage(ind_v, ind_g, -1); }
                              if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); moveImage(ind_v, ind_g, 1); }
                              if (e.key === 'Home') { e.preventDefault(); moveImageToFirst(ind_v, ind_g); }
                            }}><i className="material-icons" aria-hidden="true">drag_indicator</i></span>
                          <div className="item-gambar">
                            <span className="img-no">{ind_g + 1}</span>
                            {ind_g === 0 && <span className="cover-chip">{ind_v === 0 ? 'Cover katalog' : 'Utama varian'}</span>}
                            <div className="missing-image-state" hidden>Foto tidak tersedia<br/>Unggah ulang</div>
                            <img src={getImageSrc(g) || "/img/nopic.jpg"} data-slot={getImageSlot(g) || ''} alt={`Foto varian ${ind_v + 1} nomor ${ind_g + 1}`} loading="lazy" decoding="async"
                              onError={(e) => {
                                const image = e.currentTarget;
                                const slot = image.dataset.slot;
                                const proxyFallback = slot ? productCoverProxyPreviewUrl(slot) : '';
                                if (proxyFallback && image.dataset.proxyTried !== '1' && image.src !== proxyFallback) {
                                  image.dataset.proxyTried = '1';
                                  image.src = proxyFallback;
                                  return;
                                }
                                image.hidden = true;
                                image.parentElement?.querySelector('.missing-image-state')?.removeAttribute('hidden');
                              }}
                              onLoad={(e) => { e.currentTarget.hidden = false; e.currentTarget.parentElement?.querySelector('.missing-image-state')?.setAttribute('hidden', 'hidden'); }} />
                          </div>
                          <div className="image-actions">
                            <label htmlFor={`replace-file-${ind_v}-${ind_g}`} title="Ganti foto" aria-label={`Ganti foto ${ind_g + 1}`}><i className="material-icons" aria-hidden="true">edit</i></label>
                            <button type="button" className="danger" title="Hapus foto" onClick={() => removeImage(ind_v, ind_g)} aria-label={`Hapus foto ${ind_g + 1}`}><i className="material-icons">delete</i></button>
                            {ind_g > 0 && <button type="button" className="make-primary" onClick={() => moveImageToFirst(ind_v, ind_g)} title={ind_v === 0 ? 'Jadikan cover katalog' : 'Jadikan foto utama varian'} aria-label={ind_v === 0 ? 'Jadikan cover katalog' : 'Jadikan foto utama varian'}><i className="material-icons" aria-hidden="true">star</i></button>}
                          </div>
                        </div>
                        );
                      })}
                      <div>
                        <input type="file" accept="image/webp,image/avif" id={`file-${ind_v}`} style={{ display:'none' }} multiple disabled={resizeProgress.active || loading}
                          onChange={(e) => { addMultipleImages(ind_v, e.target.files); e.target.value = ''; }} />
                        <label htmlFor={`file-${ind_v}`} className={`batch-upload-label ${resizeProgress.active || loading ? 'is-disabled' : ''}`} aria-disabled={resizeProgress.active || loading}><i className="material-icons">add_photo_alternate</i><strong>Tambah foto</strong><small>Pilih beberapa sekaligus</small><small>WebP / AVIF</small></label>
                      </div>
                    </div>

                    <table className="table-input w-100" style={{ marginTop:'12px' }}>
                      <tbody>
                        <tr>
                          <td>Nama</td>
                          <td>
                            <input className="form-control" value={v.nama}
                              onChange={e => setFormData({
                                ...formData,
                                varian: formData.varian.map((vv, i) => i === ind_v ? { ...vv, nama: e.target.value } : vv)
                              })} required />
                          </td>
                        </tr>
                        <tr>
                          <td>Kode Warna</td>
                          <td>
                            <input className="form-control" value={v.kode}
                              onChange={e => setFormData({
                                ...formData,
                                varian: formData.varian.map((vv, i) => i === ind_v ? { ...vv, kode: e.target.value } : vv)
                              })} required />
                          </td>
                        </tr>
                        <tr>
                          <td>Stok</td>
                          <td>
                            <input className="form-control" value={v.stok} readOnly={Boolean(idProduct)}
                              onChange={e => setFormData({
                                ...formData,
                                varian: formData.varian.map((vv, i) => i === ind_v ? { ...vv, stok: e.target.value } : vv)
                              })} required aria-describedby={idProduct ? `stock-help-${ind_v}` : undefined} />
                            {Boolean(idProduct) && <small id={`stock-help-${ind_v}`} className="text-muted">Stok diatur melalui Mutasi.</small>}
                          </td>
                        </tr>
                      </tbody>
                    </table>

                  </div>
                </div>
              ))}
            </div>

            <button className="btn-default-merah" type="button" style={{ marginTop:'12px' }}
              onClick={() => {
                setFormData({
                  ...formData,
                  varian: [...(formData.varian || []), { nama: "", kode: "", stok: "", urutan_gambar: "" }]
                });
                setGambarFile([...(gambarFile || []), []]);
                setGambarSrc([...(gambarSrc || []), []]);
              }}>
              Tambah Varian
            </button>
          </div>
        </div>
      </div>

      <input type="text" name="hitung-varian" style={{ display: 'none' }} defaultValue={1} />
    </>
  );
};

ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>
