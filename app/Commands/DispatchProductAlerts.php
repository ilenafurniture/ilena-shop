<?php

namespace App\Commands;

use App\Models\BarangModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/** Run from cron/systemd: product alerts and shipped-order completion. */
class DispatchProductAlerts extends BaseCommand
{
    protected $group = 'Commerce';
    protected $name = 'commerce:alerts:dispatch';
    protected $description = 'Dispatch product alerts and complete eligible shipped orders.';

    public function run(array $params)
    {
        $db = db_connect();
        $sent = 0;
        if ($db->tableExists('product_interest_alerts')) {
            $alerts = $db->table('product_interest_alerts')->where('status', 'active')->where('notified_at', null)->orderBy('id', 'ASC')->limit(100)->get()->getResultArray();
            $catalog = new BarangModel();
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
        } else {
            CLI::write('Product alert table is not migrated; continuing with order completion.');
        }

        CLI::write('Product alerts sent: ' . $sent);
        CLI::write('Shipped orders completed: ' . $this->completeShippedOrders($db));
    }

    private function completeShippedOrders($db): int
    {
        $now = time();
        $candidates = $db->query("SELECT id FROM pemesanan WHERE status = 'Dikirim' AND JSON_VALID(data_mid) = 1 AND JSON_EXTRACT(data_mid, '$._app_shipped_at') IS NOT NULL ORDER BY id ASC LIMIT 25")->getResultArray();
        $completed = 0;
        $hasCaseTable = $db->tableExists('customer_order_service_cases');

        foreach ($candidates as $candidate) {
            $db->transBegin();
            $transactionOpen = true;
            try {
                $order = $db->query('SELECT * FROM pemesanan WHERE id = ? FOR UPDATE', [(int) $candidate['id']])->getRowArray();
                if (! $order || (string) ($order['status'] ?? '') !== 'Dikirim') {
                    $db->transRollback();
                    $transactionOpen = false;
                    continue;
                }

                $dataMid = json_decode((string) ($order['data_mid'] ?? '{}'), true);
                $dataMid = is_array($dataMid) ? $dataMid : [];
                $orderId = (string) ($order['id_midtrans'] ?? '');
                $hasOpenCase = false;
                if ($hasCaseTable && $orderId !== '') {
                    $hasOpenCase = $db->table('customer_order_service_cases')
                        ->where('order_id', $orderId)->whereIn('status', ['received', 'reviewing'])->countAllResults() > 0;
                }

                if (! \App\Services\OrderDelivery::isAutoCompletionDue('Dikirim', $dataMid['_app_shipped_at'] ?? null, $now, $hasOpenCase)) {
                    $db->transRollback();
                    $transactionOpen = false;
                    continue;
                }

                $completedAt = gmdate('Y-m-d H:i:s');
                $dataMid['_app_received_at'] = $completedAt;
                $dataMid['_app_received_confirmation_source'] = 'auto_after_6_days';
                $dataMid['_app_auto_completed_at'] = $completedAt;
                $updated = $db->table('pemesanan')->where('id', (int) $order['id'])->where('status', 'Dikirim')->update([
                    'status' => 'Selesai',
                    'data_mid' => json_encode($dataMid, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                ]);
                if (! $updated || ! $db->transStatus()) {
                    throw new \RuntimeException('Could not persist automatic order completion.');
                }
                $db->transCommit();
                $transactionOpen = false;

                $order['status'] = 'Selesai';
                $order['data_mid'] = json_encode($dataMid, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $this->notifyCompletedOrder($order, $dataMid);
                log_message('info', 'Order {order} auto-completed six days after shipment.', ['order' => $orderId]);
                $completed++;
            } catch (\Throwable $error) {
                if ($transactionOpen) $db->transRollback();
                log_message('error', 'Automatic completion failed for order row {id}: {error}', [
                    'id' => (int) $candidate['id'], 'error' => $error->getMessage(),
                ]);
            }
        }

        return $completed;
    }

    /** Notify the buyer and the connected inventory system after the atomic status transition. */
    private function notifyCompletedOrder(array $order, array $dataMid): void
    {
        $orderId = (string) ($order['id_midtrans'] ?? '');
        $emailAddress = (string) ($order['email'] ?? '');
        $token = (string) ($dataMid['_app_order_access_token'] ?? '');

        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            try {
                $email = service('email');
                $emailConfig = config('Email');
                $email->clear(true);
                $email->setFrom($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com', 'Ilena Furniture');
                $email->setReplyTo('info@ilenafurniture.com', 'Customer Care Ilena Furniture');
                $email->setTo($emailAddress);
                $email->setSubject('ILENA Store - Pesanan Diterima #' . $orderId);
                $detailUrl = 'https://ilenafurniture.com/orderdetail/selesai?idorder=' . rawurlencode($orderId)
                    . '&access_token=' . rawurlencode($token);
                $name = (string) ($order['nama'] ?? 'Customer');
                $email->setMessage('<div style="font-family:Arial,sans-serif;color:#222;line-height:1.6"><h2>Halo ' . esc($name) . ',</h2>'
                    . '<p>Pesanan Anda telah otomatis ditandai selesai karena sudah 6 hari sejak dikirim. Jika pesanan belum diterima atau ada kendala, silakan hubungi Customer Care Ilena.</p>'
                    . '<p><b>Kode Pesanan:</b> ' . esc($orderId) . '</p><p><a href="' . esc($detailUrl, 'attr') . '">Lihat detail pesanan</a></p>'
                    . '<p>Terima kasih sudah berbelanja di Ilena Furniture.</p></div>');
                $email->setAltMessage("Halo {$name}, pesanan {$orderId} telah otomatis ditandai selesai enam hari setelah dikirim. Jika belum diterima, hubungi Customer Care Ilena. Detail: {$detailUrl}");
                if (! $email->send(false)) {
                    log_message('error', 'Auto-completion email failed for order {order}.', ['order' => $orderId]);
                }
            } catch (\Throwable $error) {
                log_message('error', 'Auto-completion email error for order {order}: {error}', ['order' => $orderId, 'error' => $error->getMessage()]);
            }
        }

        if ((string) env('ILENA_SYSTEM_WEB_ORDER_ENABLED', 'false') !== 'true') return;
        $url = trim((string) env('ILENA_SYSTEM_WEB_ORDER_URL', ''));
        $webhookToken = trim((string) env('ILENA_SYSTEM_WEB_ORDER_TOKEN', ''));
        if ($url === '' || $webhookToken === '') return;

        $items = json_decode((string) ($order['items'] ?? '[]'), true);
        $courier = json_decode((string) ($order['kurir'] ?? '[]'), true);
        $paymentEnvironment = \App\Services\Payments\PaymentSafety::environment($order, [
            'galihsuks123@gmail.com', 'ilenafurniture@gmail.com', 'galih8.4.2001@gmail.com',
            'adityaanugrah494@gmail.com', 'tipaun0605@gmail.com', 'uuua5021@gmail.com',
        ], 'email');
        unset($dataMid['_app_order_access_token']);
        $payload = [
            'order_id' => $orderId, 'status' => 'Selesai', 'nama_pen' => (string) ($order['nama'] ?? ''),
            'email' => $emailAddress, 'hp_pen' => (string) ($order['nohp'] ?? ''), 'alamat_pen' => (string) ($order['alamat'] ?? ''),
            'items' => is_array($items) ? $items : [], 'kurir' => is_array($courier) ? $courier : [],
            'data_mid' => $dataMid, 'is_test' => $paymentEnvironment === 'sandbox',
            'payment_environment' => $paymentEnvironment, 'source' => 'ilenafurniture.com',
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json', 'X-Ilena-Webhook-Token: ' . $webhookToken],
            CURLOPT_TIMEOUT => 20,
        ]);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($error !== '' || $httpCode < 200 || $httpCode >= 300) {
            log_message('error', 'Auto-completed order sync failed for {order}: HTTP {http}; {error}; {response}', [
                'order' => $orderId, 'http' => $httpCode, 'error' => $error, 'response' => substr((string) $response, 0, 300),
            ]);
        }
    }
}
