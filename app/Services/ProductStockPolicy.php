<?php
namespace App\Services;
final class ProductStockPolicy
{
    /** Metadata/image edits must never overwrite stock sold since the form was opened. */
    public static function rebase(array $current,array $submitted): array
    {
        $stocks=[];
        foreach($current as $v) $stocks[mb_strtolower(trim((string)$v['nama']))]=(int)$v['stok'];
        $seen=[];
        foreach($submitted as &$v) {
            if(!is_array($v)||trim((string)($v['nama']??''))==='') throw new \RuntimeException('Varian tidak valid.');
            $key=mb_strtolower(trim((string)$v['nama']));
            if(isset($seen[$key])) throw new \RuntimeException('Nama varian harus unik.');
            $seen[$key]=true; $v['stok']=$stocks[$key]??0;
        }
        unset($v);
        foreach($stocks as $key=>$stock) if($stock>0&&!isset($seen[$key])) throw new \RuntimeException('Varian dengan saldo stok tidak boleh dihapus/diubah nama. Gunakan Mutasi terlebih dahulu.');
        return $submitted;
    }
}
