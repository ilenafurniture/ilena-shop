<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\PemesananModel;
use App\Services\Auth\CustomerEmailIdentity;

/** Customer self-service commerce extras. Customer-submitted content stays pending until staff moderation. */
class StoreEngagementController extends BaseController
{
    private function localePath(string $path): string
    {
        return ilena_store_path($path, in_array($this->request->getLocale(), ['id', 'en'], true) ? $this->request->getLocale() : 'id');
    }

    private function ownerEmails(): array
    {
        if (! session()->get('isLogin') || (string) session()->get('active') !== '1') return [];
        return (new CustomerEmailIdentity())->allFor((string) session()->get('email'));
    }

    private function bounce(string $message)
    {
        return redirect()->back()->with('commerce_notice', $message);
    }

    public function subscribeAlert()
    {
        $email = strtolower(trim((string) session()->get('email')));
        if (! session()->get('isLogin') || (string) session()->get('active') !== '1') return redirect()->to($this->localePath('/login'));
        $productId = trim((string) $this->request->getPost('product_id'));
        $type = (string) $this->request->getPost('alert_type');
        $variant = mb_substr(trim((string) $this->request->getPost('variant_name')), 0, 191);
        $db = db_connect();
        if (! $db->tableExists('product_interest_alerts')) return $this->bounce('Fitur notifikasi produk sedang disiapkan. Silakan hubungi Customer Care.');
        if ($email === '' || $email === 'tamu') return redirect()->to($this->localePath('/login'));
        if (! in_array($type, ['back_in_stock', 'price_drop'], true) || ! preg_match('/^[A-Za-z0-9._-]{1,64}$/', $productId)
            || ! (new BarangModel())->where('id', $productId)->where('active', '1')->first()
            || ! $this->request->getPost('consent')) {
            return $this->bounce('Periksa kembali pilihan notifikasi dan persetujuan Anda.');
        }
        $existing = $db->table('product_interest_alerts')->where('email', $email)->where('product_id', $productId)
            ->where('variant_name', $variant)->where('alert_type', $type)->get()->getRowArray();
        $product = (new BarangModel())->getBarang($productId);
        $variants = json_decode((string) ($product['varian'] ?? '[]'), true) ?: [];
        $requestedVariant = null;
        foreach ($variants as $candidate) {
            if (mb_strtolower(trim((string) ($candidate['nama'] ?? ''))) === mb_strtolower($variant)) { $requestedVariant = $candidate; break; }
        }
        if ($type === 'back_in_stock' && (!$requestedVariant || (int) ($requestedVariant['stok'] ?? 0) > 0)) return $this->bounce('Varian ini masih tersedia; notifikasi stok belum diperlukan.');
        if (! $existing) {
            $db->table('product_interest_alerts')->insert([
                'email' => $email, 'product_id' => $productId, 'variant_name' => $variant,
                'alert_type' => $type, 'baseline_price' => (int) ($product['harga_akhir'] ?? $product['harga'] ?? 0),
                'status' => 'active', 'consented_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } elseif ($existing['status'] === 'sent' || $existing['status'] === 'cancelled') {
            $db->table('product_interest_alerts')->where('id', (int) $existing['id'])->update([
                'baseline_price' => (int) ($product['harga_akhir'] ?? $product['harga'] ?? 0),
                'status' => 'active', 'notified_at' => null, 'consented_at' => date('Y-m-d H:i:s'),
            ]);
        }
        return $this->bounce($this->request->getLocale() === 'en' ? 'Alert saved. We will email you when the selected condition is met.' : 'Pemberitahuan tersimpan. Kami akan mengirim email saat kondisi pilihan terpenuhi.');
    }

    public function submitFeedback()
    {
        $db = db_connect();
        $id = trim((string) $this->request->getPost('product_id'));
        $type = (string) $this->request->getPost('feedback_type');
        $body = trim((string) $this->request->getPost('body'));
        $email = strtolower(trim((string) (session()->get('email') ?: $this->request->getPost('email'))));
        $name = trim((string) (session()->get('nama') ?: $this->request->getPost('name')));
        $rating = filter_var($this->request->getPost('rating'), FILTER_VALIDATE_INT);
        if (! $db->tableExists('product_feedback')) return $this->bounce('Fitur ulasan sedang disiapkan.');
        if (! (new \App\Services\Auth\AuthAttemptLimiter())->allow('product_feedback', (string) $this->request->getIPAddress(), $id, 8, 5, 900)) return $this->bounce('Terlalu banyak kiriman. Silakan coba kembali nanti.');
        if (! in_array($type, ['review', 'question'], true) || ! (new BarangModel())->where('id', $id)->where('active', '1')->first()
            || ! filter_var($email, FILTER_VALIDATE_EMAIL) || $name === '' || mb_strlen($body) < 8 || mb_strlen($body) > 2000
            || ! $this->request->getPost('publication_consent')
            || ($type === 'review' && ($rating === false || $rating < 1 || $rating > 5))) {
            return $this->bounce('Mohon lengkapi data dengan benar. Pesan harus 8–2.000 karakter.');
        }
        $verified = false;
        $orderId = trim((string) $this->request->getPost('order_id'));
        if ($type === 'review' && $orderId !== '' && session()->get('isLogin') && (string) session()->get('active') === '1') {
            $verifiedEmails = $this->ownerEmails();
            $order = (new PemesananModel())->where('id_midtrans', $orderId)->whereIn('email', $verifiedEmails)->first();
            if ($order && ($order['status'] ?? '') === 'Selesai') {
                $items = json_decode((string) ($order['items'] ?? '[]'), true) ?: [];
                foreach ($items as $item) if (is_array($item) && (string) ($item['id'] ?? '') === $id) $verified = true;
            }
        }
        $db->table('product_feedback')->insert([
            'product_id' => $id, 'author_email' => $email, 'author_name' => mb_substr($name, 0, 120),
            'order_id' => $verified ? $orderId : null, 'feedback_type' => $type,
            'rating' => $type === 'review' ? $rating : null,
            'title' => mb_substr(trim((string) $this->request->getPost('title')), 0, 160) ?: null,
            'body' => $body, 'verified_purchase' => $verified ? 1 : 0,
            'status' => 'pending', 'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->bounce($this->request->getLocale() === 'en'
            ? 'Thank you. Your submission is queued for review before it appears publicly.'
            : 'Terima kasih. Kiriman Anda menunggu pemeriksaan sebelum ditampilkan.');
    }

    public function createOrderCase(string $type)
    {
        $db = db_connect();
        $owners = $this->ownerEmails();
        if ($owners === []) return redirect()->to($this->localePath('/login'));
        if (! $db->tableExists('customer_order_service_cases') || ! in_array($type, ['cancellation', 'return', 'refund'], true)) return $this->bounce('Layanan pesanan sedang disiapkan.');
        $orderId = trim((string) $this->request->getPost('order_id'));
        $reason = trim((string) $this->request->getPost('reason'));
        $message = trim((string) $this->request->getPost('message'));
        $order = (new PemesananModel())->where('id_midtrans', $orderId)->whereIn('email', $owners)->first();
        if (! $order || mb_strlen($reason) < 3 || mb_strlen($reason) > 80 || mb_strlen($message) > 2000) return $this->bounce('Pesanan atau informasi permintaan tidak valid.');
        $orderStatus = (string) ($order['status'] ?? '');
        $allowedStatuses = match ($type) {
            'cancellation' => ['Proses', 'Dikirim'],
            'return' => ['Dikirim', 'Selesai'],
            'refund' => ['Proses', 'Dikirim', 'Selesai', 'Partial Refund'],
        };
        if (! in_array($orderStatus, $allowedStatuses, true)) return $this->bounce('Status pesanan belum memenuhi alur permintaan tersebut. Periksa detail pesanan atau hubungi Customer Care.');
        $duplicate = $db->table('customer_order_service_cases')->where('order_id', $orderId)->where('customer_email', (string) session()->get('email'))
            ->whereIn('status', ['received', 'reviewing'])->countAllResults();
        if ($duplicate > 0) return $this->bounce('Permintaan untuk pesanan ini sudah tercatat dan sedang ditinjau.');
        $db->table('customer_order_service_cases')->insert([
            'order_id' => $orderId, 'customer_email' => (string) session()->get('email'), 'case_type' => $type,
            'reason' => $reason, 'message' => $message, 'status' => 'received', 'created_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->bounce('Permintaan diterima. Tim Customer Care akan meninjau dan memperbarui statusnya di detail pesanan.');
    }
}
