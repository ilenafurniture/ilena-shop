<?php
/**
 * HelperTrait - Utility helper methods for AdminController
 * 
 * Methods:
 * - generateSjNumberGlobal(): Generate SJ number for sale type
 * - generateSjNumberGlobalNF(): Generate SJ number for non-faktur
 * - generateSjCodeSimple(): Generate simple SJ code for interior
 * - groupOrderItems(): Group order items by product/variant
 * - shippedQtyMap(): Map shipped quantities
 * - shippedQtyMapExceptSj(): Map shipped quantities excluding specific SJ
 * - generateNextOfflineCode(): Generate next offline order code
 * - interiorShippedQtyMap(): Map shipped quantities for interior
 * - displayNoNf(): Format NF display number
 * - ppnRateFromMode(): Get PPN rate from mode string
 */

namespace App\Controllers\Admin\Traits;

trait HelperTrait
{
    /**
     * Generate SJ number global format: 00001/SJ/CBM/mm/yyyy
     */
    protected function generateSjNumberGlobal(string $tanggalDb): string
    {
        $ts   = strtotime($tanggalDb ?: 'now');
        $mm   = (int)date('m', $ts);
        $yyyy = date('Y', $ts);

        $last = $this->suratJalanModel
            ->select('no_sj')
            ->where('no_sj IS NOT NULL', null, false)
            ->like('no_sj', '/SJ/CBM/' . $mm . '/' . $yyyy, 'both')
            ->orderBy('id', 'DESC')
            ->first();

        $next = 1;
        if ($last && !empty($last['no_sj'])) {
            $parts = explode('/', $last['no_sj']);
            $num   = (int)($parts[0] ?? 0);
            $next  = $num + 1;
        }

        $urut = str_pad((string)$next, 5, '0', STR_PAD_LEFT);
        return $urut . '/SJ/CBM/' . $mm . '/' . $yyyy;
    }

    /**
     * Generate SJ number for NF format: NF0001/SJ/CBM/mm/yyyy
     */
    protected function generateSjNumberGlobalNF(string $tanggalDb): string
    {
        $ts   = strtotime($tanggalDb ?: 'now');
        $mm   = (int)date('m', $ts);
        $yyyy = date('Y', $ts);

        $last = $this->suratJalanModel
            ->select('no_sj')
            ->where('no_sj IS NOT NULL', null, false)
            ->like('no_sj', 'NF%/SJ/CBM/' . $mm . '/' . $yyyy, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $next = 1;
        if ($last && !empty($last['no_sj'])) {
            if (preg_match('/NF(\d+)\//', $last['no_sj'], $mmatch)) {
                $next = ((int)$mmatch[1]) + 1;
            }
        }

        $urut = str_pad((string)$next, 4, '0', STR_PAD_LEFT);
        return 'NF' . $urut . '/SJ/CBM/' . $mm . '/' . $yyyy;
    }

    /**
     * Generate SP number format for Surat Pengantar: 0001/SP/mm/yyyy
     * Format: 4-digit sequence, 2-digit zero-padded month
     */
    protected function generateSpNumberGlobal(string $tanggalDb): string
    {
        $ts   = strtotime($tanggalDb ?: 'now');
        $mm   = str_pad((string)date('n', $ts), 2, '0', STR_PAD_LEFT);
        $yyyy = date('Y', $ts);

        $last = $this->suratJalanModel
            ->select('no_sj')
            ->where('no_sj IS NOT NULL', null, false)
            ->like('no_sj', '/SP/' . $mm . '/' . $yyyy, 'both')
            ->orderBy('id', 'DESC')
            ->first();

        $next = 1;
        if ($last && !empty($last['no_sj'])) {
            $parts = explode('/', $last['no_sj']);
            $num   = (int)($parts[0] ?? 0);
            $next  = $num + 1;
        }

        $urut = str_pad((string)$next, 4, '0', STR_PAD_LEFT);
        return $urut . '/SP/' . $mm . '/' . $yyyy;
    }

    /**
     * Generate Invoice number for Interior: 00001/INV/CBM/mm/yyyy
     * Global sequential per month
     */
    protected function generateInvoiceNumberGlobal(string $tanggalDb = null): string
    {
        $ts   = strtotime($tanggalDb ?: 'now');
        $mm   = str_pad((string)date('n', $ts), 2, '0', STR_PAD_LEFT);
        $yyyy = date('Y', $ts);

        $db = \Config\Database::connect();
        
        // Look for existing invoices this month
        $pattern = '%/INV/CBM/' . $mm . '/' . $yyyy;
        $last = $db->table('interior_invoice')
            ->select('no_invoice')
            ->like('no_invoice', $pattern, 'before')
            ->orderBy('id', 'DESC')
            ->get(1)->getRowArray();

        $next = 1;
        if ($last && !empty($last['no_invoice'])) {
            $parts = explode('/', $last['no_invoice']);
            $num   = (int)($parts[0] ?? 0);
            $next  = $num + 1;
        }

        $urut = str_pad((string)$next, 5, '0', STR_PAD_LEFT);
        return $urut . '/INV/CBM/' . $mm . '/' . $yyyy;
    }

    /**
     * Generate simple SJ code for interior: SJ000001
     */
    protected function generateSjCodeSimple(): string
    {
        $db = \Config\Database::connect();
        $row = $db->table('surat_jalan')
            ->select('no_sj')
            ->like('no_sj', 'SJ', 'after')
            ->orderBy('id', 'DESC')
            ->get(1)->getRowArray();

        $last = (string)($row['no_sj'] ?? '');
        $n = 0;
        if (preg_match('/^SJ(\d{1,})$/', $last, $m)) {
            $n = (int)$m[1];
        } else {
            if (preg_match('/SJ(\d+)/', $last, $m2)) $n = (int)$m2[1];
        }
        $next = $n + 1;
        return 'SJ' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Group order items by product and variant
     */
    protected function groupOrderItems(string $idPesanan): array
    {
        $rows = $this->pemesananOfflineItemModel
            ->select('id_barang, varian, COUNT(*) as qty')
            ->where('id_pesanan', $idPesanan)
            ->groupBy('id_barang, varian')
            ->findAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_barang' => (int)$r['id_barang'],
                'varian'    => (string)$r['varian'],
                'qty'       => (int)$r['qty'],
            ];
        }
        return $out;
    }

    /**
     * Map shipped quantities for order
     */
    protected function shippedQtyMap(string $idPesanan): array
    {
        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.id_barang, surat_jalan_item.varian, SUM(surat_jalan_item.qty) as shipped_qty, surat_jalan_item.kode_barang')
            ->join('surat_jalan', 'surat_jalan.id = surat_jalan_item.surat_jalan_id')
            ->where('surat_jalan.id_pesanan', $idPesanan)
            ->groupBy('surat_jalan_item.id_barang, surat_jalan_item.varian, surat_jalan_item.kode_barang')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $isInterior = empty($r['id_barang']);
            $key = $isInterior ? '__INTERIOR__' : ((int)$r['id_barang'] . '||' . (string)$r['varian']);
            $map[$key] = (int)($r['shipped_qty'] ?? 0);
        }
        return $map;
    }

    /**
     * Map shipped quantities excluding specific SJ
     */
    protected function shippedQtyMapExceptSj(string $idPesanan, int $excludeSjId): array
    {
        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.id_barang, surat_jalan_item.varian, surat_jalan_item.qty, surat_jalan_item.kode_barang')
            ->join('surat_jalan sj', 'sj.id = surat_jalan_item.surat_jalan_id')
            ->where('sj.id_pesanan', $idPesanan)
            ->where('sj.id !=', $excludeSjId)
            ->where('sj.status', 'printed')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $isInterior = empty($r['id_barang']);

            if ($isInterior) {
                $kode = trim((string)($r['kode_barang'] ?? ''));
                if ($kode === '') continue;
                $key = 'I||' . $kode;
            } else {
                $key = ((int)$r['id_barang']).'||'.((string)$r['varian']);
            }

            $map[$key] = (int)($map[$key] ?? 0) + (int)($r['qty'] ?? 0);
        }
        return $map;
    }

    /**
     * Generate next offline order code
     */
    protected function generateNextOfflineCode(string $prefix): string
    {
        $prefix = strtoupper(trim($prefix));
        if ($prefix === '') return '';

        $last = $this->pemesananOfflineModel
            ->like('id_pesanan', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $nextNum = 1;
        if ($last && !empty($last['id_pesanan'])) {
            $digits = preg_replace('/\D+/', '', substr($last['id_pesanan'], strlen($prefix)));
            if ($digits !== '') $nextNum = ((int)$digits) + 1;
        }

        return $prefix . sprintf('%08d', $nextNum);
    }

    /**
     * Map shipped quantities for interior projects
     */
    protected function interiorShippedQtyMap(string $idPesanan): array
    {
        if ($idPesanan === '') return [];

        $rows = $this->suratJalanItemModel
            ->select('surat_jalan_item.kode_barang, surat_jalan_item.qty')
            ->join('surat_jalan sj', 'sj.id = surat_jalan_item.surat_jalan_id')
            ->where('sj.id_pesanan', $idPesanan)
            ->where('sj.status', 'printed')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $kode = trim((string)($r['kode_barang'] ?? ''));
            if ($kode === '') continue;
            $key = 'I||' . $kode;
            $map[$key] = (int)($map[$key] ?? 0) + (int)($r['qty'] ?? 0);
        }
        return $map;
    }

    /**
     * Format NF display number (NF00000033 -> NF0033)
     */
    protected function displayNoNf(string $idPesanan): string
    {
        if (preg_match('/^NF(\d+)$/i', $idPesanan, $m)) {
            $digits = $m[1];
            $last4  = substr($digits, -3);
            return 'NF' . str_pad($last4, 4, '0', STR_PAD_LEFT);
        }
        return $idPesanan;
    }

    /**
     * Get PPN rate from mode string
     */
    protected function ppnRateFromMode(string $mode): int
    {
        $mode = strtolower(trim($mode));
        if ($mode === 'ppn10') return 10;
        if ($mode === 'ppn11') return 11;
        return 0;
    }
}
