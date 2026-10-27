# Peta Sistem Admin Ilena — 27 Oktober 2026

Dokumen kerja untuk menjaga redesign admin tetap mengikuti alur yang benar. Sumber audit: `app/Config/Routes.php`, `app/Controllers/AdminController.php`, `app/Controllers/Analytics.php`, `app/Controllers/MarketplaceController.php`, `app/Services/AdminRbacService.php`, `app/Filters/AdminFilter.php`, `app/Views/admin/`, dan sidebar desktop/mobile.

## Prinsip kerja redesign

- UI boleh dirapikan, tetapi route, aksi POST, validasi, status, hak akses, data, dan urutan transaksi tidak boleh berubah tanpa audit alur dan persetujuan.
- Petakan dahulu: tujuan layar → siapa yang mengakses → data yang dibaca → aksi → hasil/status → dokumen/lanjutan → permission.
- Gaya storefront yang diminta: clean, elegant, luxury, tipografi ringkas, hierarki jelas, ruang lapang, dan mengurangi card dekoratif. Terapkan sebagai bahasa visual, bukan menyalin pola belanja ke layar operasional yang butuh tabel/form padat.
- Jangan menganggap daftar menu sidebar sebagai seluruh fitur. Route dan view menunjukkan modul operasional tambahan yang tidak muncul di kedua navigasi saat ini.

## Peta modul dan alur fitur

### 1. Dashboard
- Route: `GET /admin`.
- Layar: `admin/dashboard.php`.
- Tujuan: ringkasan operasional dan akses awal admin.
- Izin: `dashboard`.

### 2. Katalog dan produk
- Route utama: `/admin/product`, `/admin/producttable`, `/admin/addproduct`, `/admin/editproduct/{id}`, POST tambah/edit, aktivasi, hapus, bulk edit, SEO check.
- Layar terkait: `all.php`, `allTable.php`, `add.php`, `bulkEdit.php`, `gantiGambar.php`, `labelBarang.php`.
- Alur: daftar/tabel → tambah atau edit → validasi data + foto/varian → simpan; admin dapat mengaktifkan/nonaktifkan dan menghapus; bulk edit untuk beberapa produk.
- Foto: batas/validasi tipe dan ukuran pada controller; UI `add.php` melakukan resize/konversi browser; simpan turunan WebP; mekanisme lokal/R2 berada di service/controller. Aksi gambar legacy juga ada di `/changepic/{id}` dan `/gantilokasi/{id}`.
- Varian mengikat nama, kode warna, stok, urutan foto, cover, dan pilihan di katalog/detail. Stok pada edit mengikuti alur mutasi, bukan diedit langsung.
- Izin: `products`.

### 3. Preview gambar mega-menu
- Route: GET/POST `/admin/menu-previews`.
- Layar: `menuPreviews.php`.
- Admin memilih satu produk aktif sebagai preview untuk pasangan koleksi + jenis; server memvalidasi produk masih aktif dan cocok dengan pasangan tersebut.
- Preview storefront dibangun oleh `Pages::getNavbarData()` dan `menuPreviewImageUrl()`; jangan mengubah pemetaan produk/foto saat hanya mengubah styling.
- Izin: `products`.

### 4. Stok dan mutasi
- Route: `/admin/mutasi`, `/admin/mutasiconfirm`, tambah, approve, deny, label barang.
- Layar: `mutasi.php`, `mutasiConfirm.php`, `labelBarang.php`.
- Alur: staf mengajukan mutasi → petugas berwenang memeriksa/approve atau deny → saldo stok dan label mengikuti hasil mutasi.
- Izin: `mutasi`.

### 5. Pesanan online dan marketplace
- Pesanan website: `/admin/order/online`, tambah pesanan, edit resi, reprint, surat jalan, tandai surat dicetak.
- Marketplace admin: `/admin/marketplace`, edit dan konfirmasi transaksi marketplace.
- Workspace marketplace terpisah di `/market/*`: katalog, pencarian, cart, submit pesanan, reduce cart.
- Layar terkait: `order.php`, `orderAdd.php`, `marketplace.php`, `reprint.php`, `suratJalanEdit.php`.
- Izin: `orders_online`; workspace `/market/*` memiliki permission `marketplace` tersendiri pada RBAC.

### 6. Pesanan offline
- Route: `/admin/order/offline/{jenis}`, form tambah, update, finalize, koreksi SP, invoice, invoice DP, surat offline, surat jalan offline (buat/edit/finalize).
- Layar terkait: `orderOffline.php`, `orderOfflineAdd.php`, `suratOffline.php`, `suratInvoice.php`, `suratKoreksi.php`.
- Alur meliputi draft/input → update item → finalisasi → dokumen invoice/DP/SP dan pengiriman. Surat jalan offline memakai alur item/kuantitas dan finalisasi tersendiri.
- Izin: `orders_offline`.

### 7. Project Interior
- Route: `/admin/project-interior`, tambah, detail, pembayaran, invoice pembayaran, invoice pengiriman, surat jalan.
- Layar: `projectInteriorList.php`, `projectInteriorAdd.php`, `projectInteriorDetail.php`, `projectInteriorInvoicePengiriman.php`.
- Alur: buat project → kelola detail dan pembayaran → buat invoice pembayaran/pengiriman serta surat jalan. Shared surat jalan mengotorisasi berdasarkan sumber project interior atau order offline.
- Izin: `project_interior`.

### 8. Konten, home, dan partner
- Artikel: daftar/kategori, tambah/edit/hapus, galeri artikel.
- Home: `/admin/homelayout`, ubah layout/banner/slider, foto responsif desktop/mobile, dan halaman ganti gambar.
- Partner: CRUD partner.
- Layar: `artikel.php`, `addArtikel.php`, `editArtikel.php`, `editArtikelBaru.php`, `homeLayout.php`, `partners.php`.
- Izin utama: `content`.

### 9. Voucher dan gratis ongkir
- Voucher: CRUD, toggle aktif/nonaktif, lihat pemakaian, hapus catatan pemakaian.
- Gratis ongkir: lihat dan ubah wilayah/aturan.
- Layar: `voucher.php`, `voucher_usage.php`, `freeShipping.php`.
- Izin: `vouchers`, `shipping`.

### 10. Pelanggan dan administrasi akses
- Akun pelanggan: daftar/status, suspend, reinstate; layar `customers.php`.
- Role & akses: definisi role, permission, assignment user, hapus assignment, sinkronisasi role; layar `rbac.php`.
- Log aktivitas: layar `activityLog.php`; filter `AdminFilter::after()` mencatat request admin yang memenuhi aturan AuditLogService.
- Cleanup spam: lihat dan jalankan pembersihan akun; layar `spamCleanup.php`.
- Permission: `customers`, `activity_log`, `spam_cleanup`; RBAC hanya superadmin `role=1`.

### 11. Analytics dan integrasi
- Analytics di `/analytics`: ringkasan/live, ekspor CSV/PDF, tambah/hapus blacklist.
- Meta CAPI: pengaturan Pixel/token/Graph API/test event; layar `metaCapi.php`.
- Izin: `analytics`, `meta_capi`.

## Guard dan role yang terverifikasi di kode

- `AdminFilter` mewajibkan sesi login aktif. Role `2` dihentikan dari akses admin. Role `1` adalah superadmin dan melewati cek permission. Role admin/staf lain perlu assignment RBAC yang sesuai.
- Nama role `Admin` atau `Admin Operasional` memperoleh semua permission operasional kecuali `meta_capi` dan `spam_cleanup`.
- Role & Akses dibatasi untuk `role=1`; path tak dikenal yang tidak terpetakan ke permission hanya dapat diakses superadmin.
- Grup `/admin` memasang `adminFilter` dan CSRF. Filter setelah request mencatat aktivitas yang lolos kriteria audit.
- `AdminRbacService` memetakan prefix route ke permission; shared offline/interior shipment memilih permission berdasarkan record sumber.

## Temuan navigasi penting sebelum redesign

Desktop (`admin/navbar.php`) dan mobile (`admin/navbarHpAdmin.php`) saat ini tidak menampilkan semua modul yang tersedia di route/RBAC. Di antara yang ada di permission/route tetapi tidak tercantum di navigasi utama: pesanan offline, Project Interior, mutasi stok, workspace marketplace, dan cleanup spam. Ini bukan bukti fiturnya tidak dipakai; bisa jadi diakses lewat tautan dalam layar atau menu lama. Sebelum redesign navigasi, cocokkan dengan staf/pemilik dan audit tautan internal.

## Batas bukti

Peta ini merekam permukaan route, view, dan guard yang ditemukan di repo lokal. Ia belum membuktikan seluruh perilaku produksi, data aktif, integrasi eksternal, variasi status bisnis, atau hak tiap akun produksi. Sebelum perubahan flow, periksa method controller/model terkait dan uji jalur berdasarkan permission; sebelum perubahan visual, pertahankan kontrak data/route yang ada.

## Temuan audit lanjutan dan perbaikan lokal (27 Oktober 2026)

### A. Akses Foto Preview Menu tidak selaras dengan menu dan permission (bug terverifikasi dari kode)

- Route GET/POST ada di `app/Config/Routes.php` sebagai `/admin/menu-previews`; sebelum perbaikan, desktop/mobile sidebar menampilkan tautan untuk pemilik izin `products`.
- Sebelumnya `AdminRbacService::routePermission()` belum memetakan prefix `admin/menu-previews`; `canAccessPath()` memperlakukan permission yang tidak terpetakan sebagai superadmin-only.
- Status: diperbaiki di workspace. Prefix `admin/menu-previews` dipetakan ke `products` dan regression test memastikan GET/POST alias mendapat permission yang sama.

### B. Hak akses halaman Perbaikan File Gambar berpotensi salah klasifikasi

- Route GET `/admin/changepic` membuka `AdminController::changePic()` → view `admin/gantiGambar.php`; view menyebut perbaikan file fisik gambar **produk** dan memanggil POST `/gantilokasi/{id}`.
- Sebelumnya `routePermission()` memetakan `admin/changepic` ke `content`, sedangkan endpoint mutasi `/gantilokasi/{id}` dipetakan ke `products`.
- Status: diselaraskan ke `products`, sesuai fungsi halaman dan izin endpoint perbaikan file. Tautan muncul di menu Produk desktop dan drawer mobile.

### C. Dashboard ditampilkan tanpa memeriksa permission dashboard

- Sidebar desktop dan quick-menu/drawer mobile menampilkan Dashboard/Home dengan `show=true`, sedangkan `/admin` mewajibkan permission `dashboard` untuk selain superadmin.
- Status: diperbaiki. Dashboard/Home di sidebar desktop, quick-menu dan drawer mobile mengikuti permission `dashboard`.

### D. Modul operasional tersedia di route/RBAC tetapi tidak ada di navigasi utama

- Tidak ditemukan tautan utama dari `admin/navbar.php` dan `admin/navbarHpAdmin.php` untuk mutasi stok, pesanan offline, Project Interior, cleanup spam, dan workspace `/market/product`.
- Status: ditambahkan ke navigasi desktop dan drawer mobile dengan permission masing-masing; workspace Marketplace tetap memakai izin `marketplace`, terpisah dari halaman Marketplace Admin yang berada di bawah `orders_online`.

### E. Catatan permission khusus

- Status: ditampilkan secara eksplisit hanya untuk `role=1`; permission `rbac` tidak ditambahkan ke izin yang dapat diberikan ke role staf.
- `/market/*` memakai `MarketFilter`: akses dibatasi role `5` dengan permission `marketplace`; jangan menyamakan dengan halaman `/admin/marketplace` yang saat ini memakai `orders_online`.
- Audit lanjutan menemukan `MarketFilter` hanya memeriksa `isLogin`, tidak memvalidasi status akun aktif seperti `AdminFilter`. Filter workspace marketplace kini memeriksa `active === '1'` dan mengakhiri sesi yang sudah tidak aktif sebelum melanjutkan pemeriksaan role/izin.
- Shared surat jalan sebelumnya menentukan izin sumber dengan JOIN antara dua kolom teks (`project_interior.kode_sj` dan `surat_jalan.id_pesanan`). Karena instalasi lama memakai kolasi kolom yang berbeda, jalur ini berisiko gagal saat pemeriksaan izin. Lookup kini mengambil nomor pesanan lalu membandingkan ke parameter terikat, serta menolak akses secara fail-closed jika lookup gagal.
- Pemeriksaan status admin sebelumnya hanya mempercayai `active` dan `role` di session. Jika akun dinonaktifkan atau role superadmin diubah setelah login, session lama dapat tetap lolos. `AdminFilter` dan `MarketFilter` kini memvalidasi role/status terhadap baris `user` terkini dan mengakhiri session saat tidak cocok; kegagalan query juga fail-closed.
- Audit login menemukan role selain superadmin yang memiliki assignment RBAC dapat jatuh ke cabang admin dan diberi session role staf meski role basis datanya bukan role staf aktif. Cabang login kini hanya menerima role `5` atau role legacy `0/3/4` dengan assignment eksplisit; role legacy dipromosikan ke `5` sebelum session diberikan, sedangkan role gudang `2` dan role tak dikenal ditolak.
- Endpoint publik `POST /track/hit` sebelumnya mempercayai header `CF-Connecting-IP`/`X-Forwarded-For` dari request langsung dan menerima user-agent yang dikirim klien, sehingga pembatasan berbasis IP dapat dipalsukan. Endpoint kini memakai alamat klien yang diselesaikan CI4 dari proxy tepercaya, memberi throttle 120 request/menit/IP, mengabaikan bot dari user-agent request, serta membersihkan dan membatasi panjang path. Daftar proxy Cloudflare tercatat di `app/Config/App.php` dan perlu mengikuti daftar resmi Cloudflare.
- HTTPS global dan cookie `Secure` kini aktif hanya pada `ENVIRONMENT=production`, sehingga produksi menegakkan HTTPS dan cookie autentikasi tidak terkirim melalui HTTP; pengembangan lokal HTTP tetap dapat dipakai.
- Validasi lokal setelah perubahan: semua file PHP yang disentuh lolos `php -l`; `vendor/bin/phpunit.bat` lulus 88 test / 421 assertion. PHPUnit mengeluarkan warning lingkungan karena driver coverage tidak tersedia dan satu deprecation; tidak ada test gagal.
