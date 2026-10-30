<?php

namespace App\Services;

use DateTimeImmutable;

/** Normalizes analytics filters before they reach database queries. */
final class AnalyticsOptions
{
    private const MAX_RANGE_DAYS = 366;
    private const MAX_IP_FILTERS = 50;

    public static function normalize(array $query, string $defaultEnd): array
    {
        $end = self::validDate($query['end'] ?? null) ?? (self::validDate($defaultEnd) ?? date('Y-m-d'));
        $start = self::validDate($query['start'] ?? null) ?? date('Y-m-01', strtotime($end));

        if ($start > $end) {
            $start = date('Y-m-01', strtotime($end));
        }

        $days = (int) ((strtotime($end) - strtotime($start)) / 86400) + 1;
        if ($days > self::MAX_RANGE_DAYS) {
            $start = date('Y-m-d', strtotime($end . ' -' . (self::MAX_RANGE_DAYS - 1) . ' days'));
        }

        $ips = [];
        $ipsInput = $query['exclude_ips'] ?? '';
        $rawIps = is_string($ipsInput) ? trim($ipsInput) : '';
        if ($rawIps !== '') {
            foreach (array_slice(explode(',', $rawIps), 0, self::MAX_IP_FILTERS) as $candidate) {
                $candidate = trim($candidate);
                if (filter_var($candidate, FILTER_VALIDATE_IP) !== false) {
                    $ips[] = $candidate;
                }
            }
        }

        return [
            'start' => $start,
            'end' => $end,
            'min_duration' => self::boundedInt($query['min_duration'] ?? 5, 0, 86400, 5),
            'exclude_low_avg_duration' => self::boundedInt($query['exclude_low_avg_duration'] ?? 0, 0, 86400, 0),
            'exclude_high_hits_per_day' => self::boundedInt($query['exclude_high_hits_per_day'] ?? 0, 0, 100000, 0),
            'exclude_ips' => array_values(array_unique($ips)),
        ];
    }

    private static function validDate($value): ?string
    {
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }

    private static function boundedInt($value, int $min, int $max, int $default): int
    {
        if (!is_scalar($value) || !is_numeric($value)) {
            return $default;
        }

        return max($min, min($max, (int) $value));
    }
}
