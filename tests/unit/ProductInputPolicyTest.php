<?php

namespace Tests\Unit;

use App\Services\ProductInputPolicy;
use PHPUnit\Framework\TestCase;

final class ProductInputPolicyTest extends TestCase
{
    private function validPayload(): array
    {
        return [
            'nama' => 'Kursi Ilena',
            'harga' => '1250000',
            'diskon' => '10',
            'deskripsi' => json_encode([
                'deskripsi' => '<p>Kursi nyaman</p>',
                'dimensi' => [
                    'asli' => ['panjang' => '100', 'lebar' => '50', 'tinggi' => '80', 'berat' => '12'],
                    'paket' => ['panjang' => '110', 'lebar' => '60', 'tinggi' => '90', 'berat' => '14'],
                ],
            ]),
            'varian' => json_encode([
                ['nama' => 'Oak', 'kode' => 'KUR-OAK', 'stok' => '5', 'urutan_gambar' => '1,2'],
                ['nama' => 'Walnut', 'kode' => 'KUR-WAL', 'stok' => '0', 'urutan_gambar' => '3'],
            ]),
            'pakai_jadwal_diskon' => '0',
        ];
    }

    public function testAcceptsCompleteProductPayload(): void
    {
        self::assertNull(ProductInputPolicy::validate($this->validPayload()));
    }

    public function testAcceptsEnglishProductAndVariantLabelsWithinLimits(): void
    {
        $payload = $this->validPayload();
        $description = json_decode($payload['deskripsi'], true);
        $description['nama_en'] = 'Ilena Dining Chair';
        $description['deskripsi_en'] = 'A comfortable chair.';
        $description['perawatan_en'] = 'Wipe with a soft cloth.';
        $payload['deskripsi'] = json_encode($description);
        $variants = json_decode($payload['varian'], true);
        $variants[0]['nama_en'] = 'Natural Oak';
        $payload['varian'] = json_encode($variants);

        self::assertNull(ProductInputPolicy::validate($payload));
    }

    public function testRejectsEnglishProductAndVariantLabelsOverLimits(): void
    {
        $payload = $this->validPayload();
        $description = json_decode($payload['deskripsi'], true);
        $description['nama_en'] = str_repeat('x', 181);
        $payload['deskripsi'] = json_encode($description);
        self::assertSame('Konten bahasa Inggris pada produk melebihi batas karakter.', ProductInputPolicy::validate($payload));

        $payload = $this->validPayload();
        $variants = json_decode($payload['varian'], true);
        $variants[0]['nama_en'] = str_repeat('x', 101);
        $payload['varian'] = json_encode($variants);
        self::assertSame('Nama varian Inggris maksimal 100 karakter.', ProductInputPolicy::validate($payload));
    }

    public function testRejectsInvalidPriceAndDiscount(): void
    {
        $payload = $this->validPayload();
        $payload['harga'] = '-1';
        self::assertSame('Harga produk harus berupa angka nol atau lebih.', ProductInputPolicy::validate($payload));

        $payload = $this->validPayload();
        $payload['diskon'] = '101';
        self::assertSame('Diskon harus berada di antara 0 sampai 100 persen.', ProductInputPolicy::validate($payload));
    }

    public function testRejectsDuplicateVariantAndInvalidImageSlot(): void
    {
        $payload = $this->validPayload();
        $variants = json_decode($payload['varian'], true);
        $variants[1]['nama'] = ' oak ';
        $payload['varian'] = json_encode($variants);
        self::assertSame('Nama varian harus unik.', ProductInputPolicy::validate($payload));

        $variants[1]['nama'] = 'Walnut';
        $variants[1]['urutan_gambar'] = '0';
        $payload['varian'] = json_encode($variants);
        self::assertSame('Urutan foto varian tidak valid.', ProductInputPolicy::validate($payload));
    }

    public function testAllowsSparseImageSlotsButCapsTotalPhotoCount(): void
    {
        $payload = $this->validPayload();
        $variants = json_decode($payload['varian'], true);
        $variants[0]['urutan_gambar'] = '31,48';
        $variants[1]['urutan_gambar'] = '87';
        $payload['varian'] = json_encode($variants);
        self::assertNull(ProductInputPolicy::validate($payload));

        $variants[0]['urutan_gambar'] = implode(',', range(1, 25));
        $variants[1]['urutan_gambar'] = '87';
        $payload['varian'] = json_encode($variants);
        self::assertSame('Maksimal 25 foto produk untuk semua varian.', ProductInputPolicy::validate($payload));
    }

    public function testRejectsMalformedDimensionsAndSchedule(): void
    {
        $payload = $this->validPayload();
        $description = json_decode($payload['deskripsi'], true);
        $description['dimensi']['asli']['berat'] = '-2';
        $payload['deskripsi'] = json_encode($description);
        self::assertSame('Dimensi dan berat produk harus berupa angka nol atau lebih.', ProductInputPolicy::validate($payload));

        $payload = $this->validPayload();
        $payload['pakai_jadwal_diskon'] = '1';
        $payload['diskon_mulai'] = '2026-10-26T12:00';
        $payload['diskon_selesai'] = '2026-10-26T11:00';
        self::assertSame('Jadwal diskon harus memiliki waktu mulai dan selesai yang valid; waktu selesai harus setelah waktu mulai.', ProductInputPolicy::validate($payload));
    }
}
