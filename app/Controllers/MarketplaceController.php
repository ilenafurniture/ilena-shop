<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\GambarBarangModel;
use App\Models\PembeliModel;
use App\Models\PemesananModel;
use App\Models\UserModel;
use App\Models\KoleksiModel;
use App\Models\JenisModel;
use App\Models\KeranjangMarketModel;
use App\Models\KartuStokModel;

class MarketplaceController extends BaseController
{
    protected $barangModel;
    protected $gambarBarangModel;
    protected $userModel;
    protected $pembeliModel;
    protected $pemesananModel;
    protected $koleksiModel;
    protected $jenisModel;
    protected $keranjangMarketModel;
    protected $kartuStokModel;
    protected $session;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->gambarBarangModel = new GambarBarangModel();
        $this->userModel = new UserModel();
        $this->pembeliModel = new PembeliModel();
        $this->pemesananModel = new PemesananModel();
        $this->koleksiModel = new KoleksiModel();
        $this->jenisModel = new JenisModel();
        $this->keranjangMarketModel = new KeranjangMarketModel();
        $this->kartuStokModel = new KartuStokModel();
        $this->session = \Config\Services::session();
    }

    public function product()
    {
        $cart = (new \App\Services\SessionMarketCart())->items();
        $product = $this->barangModel->getBarang();
        $data = [
            'title' => 'Semua Produk',
            'produk' => $product,
            'keranjang' => $cart
        ];
        return view('market/product', $data);
    }
    public function actionFind()
    {
        $cari = str_replace(" ", "-", $this->request->getVar('cari'));
        return redirect()->to('/market/find/' . $cari);
    }
    public function find($teks)
    {
        $cari = str_replace("-", " ", $teks);
        $cart = (new \App\Services\SessionMarketCart())->items();
        $produk = $this->barangModel->like('nama', $cari, 'both')->where(['active' => '1'])->findAll();
        $data = [
            'title' => 'Cari Produk',
            'produk' => $produk,
            'keranjang' => $cart,
            'find' => $cari
        ];
        return view('market/product', $data);
    }

    public function cart()
    {
        $cart = (new \App\Services\SessionMarketCart())->items();
        foreach ($cart as $ind_c => $c) {
            $produknya = $this->barangModel->getBarang($c['id_barang']);
            $cart[$ind_c]['nama'] = $produknya['nama'] ?? 'Produk tidak tersedia';
        }
        $data = [
            'title' => 'Keranjang Produk',
            'keranjang' => $cart
        ];
        return view('market/cart', $data);
    }

    public function addCart($id_barang, $varian)
    {
        $product=$this->barangModel->getBarang($id_barang); $variant=rawurldecode((string)$varian); $valid=false;
        foreach (json_decode($product['varian']??'[]',true)??[] as $v) if (strcasecmp((string)$v['nama'],$variant)===0 && (int)$v['stok']>0) $valid=true;
        if (!$valid) return redirect()->to('/market/product')->with('msg','Produk/varian tidak tersedia.');
        (new \App\Services\SessionMarketCart())->add((string)$id_barang,$variant);
        return redirect()->to('/market/product');
    }

    public function submitOrder()
    {
        $cart=new \App\Services\SessionMarketCart(); $rows=array_values($cart->items());
        if (!$rows) return redirect()->to('/market/cart')->with('msg','Keranjang kosong atau sudah dikirim.');
        $date=(string)$this->request->getPost('tanggal');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/D',$date) || strtotime($date)===false) return redirect()->back()->with('msg','Tanggal pesanan tidak valid.');
        $db=db_connect(); $db->transBegin();
        try {
            usort($rows,static fn($a,$b)=>[(string)$a['id_barang'],(string)$a['varian']]<=>[(string)$b['id_barang'],(string)$b['varian']]);
            $id=\App\Services\OrderIdentifiers::market(); $items=[]; $total=0;
            $timestamp=$date.' '.(new \DateTimeImmutable('now',new \DateTimeZone('Asia/Jakarta')))->format('H:i:s');
            foreach ($rows as $row) {
                $product=$this->barangModel->getBarangForUpdate($row['id_barang']);
                if (!$product) throw new \RuntimeException('Produk tidak tersedia.');
                $variant=null;
                foreach (json_decode($product['varian'],true)??[] as $v) if (strcasecmp((string)$v['nama'],(string)$row['varian'])===0) $variant=$v;
                $qty=(int)$row['jumlah'];
                if (!$variant || $qty<1 || $qty>(int)$variant['stok']) throw new \RuntimeException('Varian atau stok berubah. Periksa keranjang.');
                $price=\App\Services\CatalogPricing::unitPrice($product);
                $desc=json_decode($product['deskripsi']??'{}',true);
                $name=$product['nama'].' '.($desc['dimensi']['asli']['panjang']??'').' ('.$variant['nama'].')';
                $items[]=['id'=>$product['id'],'name'=>$name,'variant'=>$variant['nama'],'quantity'=>$qty,'price'=>$price];
                $total+=$price*$qty;
                if (!$this->kartuStokModel->insert(['id_barang'=>$product['id'],'tanggal'=>$timestamp,'keterangan'=>$id.'-'.$product['id'].'-'.strtoupper($variant['nama']),'debit'=>0,'kredit'=>$qty,'saldo'=>(int)$variant['stok'],'pending'=>true,'id_pesanan'=>$id,'varian'=>strtoupper($variant['nama'])])) throw new \RuntimeException('Kartu stok gagal disimpan.');
            }
            $post=fn($name)=>(string)$this->request->getPost($name);
            $order=['id_midtrans'=>$id,'items'=>json_encode($items),'data_mid'=>json_encode(['transaction_time'=>$timestamp,'order_id'=>$id,'gross_amount'=>$total,'payment_type'=>'market']),
                'email'=>$post('email'),'nama'=>$post('nama_lengkap'),'nohp'=>$post('no_hp'),'alamat'=>$post('alamat_lengkap'),'status'=>'Proses','resi'=>$post('no_resi'),'id_marketplace'=>$post('id_marketplace'),
                'status_print'=>'siap','keterangan_suratjalan'=>'','kurir'=>json_encode(['nama'=>$post('nama_ekspedisi'),'deskripsi'=>$post('jenis_kurir'),'harga'=>(int)$post('harga_pengiriman'),'estimasi'=>$post('estimasi')])];
            if (!$this->pemesananModel->insert($order) || !$db->transStatus()) throw new \RuntimeException('Pesanan gagal disimpan.');
            $db->transCommit(); $cart->clear();
            return redirect()->to('/market/product')->with('msg','Pesanan disimpan untuk konfirmasi.');
        } catch (\Throwable $e) { $db->transRollback(); log_message('error','Marketplace submit: {error}',['error'=>$e->getMessage()]); return redirect()->back()->with('msg','Pesanan gagal diproses: '.$e->getMessage()); }
    }

    public function reduceCart($id)
    {
        (new \App\Services\SessionMarketCart())->reduce((string)$id);
        return redirect()->to('/market/product');
    }
}
