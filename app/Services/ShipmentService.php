<?php
namespace App\Services;

/** Shared, transaction-protected SJ lifecycle for catalogue and custom interior items. */
final class ShipmentService
{
    public function __construct(private $db) {}

    private function lock(string $table, string $field, $id): array
    {
        $sql=$this->db->table($table)->where($field,$id)->getCompiledSelect();
        $row=$this->db->query($sql.($this->db->DBDriver==='MySQLi'?' FOR UPDATE':''))->getRowArray();
        if (!$row) throw new \RuntimeException('Dokumen sumber tidak ditemukan.');
        return $row;
    }

    private function transaction(callable $operation)
    {
        $this->db->transBegin();
        try {
            $result=$operation();
            if (!$this->db->transStatus()) throw new \RuntimeException('Penyimpanan surat jalan gagal.');
            $this->db->transCommit();
            return $result;
        } catch (\Throwable $e) { $this->db->transRollback(); throw $e; }
    }

    private function key(array $item): string
    {
        return empty($item['id_barang']) ? 'I||'.(string)$item['kode_barang'] : (string)$item['id_barang'].'||'.(string)$item['varian'];
    }

    private function ordered(string $orderId): array
    {
        $project=$this->db->table('project_interior')->where('kode_sj',$orderId)->get()->getRowArray();
        $result=[];
        if ($project) {
            foreach ($this->db->table('project_interior_item')->where('project_id',$project['id'])->get()->getResultArray() as $item) {
                $key='I||'.$item['kode_barang'];
                if (isset($result[$key])) throw new \RuntimeException('Kode item interior ganda; periksa data project sebelum mengirim.');
                $result[$key]=['id_barang'=>null,'kode_barang'=>$item['kode_barang'],'nama_barang'=>$item['nama_barang'],'varian'=>'','qty'=>(int)$item['qty']];
            }
        } else {
            foreach ($this->db->table('pemesanan_offline_item')->select('id_barang, varian, COUNT(*) AS qty')->where('id_pesanan',$orderId)->groupBy('id_barang, varian')->get()->getResultArray() as $item) $result[$this->key($item)]=$item;
        }
        return $result;
    }

    private function shipped(string $orderId, int $exclude = 0): array
    {
        $rows=$this->db->table('surat_jalan_item i')->select('i.*')->join('surat_jalan s','s.id=i.surat_jalan_id')->where('s.id_pesanan',$orderId)->whereIn('s.status',['final','printed'])->where('s.id !=',$exclude)->get()->getResultArray();
        $totals=[];
        foreach ($rows as $row) { $key=$this->key($row); $totals[$key]=($totals[$key]??0)+(int)$row['qty']; }
        return $totals;
    }

    private function validate(array $sj, array $rows): void
    {
        $ordered=$this->ordered($sj['id_pesanan']); $shipped=$this->shipped($sj['id_pesanan'],(int)$sj['id']); $new=[];
        foreach ($rows as $row) { $key=$this->key($row); $new[$key]=($new[$key]??0)+(int)$row['qty']; }
        foreach ($new as $key=>$qty) {
            if (!isset($ordered[$key]) || $qty<0 || $qty+($shipped[$key]??0)>(int)$ordered[$key]['qty']) throw new \RuntimeException('Qty surat jalan melebihi sisa pesanan atau item sumber tidak cocok.');
        }
    }

    public function createOffline(string $orderId): int
    {
        return $this->transaction(function () use ($orderId) {
            $order=$this->lock('pemesanan_offline','id_pesanan',$orderId);
            if (!empty($order['is_draft'])) throw new \RuntimeException('Finalisasi pesanan terlebih dahulu.');
            if ($this->db->table('project_interior')->where('kode_sj',$orderId)->countAllResults()) throw new \RuntimeException('Gunakan pembuatan SJ dari halaman project interior.');
            return $this->create($orderId, $this->ordered($orderId), false);
        });
    }

    private function create(string $orderId, array $items, bool $interior): int
    {
        $shipped=$this->shipped($orderId);
        $items=array_filter($items,static fn($item,$key)=>(int)$item['qty']-($shipped[$key]??0)>0,ARRAY_FILTER_USE_BOTH);
        if (!$items) throw new \RuntimeException('Tidak ada sisa item untuk dikirim.');
        $last=$this->db->table('surat_jalan')->selectMax('sj_ke','last_ke')->where('id_pesanan',$orderId)->get()->getRowArray();
        $this->db->table('surat_jalan')->insert(['id_pesanan'=>$orderId,'no_sj'=>null,'sj_ke'=>(int)($last['last_ke']??0)+1,'tanggal'=>gmdate('Y-m-d H:i:s'),'status'=>'draft','finalized_at'=>null]);
        $id=(int)$this->db->insertID();
        if ($id<=0) throw new \RuntimeException('Header surat jalan gagal disimpan.');
        foreach ($items as $key=>$item) {
            $qty=$interior ? 0 : max(0,(int)$item['qty']-($shipped[$key]??0));
            $this->db->table('surat_jalan_item')->insert([
                'surat_jalan_id'=>$id,'id_barang'=>$item['id_barang']??null,'kode_barang'=>$item['kode_barang']??null,
                'nama_barang'=>$item['nama_barang']??null,'varian'=>$item['varian']??'','qty'=>$qty,
            ]);
        }
        return $id;
    }

    public function createInterior(string $projectCode, array $keys): int
    {
        return $this->transaction(function () use ($projectCode,$keys) {
            $project=$this->lock('project_interior','kode_project',$projectCode);
            $orderId=(string)$project['kode_sj']; $this->lock('pemesanan_offline','id_pesanan',$orderId);
            $dp=$this->db->table('project_interior_payment')->selectSum('nominal','total')->where('project_id',$project['id'])->where('jenis','dp')->get()->getRowArray();
            if ((int)($dp['total']??0)<=0) throw new \RuntimeException('Wajib DP sebelum membuat surat jalan.');
            $all=$this->ordered($orderId); $selected=[];
            foreach ($keys as $key) {
                if (!is_string($key)) throw new \RuntimeException('Pilihan item tidak valid.');
                $key=str_starts_with($key,'I||') ? $key : 'I||'.rtrim($key,'|');
                if (!isset($all[$key])) throw new \RuntimeException('Item terpilih bukan milik project.');
                $selected[$key]=$all[$key];
            }
            if (!$selected) throw new \RuntimeException('Pilih minimal satu item.');
            return $this->create($orderId,$selected,true);
        });
    }

    private function lockShipment(int $id): array
    {
        $sj=$this->db->table('surat_jalan')->where('id',$id)->get()->getRowArray();
        if (!$sj) throw new \RuntimeException('Surat jalan tidak ditemukan.');
        $this->lock('pemesanan_offline','id_pesanan',$sj['id_pesanan']);
        return $this->lock('surat_jalan','id',$id);
    }

    public function save(int $id, array $ids, array $quantities, ?string $date): void
    {
        $this->transaction(function () use ($id,$ids,$quantities,$date) {
            $sj=$this->lockShipment($id);
            if ($sj['status']!=='draft') throw new \RuntimeException('SJ final/printed tidak dapat diedit.');
            if (count($ids)!==count($quantities) || count($ids)!==count(array_unique($ids,SORT_REGULAR))) throw new \RuntimeException('Payload item tidak valid atau berulang.');
            $rows=$this->db->table('surat_jalan_item')->where('surat_jalan_id',$id)->get()->getResultArray();
            $byId=[]; foreach ($rows as $i=>$row) $byId[(int)$row['id']]=$i;
            foreach ($ids as $i=>$itemId) {
                $itemId=filter_var($itemId,FILTER_VALIDATE_INT); $qty=filter_var($quantities[$i],FILTER_VALIDATE_INT);
                if ($itemId===false || !isset($byId[$itemId]) || $qty===false || $qty<0) throw new \RuntimeException('Item bukan milik surat jalan atau qty tidak valid.');
                $rows[$byId[$itemId]]['qty']=$qty;
            }
            $this->validate($sj,$rows);
            if ($date!==null && $date!=='') {
                $date=str_replace('T',' ',$date);
                if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(?::\d{2})?$/D',$date) || strtotime($date)===false) throw new \RuntimeException('Tanggal tidak valid.');
                $this->db->table('surat_jalan')->where('id',$id)->update(['tanggal'=>$date]);
            }
            foreach ($rows as $row) $this->db->table('surat_jalan_item')->where('id',$row['id'])->where('surat_jalan_id',$id)->update(['qty'=>(int)$row['qty']]);
        });
    }

    public function finalize(int $id): void
    {
        $this->transaction(function () use ($id) {
            $sj=$this->lockShipment($id);
            if (in_array($sj['status'],['final','printed'],true)) return;
            if ($sj['status']!=='draft') throw new \RuntimeException('Status SJ tidak dapat difinalisasi.');
            $rows=$this->db->table('surat_jalan_item')->where('surat_jalan_id',$id)->get()->getResultArray();
            if (array_sum(array_column($rows,'qty'))<=0) throw new \RuntimeException('Isi qty surat jalan terlebih dahulu.');
            $this->validate($sj,$rows);
            $order=$this->db->table('pemesanan_offline')->where('id_pesanan',$sj['id_pesanan'])->get()->getRowArray();
            $date=new \DateTimeImmutable($sj['tanggal']); $suffix='/SJ/CBM/'.$date->format('n/Y'); $nf=($order['jenis']??'')==='nf'; $max=0;
            foreach ($this->db->table('surat_jalan')->select('no_sj')->like('no_sj',$suffix,'before')->get()->getResultArray() as $legacy) {
                if (preg_match($nf?'~^NF(\d+)/SJ/~':'~^(\d+)/SJ/~',(string)$legacy['no_sj'],$m)) $max=max($max,(int)$m[1]);
            }
            $next=DocumentSequence::next($this->db,($nf?'NF':'SJ').':'.$date->format('Y-m'),$max);
            $number=($nf?'NF':'').str_pad((string)$next,$nf?4:5,'0',STR_PAD_LEFT).$suffix;
            $this->db->table('surat_jalan')->where('id',$id)->update(['no_sj'=>$number,'status'=>'final','finalized_at'=>gmdate('Y-m-d H:i:s')]);
        });
    }
}
