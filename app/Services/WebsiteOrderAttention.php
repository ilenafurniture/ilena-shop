<?php

namespace App\Services;

/** Shared counts for website orders that still need an admin action. */
final class WebsiteOrderAttention
{
    public static function missingShipmentTimestampSql(): string
    {
        $path = "JSON_EXTRACT(data_mid, '$._app_shipped_at')";
        return "CASE WHEN JSON_VALID(data_mid) = 1 THEN ({$path} IS NULL OR JSON_TYPE({$path}) = 'NULL' OR JSON_UNQUOTE({$path}) NOT REGEXP '^[0-9]+([.][0-9]+)?$' OR CAST(JSON_UNQUOTE({$path}) AS DECIMAL(20,0)) < 1) ELSE 1 END = 1";
    }

    /** @param list<string> $excludedEmails @return array{manual_payment_confirmation:int, processing:int, legacy_shipment_date:int, total:int} */
    public static function counts($db, array $excludedEmails): array
    {
        $missingTimestamp = self::missingShipmentTimestampSql();
        $row = $db->table('pemesanan')
            ->select("SUM(CASE WHEN status = 'Menunggu Pembayaran' AND JSON_VALID(data_mid) = 1 AND JSON_UNQUOTE(JSON_EXTRACT(data_mid, '$.payment_type')) = 'admin' THEN 1 ELSE 0 END) AS manual_payment_confirmation", false)
            ->select("SUM(CASE WHEN status = 'Proses' THEN 1 ELSE 0 END) AS processing", false)
            ->select("SUM(CASE WHEN status = 'Dikirim' AND ({$missingTimestamp}) THEN 1 ELSE 0 END) AS legacy_shipment_date", false)
            ->whereNotIn('email', $excludedEmails)
            ->get()->getRowArray() ?? [];

        $manualPaymentConfirmation = (int) ($row['manual_payment_confirmation'] ?? 0);
        $processing = (int) ($row['processing'] ?? 0);
        $legacyShipmentDate = (int) ($row['legacy_shipment_date'] ?? 0);

        return [
            'manual_payment_confirmation' => $manualPaymentConfirmation,
            'processing' => $processing,
            'legacy_shipment_date' => $legacyShipmentDate,
            'total' => $manualPaymentConfirmation + $processing + $legacyShipmentDate,
        ];
    }
}
