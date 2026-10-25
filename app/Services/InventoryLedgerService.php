<?php
namespace App\Services;

/** Stock + ledger effects for manually confirmed orders. Caller owns the order lock/transaction. */
final class InventoryLedgerService
{
    public function __construct(private $db) {}
    public function apply(array $order): void
    {
        $items=json_decode((string)$order['items'],true); $mid=json_decode((string)$order['data_mid'],true)?:[];
        if (!is_array($items)) throw new \RuntimeException('Rincian pesanan tidak valid.');
        $lines=[];
        foreach ($items as $item) {
            if (in_array(strtolower((string)($item['name']??'')),['voucher','flash sale','biaya admin','biaya ongkir'],true)) continue;
            $variant=(string)($item['variant']??'');
            if ($variant==='' && preg_match('/\(([^()]*)\)\s*$/',(string)($item['name']??''),$m)) $variant=$m[1];
            $id=(string)($item['id']??''); $qty=filter_var($item['quantity']??null,FILTER_VALIDATE_INT);
            if ($id===''||$variant===''||$qty===false||$qty<=0||$qty>10000) throw new \RuntimeException('Produk, varian atau qty tidak valid.');
            $key=$id."\0".mb_strtolower($variant); $lines[$key]??=['id'=>$id,'variant'=>$variant,'qty'=>0]; $lines[$key]['qty']+=$qty;
        }
        if (!$lines) throw new \RuntimeException('Pesanan tidak memiliki produk.');
        ksort($lines);
        foreach ($lines as $line) {
            $sql=$this->db->table('barang')->where('id',$line['id'])->getCompiledSelect();
            $product=$this->db->query($sql.($this->db->DBDriver==='MySQLi'?' FOR UPDATE':''))->getRowArray();
            $variants=json_decode($product['varian']??'[]',true);
            if (!$product||!is_array($variants)) throw new \RuntimeException('Produk tidak ditemukan.');
            $balance=null;
            foreach ($variants as &$variant) {
                if (strcasecmp((string)($variant['nama']??''),$line['variant'])!==0) continue;
                $balance=(int)$variant['stok']-$line['qty'];
                InventoryAvailability::assertRemaining($this->db,$line['id'],$line['variant'],$balance,(string)$order['id_midtrans']);
                $variant['stok']=$balance; break;
            }
            unset($variant);
            if ($balance===null) throw new \RuntimeException('Varian tidak ditemukan.');
            $this->db->table('barang')->where('id',$line['id'])->update(['varian'=>json_encode($variants)]);
            $ledger=$this->db->table('kartu_stok')->where('id_pesanan',$order['id_midtrans'])->where('id_barang',$line['id'])->where('LOWER(varian)',mb_strtolower($line['variant']))->get()->getResultArray();
            if (count($ledger)>1 || (isset($ledger[0]) && !filter_var($ledger[0]['pending'],FILTER_VALIDATE_BOOLEAN))) throw new \RuntimeException('Kartu stok lama sudah diproses atau berulang; perlu rekonsiliasi.');
            $values=['id_barang'=>$line['id'],'tanggal'=>$mid['transaction_time']??gmdate('Y-m-d H:i:s'),'id_pesanan'=>$order['id_midtrans'],'varian'=>strtoupper($line['variant']),
                'keterangan'=>$order['id_midtrans'].'-'.$line['id'].'-'.strtoupper($line['variant']),'debit'=>0,'kredit'=>$line['qty'],'saldo'=>$balance,'pending'=>true,'alasan'=>''];
            if ($ledger) $this->db->table('kartu_stok')->where('id',$ledger[0]['id'])->update($values);
            else $this->db->table('kartu_stok')->insert($values);
        }
        if (!$this->db->transStatus()) throw new \RuntimeException('Stok gagal disimpan.');
    }
}
