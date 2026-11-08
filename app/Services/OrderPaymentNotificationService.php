<?php

namespace App\Services;

use Config\Services;

/** Sends payment-state email notifications from the commerce worker. */
final class OrderPaymentNotificationService
{
    public function send(array $order, string $status, bool $includeAdmin): bool
    {
        $orderId = (string) ($order['id_midtrans'] ?? '');
        $customer = (string) ($order['email'] ?? '');
        if ($orderId === '') return true;

        $mid = json_decode((string) ($order['data_mid'] ?? '{}'), true) ?: [];
        $token = (string) ($mid['_app_order_access_token'] ?? '');
        if (preg_match('/^[a-f0-9]{64}$/i', $token) !== 1) $token = '';
        $name = (string) ($order['nama'] ?? 'Customer');
        $messages = [
            'Proses' => 'Pembayaran kamu sudah berhasil kami terima. Pesanan akan segera kami proses.',
            'Menunggu Pembayaran' => 'Pesanan kamu sudah dibuat dan sedang menunggu pembayaran.',
            'Selesai' => 'Pesanan kamu telah dikonfirmasi diterima. Terima kasih sudah berbelanja di Ilena Furniture.',
            'Kadaluarsa' => 'Pembayaran pesanan kamu berstatus kedaluwarsa. Silakan cek halaman pesanan untuk detailnya.',
            'Ditolak' => 'Pembayaran pesanan kamu ditolak. Silakan cek halaman pesanan untuk detailnya.',
            'Gagal' => 'Pembayaran pesanan kamu gagal. Silakan cek halaman pesanan untuk detailnya.',
            'Dibatalkan' => 'Pesanan kamu telah dibatalkan. Silakan cek halaman pesanan untuk detailnya.',
        ];
        $message = $messages[$status] ?? ('Status pembayaran pesanan kamu telah berubah menjadi ' . $status . '.');
        $statusSlug = rawurlencode(strtolower($status));
        $url = 'https://ilenafurniture.com/orderdetail/' . $statusSlug . '?idorder=' . rawurlencode($orderId) . '&access_token=' . rawurlencode($token);
        $emailConfig = config('Email');
        $fromEmail = $emailConfig->fromEmail ?: ($emailConfig->SMTPUser ?: 'no-reply@ilenafurniture.com');
        $mail = Services::email();
        if (filter_var($customer, FILTER_VALIDATE_EMAIL)) {
            $mail->clear(true);
            $mail->setFrom($fromEmail, $emailConfig->fromName ?: 'Ilena Furniture');
            $mail->setReplyTo('info@ilenafurniture.com', 'Customer Care Ilena Furniture');
            $mail->setTo($customer);
            $mail->setSubject('ILENA Store - ' . ($status === 'Proses' ? 'Pembayaran Berhasil' : 'Status Pesanan ' . $status) . ' #' . $orderId);
            $mail->setMessage('<div style="font-family:Arial,sans-serif;color:#222;line-height:1.6"><h2>Halo ' . esc($name) . ',</h2><p>' . esc($message) . '</p><p><b>Kode Pesanan:</b> ' . esc($orderId) . '</p><p><b>Status:</b> ' . esc($status) . '</p><p><a href="' . esc($url, 'attr') . '">Lihat detail pesanan</a></p><p>Terima kasih sudah berbelanja di Ilena Furniture.</p></div>');
            $mail->setAltMessage("Halo {$name}, {$message}\nKode Pesanan: {$orderId}\nStatus: {$status}\n{$url}");
            if (!$mail->send(false)) {
                log_message('error', 'Commerce worker customer email failed for {order}: {debug}', ['order' => $orderId, 'debug' => $mail->printDebugger(['headers'])]);
                return false;
            }
        }

        if ($includeAdmin) {
            $admin = (string) env('ORDER_ADMIN_EMAIL', 'info@ilenafurniture.com');
            if (filter_var($admin, FILTER_VALIDATE_EMAIL)) {
                $total = number_format((float) ($mid['gross_amount'] ?? 0), 0, ',', '.');
                $mail->clear(true);
                $mail->setFrom($fromEmail, $emailConfig->fromName ?: 'Ilena Furniture');
                $mail->setReplyTo('info@ilenafurniture.com', 'Customer Care Ilena Furniture');
                $mail->setTo($admin);
                $mail->setSubject('Admin Ilena - Update Pembayaran #' . $orderId);
                $mail->setMessage('<div style="font-family:Arial,sans-serif;color:#222;line-height:1.6"><h2>Update pembayaran masuk</h2><p>Midtrans mengirim perubahan status pembayaran untuk pesanan berikut:</p><p><b>Kode Pesanan:</b> ' . esc($orderId) . '</p><p><b>Status:</b> ' . esc($status) . '</p><p><b>Customer:</b> ' . esc($name) . '</p><p><b>Email Customer:</b> ' . esc($customer) . '</p><p><b>Total:</b> Rp ' . esc($total) . '</p><p><a href="https://ilenafurniture.com/admin/order/online">Buka admin order</a></p></div>');
                $mail->setAltMessage("Update pembayaran masuk\nKode Pesanan: {$orderId}\nStatus: {$status}\nCustomer: {$name}\nEmail: {$customer}\nTotal: Rp {$total}");
                if (!$mail->send(false)) {
                    log_message('error', 'Commerce worker admin email failed for {order}: {debug}', ['order' => $orderId, 'debug' => $mail->printDebugger(['headers'])]);
                    return false;
                }
            }
        }
        return true;
    }
}
