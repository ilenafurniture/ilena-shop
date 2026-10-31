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
