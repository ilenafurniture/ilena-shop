<?php

namespace App\Services\Payments;

use CodeIgniter\Database\BaseConnection;

/** Serializes quota decisions per voucher so concurrent checkouts cannot oversubscribe it. */
final class VoucherReservationService
{
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function reserve(int $voucherId, string $orderId, string $email): array
    {
        $orderId = trim($orderId);
        $email = strtolower(trim($email));
        if ($voucherId < 1 || $orderId === '' || $email === '') {
            return ['ok' => false, 'message' => 'Data voucher checkout tidak valid.'];
        }

        $this->db->transBegin();
        try {
            if ($this->db->DBDriver === 'MySQLi') {
                $this->db->query('INSERT IGNORE INTO `ilena_voucher_order_locks` (`order_id`) VALUES (?)', [$orderId]);
                $this->db->query('SELECT `order_id` FROM `ilena_voucher_order_locks` WHERE `order_id` = ? FOR UPDATE', [$orderId]);
            }
            if ($this->db->DBDriver === 'MySQLi') {
                $this->db->query('INSERT IGNORE INTO `ilena_voucher_quota_locks` (`voucher_id`) VALUES (?)', [$voucherId]);
                $this->db->query('SELECT `voucher_id` FROM `ilena_voucher_quota_locks` WHERE `voucher_id` = ? FOR UPDATE', [$voucherId]);
            }
            $sql = 'SELECT * FROM `voucher` WHERE `id` = ?' . ($this->db->DBDriver === 'MySQLi' ? ' FOR UPDATE' : '');
            $voucher = $this->db->query($sql, [$voucherId])->getRowArray();
            if (! $voucher) {
                throw new \RuntimeException('Voucher tidak ditemukan saat reservasi.');
            }

            $table = $this->db->table('ilena_voucher_reservations');
            $existing = $table->where('order_id', $orderId)->where('voucher_id', $voucherId)->get()->getRowArray();
            if ($existing && $existing['status'] === 'reserved' && strtotime($existing['expires_at']) > time()) {
                $this->db->transCommit();
                return ['ok' => true, 'existing' => true];
            }
            if ($existing) {
                throw new \RuntimeException('Reservasi voucher untuk pesanan ini telah berakhir.');
            }

            $orderReservations = $this->db->table('ilena_voucher_reservations')->where('order_id', $orderId)->get()->getResultArray();
            $distinctVoucherIds = array_unique(array_map(static fn (array $reservation): int => (int) $reservation['voucher_id'], $orderReservations));
            if (! in_array($voucherId, $distinctVoucherIds, true) && count($distinctVoucherIds) >= self::MAX_VOUCHERS_PER_ORDER) {
                $this->db->transRollback();
                return ['ok' => false, 'message' => 'Maksimal dua voucher dapat digunakan dalam satu pesanan.'];
            }

            $code = (string) ($voucher['kode'] ?? '');
            $used = $this->db->table('voucher_usage')->where('kode_voucher', $code)->countAllResults();
            $now = date('Y-m-d H:i:s');
            $active = $this->db->table('ilena_voucher_reservations')
                ->where('voucher_id', $voucherId)->where('status', 'reserved')->where('expires_at >', $now)->countAllResults();
            $max = (int) ($voucher['max_pakai'] ?? 0);
            if ($max > 0 && $used + $active >= $max) {
                $this->db->transRollback();
                return ['ok' => false, 'message' => 'Voucher baru saja mencapai batas pemakaian.'];
            }

            $once = ! empty($voucher['sekali_pakai_per_user']);
            if ($once) {
                $alreadyUsed = $this->db->table('voucher_usage')
                    ->where('kode_voucher', $code)->where('email', $email)->countAllResults();
                $alreadyReserved = $this->db->table('ilena_voucher_reservations')
                    ->where('voucher_id', $voucherId)->where('email', $email)->where('status', 'reserved')
                    ->where('expires_at >', $now)->countAllResults();
                if ($alreadyUsed > 0 || $alreadyReserved > 0) {
                    $this->db->transRollback();
                    return ['ok' => false, 'message' => 'Voucher ini hanya dapat digunakan sekali per pengguna.'];
                }
            }

            $this->db->table('ilena_voucher_reservations')->insert([
                'order_id' => $orderId,
                'voucher_id' => $voucherId,
                'voucher_code' => $code,
                'email' => $email,
                'once_per_user' => $once ? 1 : 0,
                'status' => 'reserved',
                'expires_at' => date('Y-m-d H:i:s', time() + 90 * 60),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            if (! $this->db->transStatus()) {
                throw new \RuntimeException('Reservasi kuota voucher gagal disimpan.');
            }
            $this->db->transCommit();
            return ['ok' => true, 'existing' => false];
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    public const MAX_VOUCHERS_PER_ORDER = 2;

    public function consume(string $orderId): bool
    {
        $this->db->transBegin();
        try {
            $rows = $this->db->table('ilena_voucher_reservations')->where('order_id', trim($orderId))->where('status', 'reserved')->orderBy('voucher_id', 'ASC')->get()->getResultArray();
            if (! $rows) {
                $this->db->transCommit();
                return false;
            }
            if ($this->db->DBDriver === 'MySQLi') {
                $voucherIds = array_values(array_unique(array_map(static fn (array $row): int => (int) $row['voucher_id'], $rows)));
                if (count($voucherIds) > self::MAX_VOUCHERS_PER_ORDER) {
                    throw new \RuntimeException('Pesanan memiliki jumlah reservasi voucher di atas batas.');
                }
                foreach ($voucherIds as $voucherId) {
                    $this->db->query('SELECT `voucher_id` FROM `ilena_voucher_quota_locks` WHERE `voucher_id` = ? FOR UPDATE', [$voucherId]);
                }
                $rows = $this->db->query('SELECT * FROM `ilena_voucher_reservations` WHERE `order_id` = ? AND `status` = ? ORDER BY `voucher_id` ASC FOR UPDATE', [trim($orderId), 'reserved'])->getResultArray();
                if (! $rows) {
                    $this->db->transCommit();
                    return false;
                }
            }
            if (count($rows) > self::MAX_VOUCHERS_PER_ORDER) throw new \RuntimeException('Pesanan memiliki jumlah reservasi voucher di atas batas.');
            foreach ($rows as $row) {
                $this->db->table('voucher_usage')->insert([
                    'kode_voucher' => $row['voucher_code'],
                    'email' => $row['email'],
                    'used_at' => date('Y-m-d H:i:s'),
                ]);
                $this->db->table('ilena_voucher_reservations')->where('id', (int) $row['id'])->where('status', 'reserved')->update([
                    'status' => 'used', 'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if (! $this->db->transStatus()) {
                throw new \RuntimeException('Pemakaian voucher tidak berhasil dicatat.');
            }
            $this->db->transCommit();
            return true;
        } catch (\Throwable $error) {
            $this->db->transRollback();
            throw $error;
        }
    }

    public function release(string $orderId, ?int $voucherId = null): void
    {
        $builder = $this->db->table('ilena_voucher_reservations')->where('order_id', trim($orderId))->where('status', 'reserved');
        if ($voucherId !== null) $builder->where('voucher_id', $voucherId);
        $builder->update(['status' => 'released', 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
