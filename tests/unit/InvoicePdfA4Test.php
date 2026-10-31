<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Dompdf\Dompdf;

final class InvoicePdfA4Test extends CIUnitTestCase
{
    public function testCustomerInvoiceRendersAsA4PdfWithDiscountAndCourierDetails(): void
    {
        $data = [
            'title' => 'Invoice Ilena Furniture',
            'logoPath' => FCPATH . 'img' . DIRECTORY_SEPARATOR . 'LogoIlena.png',
            'transaksi' => [
                'id' => 'ORD-TEST-1', 'id_midtrans' => 'ORDER-TEST-1', 'nama' => 'Pelanggan Uji',
                'email' => 'buyer@example.test', 'nohp' => '081234567890', 'alamat' => 'Semarang',
                'resi' => 'RESI123', 'status' => 'Proses', 'kurir' => ['nama' => 'JNE', 'deskripsi' => 'JNE Trucking'],
                'data_mid' => ['transaction_time' => '2026-10-31 10:00:00', 'gross_amount' => 270000, 'payment_type' => 'bank_transfer', 'va_numbers' => [['bank' => 'bca', 'va_number' => '123456789']]],
                'items' => [
                    ['id' => 'SKU-1', 'name' => 'Lemari Cody', 'product_name' => 'Lemari Cody', 'collection' => 'Cody', 'variant' => 'Natural', 'dimensions' => ['panjang' => '800', 'lebar' => '500', 'tinggi' => '1800'], 'weight_kg' => '40', 'quantity' => 1, 'price' => 250000, 'original_price' => 300000],
                    ['id' => 'voucher', 'name' => 'voucher', 'quantity' => 1, 'price' => -50000],
                    ['id' => 'shipping', 'name' => 'biaya ongkir', 'quantity' => 1, 'price' => 70000],
                ],
            ],
        ];

        $html = view('pages/invoice_pdf', $data);
        $this->assertStringContainsString('size: A4 portrait', $html);
        $this->assertStringContainsString('Diskon voucher', $html);
        $this->assertStringContainsString('JNE Trucking', $html);

        $pdf = new Dompdf(['chroot' => FCPATH, 'isRemoteEnabled' => false]);
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $output = $pdf->output();

        $this->assertStringStartsWith('%PDF-', $output);
        $this->assertGreaterThan(1000, strlen($output));
        $this->assertStringContainsString('/MediaBox [0.000 0.000 595.280 841.890]', $output);
    }
}
