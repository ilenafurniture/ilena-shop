<?php

namespace App\Services;

final class ProductInputPolicy
{
    /** Validate the complete payload sent by the add/edit product form. */
    public static function validate(array $input): ?string
    {
        $name = trim((string) ($input['nama'] ?? ''));
        if ($name === '' || mb_strlen($name, 'UTF-8') > 191) {
            return 'Nama produk wajib diisi dan maksimal 191 karakter.';
        }

        $price = $input['harga'] ?? null;
        if (!is_numeric($price) || !is_finite((float) $price) || (float) $price < 0) {
            return 'Harga produk harus berupa angka nol atau lebih.';
        }

        $discount = $input['diskon'] ?? 0;
        if (!is_numeric($discount) || !is_finite((float) $discount) || (float) $discount < 0 || (float) $discount > 100) {
            return 'Diskon harus berada di antara 0 sampai 100 persen.';
        }

        $description = self::decodeArray($input['deskripsi'] ?? null);
        if ($description === null || !isset($description['dimensi']['asli'], $description['dimensi']['paket'])) {
            return 'Deskripsi atau data dimensi produk tidak valid.';
        }
        foreach (['asli', 'paket'] as $dimensionType) {
            foreach (['panjang', 'lebar', 'tinggi', 'berat'] as $field) {
                $value = $description['dimensi'][$dimensionType][$field] ?? null;
                if (!is_numeric($value) || !is_finite((float) $value) || (float) $value < 0) {
                    return 'Dimensi dan berat produk harus berupa angka nol atau lebih.';
                }
            }
        }

        $variants = self::decodeArray($input['varian'] ?? null);
        if ($variants === null || count($variants) < 1 || count($variants) > 25) {
            return 'Produk harus memiliki 1 sampai 25 varian.';
        }
        $seenNames = [];
        $totalImageReferences = 0;
        foreach ($variants as $variant) {
            if (!is_array($variant)) {
                return 'Format varian produk tidak valid.';
            }
            $variantName = trim((string) ($variant['nama'] ?? ''));
            $variantCode = trim((string) ($variant['kode'] ?? ''));
            $stock = $variant['stok'] ?? null;
            if ($variantName === '' || $variantCode === '' || !preg_match('/^\d+$/', (string) $stock)) {
                return 'Setiap varian wajib memiliki nama, kode, dan stok bilangan bulat nol atau lebih.';
            }
            if ((float) $stock > 2147483647) {
                return 'Jumlah stok varian melebihi batas.';
            }
            $key = mb_strtolower($variantName, 'UTF-8');
            if (isset($seenNames[$key])) {
                return 'Nama varian harus unik.';
            }
            $seenNames[$key] = true;

            $slots = array_filter(array_map('trim', explode(',', (string) ($variant['urutan_gambar'] ?? ''))), static fn($slot) => $slot !== '');
            if ($slots === []) {
                return 'Setiap varian wajib memiliki minimal satu foto.';
            }
            foreach ($slots as $slot) {
                if (!ctype_digit($slot) || (int) $slot < 1 || (int) $slot > 9999) {
                    return 'Urutan foto varian tidak valid.';
                }
            }
            $totalImageReferences += count($slots);
        }
        if ($totalImageReferences > 25) {
            return 'Maksimal 25 foto produk untuk semua varian.';
        }

        $useSchedule = !empty($input['pakai_jadwal_diskon']);
        if ($useSchedule) {
            $start = strtotime(str_replace('T', ' ', (string) ($input['diskon_mulai'] ?? '')));
            $end = strtotime(str_replace('T', ' ', (string) ($input['diskon_selesai'] ?? '')));
            if ($start === false || $end === false || $end <= $start) {
                return 'Jadwal diskon harus memiliki waktu mulai dan selesai yang valid; waktu selesai harus setelah waktu mulai.';
            }
        }

        return null;
    }

    private static function decodeArray($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }
        if (!is_string($value) || trim($value) === '') {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) && json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
