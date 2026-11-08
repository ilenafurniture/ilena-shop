<?php

namespace App\Services;

use App\Services\Payments\StockReservationService;
use App\Services\Payments\VoucherReservationService;
use Config\Database;
use RuntimeException;

/** Performs one persisted Midtrans charge. Ambiguous non-idempotent outcomes are parked. */
final class MidtransCheckoutJobHandler
{
    private $db;

    public function __construct($db = null) { $this->db = $db ?? Database::connect(); }

    /** @return array{done:bool,retrySafe:bool,message:string} */
    public function handle(array $job): array
    {
        $payload = json_decode((string) ($job['payload'] ?? ''), true);
        if (!is_array($payload) || !is_array($payload['charge'] ?? null)) {
            return ['done' => false, 'retrySafe' => false, 'message' => 'Invalid persisted charge payload.'];
        }
        $orderId = (string) ($job['order_id'] ?? '');
        $order = $this->db->table('pemesanan')->where('id_midtrans', $orderId)->get()->getRowArray();
        if (!$order) return ['done' => false, 'retrySafe' => false, 'message' => 'Order row is missing.'];
        if (($order['status'] ?? '') !== 'Menunggu Pembayaran') {
            return ['done' => true, 'retrySafe' => false, 'message' => 'Order has already left the pending-payment state.'];
        }
        $mid = json_decode((string) ($order['data_mid'] ?? '{}'), true) ?: [];
        if (!empty($mid['transaction_id']) || !empty($mid['va_numbers']) || !empty($mid['qr_string']) || !empty($mid['actions'])) {
            // An earlier attempt already returned provider payment instructions.
            return ['done' => true, 'retrySafe' => false, 'message' => 'Provider response already recorded.'];
        }

        $environment = ($payload['environment'] ?? '') === 'sandbox' ? 'sandbox' : 'production';
        $key = trim((string) env($environment === 'sandbox' ? 'MIDTRANS_SANDBOX_KEY' : 'MIDTRANS_PRODUCTION_KEY', ''));
        if ($key === '' || $key === 'DefaultValue') return ['done' => false, 'retrySafe' => false, 'message' => 'Midtrans server key is not configured.'];

        // Fence cancellation and duplicate workers before the external side effect.
        $this->db->transBegin();
        try {
            $locked = \App\Services\Payments\PaymentSafety::lockedOrder($this->db, $orderId);
            if (!$locked || ($locked['status'] ?? '') !== 'Menunggu Pembayaran') {
                $this->db->transRollback();
                return ['done' => true, 'retrySafe' => false, 'message' => 'Order is no longer awaiting payment.'];
            }
            $lockedMid = json_decode((string) ($locked['data_mid'] ?? '{}'), true) ?: [];
            if (!empty($lockedMid['transaction_id']) || !empty($lockedMid['va_numbers']) || !empty($lockedMid['qr_string']) || !empty($lockedMid['actions'])) {
                $this->db->transRollback();
                return ['done' => true, 'retrySafe' => false, 'message' => 'Provider instructions were already recorded.'];
            }
            $lockedMid['_app_charge_in_flight'] = true;
            $lockedMid['_app_charge_started_at'] = gmdate('Y-m-d H:i:s');
            $this->db->table('pemesanan')->where('id_midtrans', $orderId)->update(['data_mid' => json_encode($lockedMid)]);
            if (!$this->db->transStatus()) throw new RuntimeException('Could not fence checkout charge.');
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return ['done' => false, 'retrySafe' => false, 'message' => 'Could not safely start charge: ' . $e->getMessage()];
        }

        $charge = $payload['charge'];
        $headers = ['Accept: application/json', 'Content-Type: application/json', 'Authorization: Basic ' . base64_encode($key . ':')];
        $supportsIdempotency = !empty($payload['idempotency_key']);
        if ($supportsIdempotency) $headers[] = 'Idempotency-Key: ' . (string) $payload['idempotency_key'];
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $environment === 'sandbox' ? 'https://api.sandbox.midtrans.com/v2/charge' : 'https://api.midtrans.com/v2/charge',
            CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($charge, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            CURLOPT_CONNECTTIMEOUT => 5, CURLOPT_TIMEOUT => 20, CURLOPT_HTTPHEADER => $headers,
        ]);
        $raw = curl_exec($curl);
        $error = curl_error($curl);
        $http = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error !== '' || $http === 0 || $http >= 500 || $http === 409) {
            $reason = $error !== '' ? $error : 'Midtrans returned HTTP ' . $http;
            log_message('error', 'Queued Midtrans request uncertain for {order}; HTTP {http}; {error}', ['order' => $orderId, 'http' => $http, 'error' => $reason]);
            return ['done' => false, 'retrySafe' => $supportsIdempotency, 'message' => $reason];
        }
        $response = json_decode((string) $raw, true);
        if (!is_array($response)) return ['done' => false, 'retrySafe' => $supportsIdempotency, 'message' => 'Midtrans response was not valid JSON.'];

        if ($http < 200 || $http >= 300 || substr((string) ($response['status_code'] ?? ''), 0, 1) !== '2') {
            $this->failOrder($orderId, $response);
            return ['done' => true, 'retrySafe' => false, 'message' => 'Provider rejected the charge.'];
        }

        $this->db->transBegin();
        try {
            $current = $this->db->table('pemesanan')->where('id_midtrans', $orderId)->get()->getRowArray();
            if (!$current) throw new RuntimeException('Order disappeared while saving provider response.');
            $stored = json_decode((string) ($current['data_mid'] ?? '{}'), true) ?: [];
            foreach ($response as $field => $value) if (!str_starts_with((string) $field, '_app_')) $stored[$field] = $value;
            $stored['_app_payment_environment'] = $environment;
            unset($stored['_app_charge_in_flight'], $stored['_app_charge_started_at']);
            $stored['_app_checkout_queue_status'] = 'payment_ready';
            $stored['_app_charge_response_saved_at'] = gmdate('Y-m-d H:i:s');
            $this->db->table('pemesanan')->where('id_midtrans', $orderId)->update(['data_mid' => json_encode($stored, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
            if (!$this->db->transStatus()) throw new RuntimeException('Could not persist Midtrans response.');
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            // Provider may have accepted the charge: never blindly repeat on local persistence failure.
            return ['done' => false, 'retrySafe' => false, 'message' => 'Provider accepted request but local response save failed: ' . $e->getMessage()];
        }
        return ['done' => true, 'retrySafe' => false, 'message' => 'Payment instructions saved.'];
    }

    private function failOrder(string $orderId, array $providerResponse): void
    {
        $this->db->transBegin();
        try {
            $this->db->table('pemesanan')->where('id_midtrans', $orderId)->where('status', 'Menunggu Pembayaran')
                ->update(['status' => 'Gagal']);
            $mid = json_decode((string) (($this->db->table('pemesanan')->where('id_midtrans', $orderId)->get()->getRowArray()['data_mid'] ?? '{}')), true) ?: [];
            unset($mid['_app_charge_in_flight'], $mid['_app_charge_started_at']);
            $mid['_app_checkout_queue_status'] = 'rejected';
            $mid['_app_charge_error'] = mb_substr((string) ($providerResponse['status_message'] ?? 'Payment provider rejected the request.'), 0, 250);
            $this->db->table('pemesanan')->where('id_midtrans', $orderId)->update(['data_mid' => json_encode($mid, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
            (new StockReservationService($this->db))->release($orderId);
            (new VoucherReservationService($this->db))->release($orderId);
            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
