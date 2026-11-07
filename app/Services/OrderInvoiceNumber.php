<?php

namespace App\Services;

/** Stable customer-facing invoice number derived from the immutable order row ID. */
final class OrderInvoiceNumber
{
    public static function fromPrimaryKey(mixed $id): string
    {
        if (! is_numeric($id) || (int) $id < 1) {
            return '—';
        }

        return 'IL' . str_pad((string) (int) $id, 8, '0', STR_PAD_LEFT);
    }
}
