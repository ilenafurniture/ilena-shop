<?php

if (! function_exists('ilena_store_path')) {
    /** Build an internal storefront URL while preserving the active locale. */
    function ilena_store_path(string $path = '/', ?string $locale = null): string
    {
        if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $path)) {
            return $path;
        }

        [$pathname, $query] = array_pad(explode('?', $path, 2), 2, null);
        $pathname = '/' . ltrim((string) $pathname, '/');
        $locale ??= ilena_current_store_locale();
        $isEnglish = $locale === 'en';

        if ($isEnglish && ! preg_match('#^/en(?:/|$)#', $pathname)) {
            $pathname = '/en' . ($pathname === '/' ? '' : $pathname);
        } elseif (! $isEnglish && preg_match('#^/en(?:/|$)#', $pathname)) {
            $pathname = (string) preg_replace('#^/en(?=/|$)#', '', $pathname, 1);
            $pathname = $pathname === '' ? '/' : $pathname;
        }

        return $pathname . ($query !== null && $query !== '' ? '?' . $query : '');
    }
}

if (! function_exists('ilena_current_store_locale')) {
    /** Use the explicit storefront URL prefix as the source of truth for navigation. */
    function ilena_current_store_locale(): string
    {
        $path = trim((string) service('uri')->getPath(), '/');
        return preg_match('#^en(?:/|$)#i', $path) === 1 ? 'en' : 'id';
    }
}

if (! function_exists('ilena_product_display_name')) {
    /** Return an editor-authored English name when available, without changing stored names or URLs. */
    function ilena_product_display_name(array $product, ?string $locale = null): string
    {
        $locale ??= ilena_current_store_locale();
        $name = trim((string) ($product['nama'] ?? ''));
        if ($locale !== 'en') return $name;
        $description = $product['deskripsi'] ?? [];
        if (is_string($description)) $description = json_decode($description, true) ?: [];
        $englishName = trim((string) (is_array($description) ? ($description['nama_en'] ?? '') : ''));
        return $englishName !== '' ? $englishName : $name;
    }
}

if (! function_exists('ilena_catalog_type_label')) {
    /** Translate common fixed catalog labels while preserving database filter values. */
    function ilena_catalog_type_label(string $name, ?string $locale = null): string
    {
        $locale ??= ilena_current_store_locale();
        if ($locale !== 'en') return ucfirst($name);
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $name)));
        $labels = [
            'lemari' => 'Cabinet', 'lemari pakaian' => 'Wardrobe', 'wardrobe' => 'Wardrobe',
            'rak buku' => 'Bookshelf', 'bookshelf' => 'Bookshelf', 'dresser' => 'Dresser',
            'meja makan' => 'Dining Table', 'kursi makan' => 'Dining Chair', 'meja' => 'Table',
            'sofa' => 'Sofa', 'tempat tidur' => 'Bed', 'dipan' => 'Bed Frame',
            'meja rias' => 'Dressing Table', 'nakas' => 'Nightstand', 'meja nakas' => 'Nightstand',
            'meja tv' => 'TV Console', 'bufet tv' => 'TV Console', 'meja konsol' => 'Console Table', 'meja kopi' => 'Coffee Table',
            'meja tamu' => 'Coffee Table', 'rak' => 'Shelf', 'ruang tamu' => 'Living Room',
            'ruang keluarga' => 'Family Room', 'ruang tidur' => 'Bedroom', 'kamar tidur' => 'Bedroom',
        ];
        return $labels[$normalized] ?? ucfirst($name);
    }
}

if (! function_exists('ilena_variant_display_name')) {
    /** Translate common color/finish labels for display only; submitted variant values remain unchanged. */
    function ilena_variant_display_name(string $name, ?string $locale = null, ?string $englishName = null): string
    {
        $locale ??= ilena_current_store_locale();
        if ($locale !== 'en') return $name;
        $englishName = trim((string) $englishName);
        if ($englishName !== '') return $englishName;
        $normalized = strtolower(trim($name));
        return [
            'putih' => 'White', 'hitam' => 'Black', 'cokelat' => 'Brown', 'coklat' => 'Brown',
            'abu-abu' => 'Gray', 'abu abu' => 'Gray', 'krem' => 'Cream', 'krim' => 'Cream',
            'biru' => 'Blue', 'merah' => 'Red', 'hijau' => 'Green', 'natural' => 'Natural',
            'alami' => 'Natural', 'oak' => 'Oak', 'walnut' => 'Walnut', 'sonoma' => 'Sonoma',
        ][$normalized] ?? $name;
    }
}

if (! function_exists('ilena_variant_translation_ready')) {
    /** A custom label needs its own English name; known finish/color labels use the built-in map. */
    function ilena_variant_translation_ready(string $name, ?string $englishName = null): bool
    {
        if (trim((string) $englishName) !== '') return true;
        return ilena_variant_display_name($name, 'en') !== $name;
    }
}

if (! function_exists('ilena_product_variant_display_name')) {
    /** Resolve an authored English variant label while keeping the stored inventory name unchanged. */
    function ilena_product_variant_display_name(array $product, string $variantName, ?string $locale = null): string
    {
        $locale ??= ilena_current_store_locale();
        $variants = $product['varian'] ?? [];
        if (is_string($variants)) $variants = json_decode($variants, true) ?: [];
        if ($locale === 'en' && is_array($variants)) {
            foreach ($variants as $variant) {
                if (! is_array($variant)
                    || strcasecmp(trim((string) ($variant['nama'] ?? '')), trim($variantName)) !== 0
                ) continue;

                return ilena_variant_display_name($variantName, $locale, (string) ($variant['nama_en'] ?? ''));
            }
        }

        return ilena_variant_display_name($variantName, $locale);
    }
}

if (! function_exists('ilena_localize_article_record')) {
    /** Use editor-authored English article fields when present; otherwise preserve the original Indonesian content. */
    function ilena_localize_article_record(array $article, ?string $locale = null): array
    {
        $locale ??= ilena_current_store_locale();
        if ($locale !== 'en') return $article;

        foreach (['judul', 'deskripsi', 'keywords', 'isi'] as $field) {
            $translated = trim((string) ($article[$field . '_en'] ?? ''));
            if ($translated !== '') $article[$field] = $translated;
        }

        $category = $article['kategori'] ?? '';
        if (is_array($category)) $category = $category[0] ?? '';
        $article['kategori_label'] = ilena_article_category_label((string) $category, $locale);

        return $article;
    }
}

if (! function_exists('ilena_article_category_label')) {
    function ilena_article_category_label(string $category, ?string $locale = null): string
    {
        $locale ??= ilena_current_store_locale();
        $category = trim($category);
        if ($locale !== 'en') return ucwords($category);

        return [
            'edukasi' => 'Guides',
            'tips & trik' => 'Tips & ideas',
            'fun fact' => 'Fun facts',
            'rekomendasi' => 'Recommendations',
        ][strtolower($category)] ?? ucwords($category);
    }
}

if (! function_exists('ilena_article_category_from_slug')) {
    /** Convert public article-category slugs to their stored category names. */
    function ilena_article_category_from_slug(string $slug): string
    {
        $slug = strtolower(trim(rawurldecode($slug)));
        if (in_array($slug, ['tips-trik', 'tips-and-trik', 'tips-ideas', 'tips-and-ideas'], true)) {
            return 'tips & trik';
        }

        return trim(str_replace(['@', '-'], ['&', ' '], $slug));
    }
}

if (! function_exists('ilena_localize_notice')) {
    /** Translate common storefront flash/notice copy at render time, including messages emitted by legacy Indonesian handlers. */
    function ilena_localize_notice(string $message, ?string $locale = null): string
    {
        $locale ??= ilena_current_store_locale();
        if ($locale !== 'en') return $message;

        $message = trim($message);
        $translations = [
            'Ada produk/varian yang tidak tersedia dan telah dikeluarkan dari keranjang. Periksa kembali pesanan Anda.' => 'Unavailable products or variants were removed from your cart. Please review your order.',
            'Jumlah produk disesuaikan dengan stok terbaru. Periksa jumlah dan subtotal sebelum melanjutkan.' => 'Quantities were updated to match current stock. Review the quantities and subtotal before continuing.',
            'Produk sudah tidak tersedia. Keranjang tidak berubah.' => 'This product is no longer available. Your cart has not changed.',
            'Varian tidak tersedia atau stoknya habis. Keranjang tidak berubah.' => 'This variant is unavailable or out of stock. Your cart has not changed.',
            'Keranjangmu kosong.' => 'Your cart is empty.',
            'Ada produk di keranjang yang sudah tidak tersedia.' => 'A product in your cart is no longer available.',
            'Data varian produk tidak valid.' => 'The product variant is invalid.',
            'Jumlah produk di keranjang tidak valid.' => 'A product quantity in your cart is invalid.',
            'Ada varian produk yang sudah tidak tersedia.' => 'A product variant is no longer available.',
            'Email tidak valid atau termasuk email sementara. Gunakan email pribadi yang aktif.' => 'This email address is invalid or temporary. Please use an active personal email address.',
            'Email pemesan tidak valid atau termasuk email sementara. Perbarui alamat dengan email pribadi yang aktif.' => 'The order email is invalid or temporary. Update the address with an active personal email address.',
            'Email yang dimasukan harus sesuai dengan email akun member Anda' => 'The email must match your member account.',
            'Alamat tidak ditemukan.' => 'Address not found.',
            'Pilihan kurir belum tersedia untuk alamat ini. Silakan cek alamat atau hubungi admin.' => 'Shipping options are not available for this address. Check the address or contact Customer Care.',
            'Keranjang kosong.' => 'Your cart is empty.',
            'Voucher tidak memenuhi syarat.' => 'This voucher’s requirements have not been met.',
            'Metode pembayaran tidak valid.' => 'The selected payment method is invalid.',
            'Kode voucher kosong.' => 'Enter a voucher code.',
            'Alamat tidak valid.' => 'The address is invalid.',
            'Keranjang kosong.' => 'Your cart is empty.',
            'Kode voucher tidak ditemukan.' => 'Voucher code not found.',
            'Voucher sedang nonaktif.' => 'This voucher is currently inactive.',
            'Voucher belum mulai berlaku.' => 'This voucher is not active yet.',
            'Voucher sudah berakhir.' => 'This voucher has expired.',
            'Belanja belum mencapai minimal voucher.' => 'Your order has not reached the minimum spend for this voucher.',
            'Voucher sudah mencapai limit pemakaian.' => 'This voucher has reached its usage limit.',
            'Voucher ini hanya bisa dipakai sekali per pengguna.' => 'This voucher can only be used once per customer.',
            'Voucher ini hanya dapat digunakan sekali per pengguna.' => 'This voucher can only be used once per customer.',
            'Voucher khusus member baru.' => 'This voucher is for new members only.',
            'Voucher khusus member lama.' => 'This voucher is for existing members only.',
            'Voucher ini khusus pengguna tertentu.' => 'This voucher is available to selected customers only.',
            'Voucher berhasil diterapkan.' => 'Voucher applied successfully.',
            'Voucher valid.' => 'Voucher is valid.',
            'Voucher sedang tidak dapat diverifikasi. Coba kembali sebentar lagi.' => 'The voucher could not be verified right now. Please try again shortly.',
            'Voucher ini sudah pernah kamu pakai.' => 'You have already used this voucher.',
            'Token checkout tidak valid.' => 'The checkout token is invalid.',
            'Token tidak valid' => 'The token is invalid.',
            'Token kadaluarsa, silakan ulangi pembayaran' => 'The token has expired. Please retry payment.',
            'Permintaan checkout sudah digunakan atau kedaluwarsa. Periksa status pesanan sebelum mencoba lagi.' => 'This checkout request has already been used or has expired. Check your order status before trying again.',
            'Alamat tidak ditemukan, silakan ulangi.' => 'Address not found. Please select it again.',
            'Kurir tidak ditemukan, silakan pilih ulang.' => 'Shipping service not found. Please select it again.',
            'Data kartu belum lengkap' => 'Card details are incomplete.',
            'Keranjang atau pilihan pembayaran berubah. Periksa kembali ringkasan sebelum membayar.' => 'Your cart or payment selection has changed. Review the order summary before paying.',
            'Sesi checkout kedaluwarsa. Muat ulang pembayaran sebelum melanjutkan.' => 'Your checkout session has expired. Reload the payment page before continuing.',
            'Voucher baru saja mencapai batas pemakaian.' => 'This voucher has just reached its usage limit.',
            'Voucher sedang tidak dapat diamankan. Coba kembali sebentar lagi.' => 'The voucher could not be reserved right now. Please try again shortly.',
            'Stok belum dapat diamankan. Coba kembali setelah beberapa saat.' => 'Stock could not be reserved. Please try again shortly.',
            'Stok berubah. Perbarui keranjang sebelum membayar.' => 'Stock has changed. Update your cart before paying.',
            'Pesanan belum tersimpan; belum ada permintaan pembayaran dikirim.' => 'Your order was not saved, and no payment request was sent.',
            'Pembayaran belum mendapat konfirmasi. Pesanan belum ditampilkan sebagai berhasil; muat ulang dan periksa status sebelum mencoba lagi.' => 'Payment has not been confirmed, so the order is not marked as successful. Reload and check its status before trying again.',
            'Transaksi gagal' => 'The transaction failed.',
            'Form login kedaluwarsa. Silakan muat ulang halaman dan coba lagi.' => 'Your sign-in form has expired. Reload the page and try again.',
            'Terlalu banyak percobaan login. Tunggu beberapa saat lalu coba lagi.' => 'Too many sign-in attempts. Please wait a moment and try again.',
            'Email atau kata sandi salah' => 'The email or password is incorrect.',
            'Akses admin belum aktif untuk email ini.' => 'Admin access is not enabled for this email address.',
            'Akses akun ini belum diatur. Hubungi Admin utama.' => 'Access for this account has not been configured. Please contact the primary administrator.',
            'Form pendaftaran kedaluwarsa atau tidak valid. Silakan muat ulang halaman.' => 'Your registration form has expired or is invalid. Reload the page and try again.',
            'Terlalu banyak percobaan pendaftaran. Coba lagi nanti.' => 'Too many registration attempts. Please try again later.',
            'Email verifikasi belum dapat dikirim. Coba lagi nanti.' => 'The verification email could not be sent. Please try again later.',
            'Pendaftaran belum berhasil disimpan. Silakan coba lagi.' => 'Your registration could not be saved. Please try again.',
            'Sesi verifikasi sudah berakhir. Silakan daftar/login ulang.' => 'Your verification session has expired. Please register or sign in again.',
            'Akun Anda ditangguhkan. Silakan hubungi admin.' => 'Your account is suspended. Please contact Customer Care.',
            'Terlalu banyak permintaan kode verifikasi. Coba lagi nanti.' => 'Too many verification code requests. Please try again later.',
            'Tunggu sebentar sebelum meminta kode OTP baru.' => 'Please wait before requesting a new verification code.',
            'Kamu telah keluar' => 'You have signed out.',
            'Kata sandi saat ini tidak sesuai.' => 'The current password is incorrect.',
            'Minimal 8 karakter. Kombinasikan setidaknya 3 jenis karakter (huruf besar, huruf kecil, angka, simbol); frasa 16+ karakter cukup memakai 2 jenis. Maksimal 72 byte.' => 'At least 8 characters. Combine 3 character types (uppercase, lowercase, numbers, symbols); a 16+ character passphrase may use 2 types. Maximum 72 bytes.',
            'Kata sandi minimal 8 karakter dan harus kuat: gunakan setidaknya 3 jenis karakter, atau frasa 16+ karakter dengan 2 jenis.' => 'Use at least 8 characters and combine 3 character types, or use a 16+ character passphrase with 2 types.',
            'Sandi yang terkonfirmasi tidak cocok' => 'The password confirmation does not match.',
            'Perubahan kata sandi gagal. Silakan coba kembali.' => 'Your password could not be changed. Please try again.',
            'Sandi Anda berhasil diubah' => 'Your password has been changed.',
            'Fitur notifikasi produk sedang disiapkan. Silakan hubungi Customer Care.' => 'Product alerts are being set up. Please contact Customer Care.',
            'Periksa kembali pilihan notifikasi dan persetujuan Anda.' => 'Review your alert selection and consent, then try again.',
            'Varian ini masih tersedia; notifikasi stok belum diperlukan.' => 'This variant is still in stock, so a stock alert is not needed yet.',
            'Pemberitahuan tersimpan. Kami akan mengirim email saat kondisi pilihan terpenuhi.' => 'Alert saved. We will email you when your selected condition is met.',
            'Fitur ulasan sedang disiapkan.' => 'Reviews are being set up.',
            'Terlalu banyak kiriman. Silakan coba kembali nanti.' => 'Too many submissions. Please try again later.',
            'Mohon lengkapi data dengan benar. Pesan harus 8–2.000 karakter.' => 'Please complete the form correctly. Your message must be 8–2,000 characters.',
            'Ulasan untuk produk dan pesanan ini sudah pernah dikirim.' => 'A review for this product and order has already been submitted.',
            'Silakan masuk ke akun member untuk bertanya atau mengirim ulasan produk.' => 'Please sign in to ask a product question or submit a review.',
        ];

        if (isset($translations[$message])) return $translations[$message];

        if (preg_match('/^Stok (.+) varian (.+) tidak mencukupi\.$/u', $message, $matches) === 1) {
            return 'There is not enough stock for ' . $matches[1] . ' (' . $matches[2] . ').';
        }
        if (preg_match('/^OTP telah dikirim ke email (.+) dan berlaku hingga (.+)$/u', $message, $matches) === 1) {
            return 'A verification code was sent to ' . $matches[1] . ' and is valid until ' . $matches[2] . '.';
        }
        if (preg_match('/^OTP telah diperbarui dan sudah dikirim kembali ke email (.+)$/u', $message, $matches) === 1) {
            return 'A new verification code was sent to ' . $matches[1] . '.';
        }
        if (preg_match('/^Email (.+) perlu diverifikasi$/u', $message, $matches) === 1) {
            return 'Please verify the email address ' . $matches[1] . '.';
        }
        if (preg_match('/^Akun Anda ditangguhkan (.+)\. Silakan hubungi admin\.$/u', $message, $matches) === 1) {
            return 'Your account is suspended ' . $matches[1] . '. Please contact Customer Care.';
        }
        if (preg_match('/^Voucher belum mulai berlaku \(mulai (.+)\)\.$/u', $message, $matches) === 1) {
            return 'This voucher is not active yet. It starts ' . $matches[1] . '.';
        }
        if (preg_match('/^Voucher sudah berakhir \(berlaku sampai (.+)\)\.$/u', $message, $matches) === 1) {
            return 'This voucher has expired. It was valid until ' . $matches[1] . '.';
        }
        if (preg_match('/^Belanja belum mencapai minimal voucher: Rp (.+)\. Tambahkan belanja hingga minimal Rp (.+)\.$/u', $message, $matches) === 1) {
            return 'Your order has not reached the voucher minimum of Rp ' . $matches[1] . '. Add items until your subtotal reaches at least Rp ' . $matches[2] . '.';
        }
        if (preg_match('/^Belanja belum mencapai minimal voucher: Rp (.+)\. Subtotal kamu: Rp (.+)$/u', $message, $matches) === 1) {
            return 'Your order has not reached the voucher minimum of Rp ' . $matches[1] . '. Your current subtotal is Rp ' . $matches[2] . '.';
        }
        if (preg_match('/^Voucher "(.+)" berhasil diklaim\.$/u', $message, $matches) === 1) {
            return 'Voucher "' . $matches[1] . '" has been claimed.';
        }

        return $message;
    }
}

if (! function_exists('ilena_localize_payment_guide')) {
    /** Translate built-in Midtrans payment instructions without changing stored payment data. */
    function ilena_localize_payment_guide(array $guide, string $locale): array
    {
        if ($locale !== 'en') {
            return $guide;
        }

        $name = trim((string) ($guide['nama'] ?? ''));
        $source = (string) ($guide['isi'] ?? '');
        $key = strtolower($name);
        $english = [
            'mybca' => 'myBCA',
            'bca mobile' => 'BCA Mobile',
            'klikbca' => 'KlikBCA',
            'klik bca' => 'KlikBCA',
            'm-bca' => 'BCA Mobile',
            'livin by mandiri' => 'Livin’ by Mandiri',
            'atm bca' => 'BCA ATM',
            'atm mandiri' => 'Mandiri ATM',
            'mandiri internet banking' => 'Mandiri Internet Banking',
            'atm bni' => 'BNI ATM',
            'bni internet banking' => 'BNI Internet Banking',
            'bni mobile banking' => 'BNI Mobile Banking',
            'atm bri' => 'BRI ATM',
            'ib bri' => 'BRI Internet Banking',
            'brimo' => 'BRImo',
            'atm permata/alto' => 'Permata/ALTO ATM',
            'atm cimb niaga' => 'CIMB Niaga ATM',
            'octo clicks' => 'OCTO Clicks',
            'octo mobile' => 'OCTO Mobile',
            'qris' => 'QRIS',
            'gopay' => 'GoPay',
            'pemesanan gudang' => 'Warehouse order',
            'pemesanan marketplace' => 'Marketplace order',
        ];
        $guide['nama'] = $english[$key] ?? $name;

        $steps = [
            'mybca' => '1. Log in to myBCA.<br>2. Choose Transfer, then Virtual Account.<br>3. Choose Transfer to a new recipient.<br>4. Enter the Virtual Account number from the online store and continue.<br>5. Select the funding account, enter the amount, and continue.<br>6. Review the transaction details and continue.<br>7. Enter your PIN to complete the payment.',
            'bca mobile' => '1. Log in to BCA mobile.<br>2. Choose m-Transfer, then BCA Virtual Account.<br>3. Enter the BCA Virtual Account number from the online store and tap Send.<br>4. Enter the amount.<br>5. Review the transaction details and confirm.<br>6. Enter your PIN to complete the payment.',
            'klikbca' => str_contains($source, 'KeyBCA')
                ? '1. Log in to KlikBCA.<br>2. Choose Fund Transfer, then Transfer to BCA Virtual Account.<br>3. Enter the BCA Virtual Account number and continue.<br>4. Enter the amount and continue.<br>5. Enter the KeyBCA response and submit.<br>6. The payment is complete.'
                : '1. Log in to KlikBCA.<br>2. Choose Fund Transfer, then Transfer to BCA Virtual Account.<br>3. Enter the BCA Virtual Account number and continue.<br>4. Enter the amount and continue.<br>5. Enter your KeyBCA response and submit.<br>6. The payment is complete.',
            'atm bca' => str_contains($source, 'Masukkan Kartu ATM')
                ? '1. Insert your ATM card and enter your PIN.<br>2. Choose Cash Withdrawal/Other Transactions.<br>3. Choose Other Transactions, then Transfer.<br>4. Choose BCA Virtual Account transfer.<br>5. Enter the BCA Virtual Account number and confirm.<br>6. Review the transaction details and confirm the payment.'
                : '1. Choose Other Transactions from the main menu.<br>2. Choose Transfer, then BCA Virtual Account.<br>3. Enter the BCA Virtual Account number.<br>4. Enter the amount and confirm.<br>5. Review the details to complete the payment.',
            'livin by mandiri' => '1. Choose Pay from the main menu.<br>2. Choose Ecommerce.<br>3. Select Midtrans as the service provider.<br>4. Enter the Virtual Account number in the payment code field.<br>5. Continue and confirm the details.<br>6. The payment is complete.',
            'atm mandiri' => '1. Choose Pay/Buy from the main menu.<br>2. Choose Other, then Multi Payment.<br>3. Enter the Midtrans company code: 70012.<br>4. Enter the payment code and confirm.<br>5. The payment is complete.',
            'mandiri internet banking' => '1. Choose Pay from the main menu.<br>2. Choose Multi Payment, then select the source account.<br>3. Select Midtrans as the service provider.<br>4. Enter the payment code and confirm.<br>5. The payment is complete.',
            'atm bni' => '1. Choose Other from the main menu, then Transfer.<br>2. Choose transfer to a BNI account.<br>3. Enter the payment account number and amount.<br>4. Review and confirm the payment.',
            'bni internet banking' => '1. Choose Transactions, then Transfer Information & Administration.<br>2. Set up the destination account and confirm.<br>3. Choose Transfer to a BNI account.<br>4. Enter the payment details and confirm.',
            'bni mobile banking' => '1. Choose Transfer.<br>2. Choose Virtual Account Billing.<br>3. Select the debit account to use.<br>4. Enter the Virtual Account number and confirm the payment.',
            'atm bri' => '1. Choose Other Transactions from the main menu.<br>2. Choose Payments, then Other.<br>3. Choose BRIVA.<br>4. Enter the BRIVA number and confirm the payment.',
            'ib bri' => '1. Choose Payments & Purchases.<br>2. Choose BRIVA.<br>3. Enter the BRIVA number and confirm the payment.',
            'brimo' => '1. Choose Payments.<br>2. Choose BRIVA.<br>3. Enter the BRIVA number and confirm the payment.',
            'klik bca' => '1. Choose Fund Transfer.<br>2. Choose Transfer to BCA Virtual Account.<br>3. Enter the BCA Virtual Account number.<br>4. Enter the amount and confirm the payment.',
            'm-bca' => '1. Choose m-Transfer.<br>2. Choose BCA Virtual Account.<br>3. Enter the BCA Virtual Account number.<br>4. Enter the amount and confirm the payment.',
            'atm permata/alto' => '1. Choose Other Transactions from the main menu.<br>2. Choose Payments, then Other Payments.<br>3. Choose Virtual Account.<br>4. Enter the Permata Virtual Account number and confirm the payment.',
            'atm cimb niaga' => '1. Choose Payments from the main menu.<br>2. Choose Virtual Account.<br>3. Enter the Virtual Account number and confirm the payment.',
            'octo clicks' => '1. Choose Bill Payments from the main menu.<br>2. Choose Mobile Virtual Account.<br>3. Enter the Virtual Account number and review the details.<br>4. Request an OTP to continue.<br>5. Enter the OTP sent to your phone and confirm the payment.',
            'octo mobile' => '1. Choose Transfer.<br>2. Choose Transfer to another CIMB Niaga account.<br>3. Select the funding source.<br>4. Enter the Virtual Account number and amount.<br>5. Follow the instructions to confirm the payment.',
            'qris' => '1. Open an app that supports QRIS payments.<br>2. Scan the QR code shown on the screen.<br>3. Confirm the payment in the app.',
            'gopay' => '1. Tap Pay now.<br>2. The Gojek or GoPay app will open.<br>3. Confirm the payment in the app.',
        ];
        if (isset($steps[$key])) {
            $guide['isi'] = $steps[$key];
        } elseif ($key === 'pemesanan gudang') {
            $guide['isi'] = 'Please contact our team to arrange payment for this warehouse order.';
        } elseif ($key === 'pemesanan marketplace') {
            $guide['isi'] = 'Please complete payment through the marketplace where you placed the order.';
        }

        return $guide;
    }
}
