<?php

namespace App\Commands;

use App\Models\BarangModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/** Run from cron/systemd: php spark commerce:alerts:dispatch. */
class DispatchProductAlerts extends BaseCommand
{
    protected $group = 'Commerce';
    protected $name = 'commerce:alerts:dispatch';
    protected $description = 'Send opted-in one-time back-in-stock and price-drop emails.';

    public function run(array $params)
    {
        $db = db_connect();
        if (! $db->tableExists('product_interest_alerts')) { CLI::write('Commerce alert table is not migrated.'); return; }
        $alerts = $db->table('product_interest_alerts')->where('status', 'active')->where('notified_at', null)->orderBy('id', 'ASC')->limit(100)->get()->getResultArray();
        $catalog = new BarangModel();
        $sent = 0;
        foreach ($alerts as $alert) {
            $product = $catalog->getBarang((string) $alert['product_id']);
            if (! $product) continue;
            $triggered = false;
            if ($alert['alert_type'] === 'back_in_stock') {
                $variants = json_decode((string) ($product['varian'] ?? '[]'), true) ?: [];
                foreach ($variants as $variant) {
                    if (mb_strtolower(trim((string) ($variant['nama'] ?? ''))) === mb_strtolower((string) $alert['variant_name']) && (int) ($variant['stok'] ?? 0) > 0) { $triggered = true; break; }
                }
            } elseif ($alert['alert_type'] === 'price_drop') {
                $triggered = (int) ($product['harga_akhir'] ?? $product['harga'] ?? 0) > 0
                    && (int) ($product['harga_akhir'] ?? $product['harga']) < (int) ($alert['baseline_price'] ?? 0);
            }
            if (! $triggered || ! filter_var($alert['email'], FILTER_VALIDATE_EMAIL)) continue;
            $email = service('email');
            $title = (string) ($product['nama'] ?? 'produk Ilena');
            $email->clear(true);
            $email->setTo((string) $alert['email']);
            $email->setSubject($alert['alert_type'] === 'back_in_stock' ? 'Produk Ilena kembali tersedia' : 'Harga produk Ilena turun');
            $url = site_url('product/' . strtolower(str_replace(' ', '-', $title)));
            $message = '<div style="font:16px/1.6 Arial,sans-serif;color:#252321;max-width:560px;margin:auto">'
                . '<p>Halo,</p><p>Produk <strong>' . esc($title) . '</strong>'
                . ($alert['alert_type'] === 'back_in_stock' ? ' varian <strong>' . esc((string) $alert['variant_name']) . '</strong> kini tersedia kembali.' : ' kini memiliki harga lebih rendah dari saat Anda berlangganan pemberitahuan.')
                . '</p><p><a href="' . esc($url, 'attr') . '" style="display:inline-block;padding:12px 18px;background:#8d2e2b;color:#fff;text-decoration:none">Lihat produk</a></p>'
                . '<p style="color:#777;font-size:13px">Email ini dikirim karena Anda meminta pemberitahuan satu kali di Ilena Furniture.</p></div>';
            $email->setMessage($message);
            $email->setAltMessage('Produk ' . $title . ' memenuhi kondisi pemberitahuan yang Anda pilih. Lihat: ' . $url);
            if (! $email->send()) { log_message('error', 'Product alert email failed for subscription {id}.', ['id' => $alert['id']]); continue; }
            $db->table('product_interest_alerts')->where('id', (int) $alert['id'])->where('notified_at', null)->update(['notified_at' => date('Y-m-d H:i:s'), 'status' => 'sent']);
            $sent++;
        }
        CLI::write('Product alerts sent: ' . $sent);
    }
}
