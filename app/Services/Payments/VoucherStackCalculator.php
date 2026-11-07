<?php

namespace App\Services\Payments;

/** Applies at most one automatic voucher and one claimed voucher per order. */
final class VoucherStackCalculator
{
    public const MAX_VOUCHERS_PER_ORDER = 2;

    public function calculate(array $eligibleAutomatic, ?array $redeemed, bool $redeemedWasClaimed, float $subtotal): array
    {
        $subtotal = max(0, (int) floor($subtotal));
        $bestAutomatic = null;

        // A typed-in legacy/manual voucher must not stack. Only a redeemed claim
        // can be combined with one automatic promotion.
        foreach ($eligibleAutomatic as $candidate) {
            if (! is_array($candidate)) continue;
            if ($redeemed !== null && ! $redeemedWasClaimed) continue;
            if ($redeemed !== null && (int) ($candidate['id'] ?? 0) === (int) ($redeemed['id'] ?? -1)) continue;

            $candidateCut = $this->discount($candidate, $subtotal);
            if ($candidateCut <= 0) continue;
            if ($bestAutomatic === null || $candidateCut > $bestAutomatic['discount']) {
                $bestAutomatic = ['voucher' => $candidate, 'discount' => $candidateCut];
            }
        }

        $applied = [];
        $remaining = $subtotal;
        if ($bestAutomatic !== null) {
            $applied[] = $this->line($bestAutomatic['voucher'], $bestAutomatic['discount'], 'automatic');
            $remaining -= $bestAutomatic['discount'];
        }

        if ($redeemed !== null) {
            $redeemedCut = $this->discount($redeemed, $remaining);
            if ($redeemedCut > 0) {
                $applied[] = $this->line($redeemed, $redeemedCut, 'redeemed');
                $remaining -= $redeemedCut;
            }
        }

        // Defensive cap: never allow a caller to turn a larger claim list into
        // multiple simultaneous discounts.
        $applied = array_slice($applied, 0, self::MAX_VOUCHERS_PER_ORDER);
        return [
            'applied' => $applied,
            'discount' => max(0, $subtotal - max(0, $remaining)),
            'remaining' => max(0, $remaining),
        ];
    }

    private function discount(array $voucher, int $basis): int
    {
        $basis = max(0, $basis);
        if ($basis === 0) return 0;

        $type = strtolower((string) ($voucher['tipe'] ?? $voucher['satuan'] ?? 'persen'));
        $value = max(0, (float) ($voucher['nilai'] ?? $voucher['nominal'] ?? 0));
        $cut = $type === 'persen' ? (int) floor($basis * $value / 100) : (int) floor($value);
        return min($basis, max(0, $cut));
    }

    private function line(array $voucher, int $discount, string $source): array
    {
        return [
            'id' => (int) ($voucher['id'] ?? 0),
            'kode' => strtoupper(trim((string) ($voucher['kode'] ?? ''))),
            'nama' => (string) ($voucher['nama'] ?? ''),
            'target' => (string) ($voucher['target'] ?? 'semua'),
            'discount' => max(0, $discount),
            'source' => $source,
        ];
    }
}
