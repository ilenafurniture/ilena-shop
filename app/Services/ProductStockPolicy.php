<?php
namespace App\Services;
final class ProductStockPolicy
{
    /** Metadata/image edits must never overwrite stock sold since the form was opened. */
    public static function rebase(array $current,array $submitted): array
    {
        $stocks=[];
        foreach($current as $v) {
            if (!is_array($v) || trim((string)($v['nama'] ?? '')) === '') {
                throw new \RuntimeException('Data varian tersimpan tidak valid. Perbaiki data produk sebelum menyimpan perubahan.');
            }
            if (!preg_match('/^\d+$/', (string)($v['stok'] ?? ''))) {
                throw new \RuntimeException('Saldo varian tersimpan tidak valid. Perbaiki data produk sebelum menyimpan perubahan.');
            }
            $key = mb_strtolower(trim((string)$v['nama']));
            if (array_key_exists($key, $stocks)) {
                throw new \RuntimeException('Data varian tersimpan memiliki nama ganda. Perbaiki data produk sebelum menyimpan perubahan.');
            }
            $stocks[$key] = max(0, (int)($v['stok'] ?? 0));
        }
        $seen=[];
        foreach($submitted as &$v) {
            if(!is_array($v)||trim((string)($v['nama']??''))==='') throw new \RuntimeException('Varian tidak valid.');
            $key=mb_strtolower(trim((string)$v['nama']));
            if(isset($seen[$key])) throw new \RuntimeException('Nama varian harus unik.');
            if (isset($stocks[$key]) && $stocks[$key] > 0) {
                foreach ($current as $oldVariant) {
                    if (mb_strtolower(trim((string)($oldVariant['nama'] ?? '')), 'UTF-8') === $key
                        && trim((string)$oldVariant['nama']) !== trim((string)$v['nama'])) {
                        throw new \RuntimeException('Varian dengan saldo stok tidak boleh diubah nama, termasuk kapitalisasi. Gunakan Mutasi terlebih dahulu.');
                    }
                }
            }
            $seen[$key]=true; $v['stok']=$stocks[$key]??0;
        }
        unset($v);
        foreach($stocks as $key=>$stock) if($stock>0&&!isset($seen[$key])) throw new \RuntimeException('Varian dengan saldo stok tidak boleh dihapus/diubah nama. Gunakan Mutasi terlebih dahulu.');
        return $submitted;
    }
}
