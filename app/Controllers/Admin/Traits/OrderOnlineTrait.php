<?php
/**
 * OrderOnlineTrait - Handles online order-related methods
 * 
 * Methods:
 * - order(): Display online orders list
 * - actionEditResi(): Update shipping info
 * - reprint(): Display reprint requests
 * - accReprint($id): Accept reprint request
 * - marketplace(): Display marketplace orders
 * - confirmMarketplace($id): Confirm marketplace order
 * - orderToko($ind): Process store order
 * - labelBarang($id): Display product labels
 */

namespace App\Controllers\Admin\Traits;

trait OrderOnlineTrait
{
    /**
     * Display online orders list
     */
    public function order()
    {
        $pesanan = $this->pemesananModel->getPemesanan();
        foreach ($pesanan as $ind_p => $p) {
            $pesanan[$ind_p]['data_mid'] = json_decode($p['data_mid'], true);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field1']))
                $pesanan[$ind_p]['data_mid']['custom_field1'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field1']);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field2']))
                $pesanan[$ind_p]['data_mid']['custom_field2'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field2']);
            if (isset($pesanan[$ind_p]['data_mid']['custom_field3']))
                $pesanan[$ind_p]['data_mid']['custom_field3'] = base64_encode($pesanan[$ind_p]['data_mid']['custom_field3']);
            $pesanan[$ind_p]['items'] = json_decode($p['items'], true);
            $pesanan[$ind_p]['kurir'] = json_decode($p['kurir'], true);
        }
        $data = [
            'title' => 'Pesanan',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pesanan' => $pesanan,
            'pesananJson' => json_encode($pesanan)
        ];
        return view('admin/order', $data);
    }

    /**
     * Update shipping info
     */
    public function actionEditResi()
    {
        $nama = $this->request->getVar('nama');
        $deskripsi = $this->request->getVar('deskripsi');
        $resi = $this->request->getVar('resi');
        $idMid = $this->request->getVar('idMid');
        $this->pemesananModel->where(['id_midtrans' => $idMid])->set([
            'kurir' => json_encode([
                'nama' => $nama,
                'deskripsi' => $deskripsi,
            ]),
            'resi' => $resi,
            'status' => 'Dikirim'
        ])->update();
        return redirect()->to('/admin/order/online');
    }

    /**
     * Display reprint requests
     */
    public function reprint()
    {
        $ajukanPrint = $this->ajukanPrintModel->findAll();
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        foreach ($ajukanPrint as $ind_a => $a) {
            $d = strtotime($a['tanggal']);
            $ajukanPrint[$ind_a]['tanggal'] = date("d", $d) . " " . $bulan[(int)date("m", $d) - 1] . " " . date("Y", $d);
        }
        $data = [
            'title' => 'Pengajuan Print Ulang',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'ajukan' => $ajukanPrint
        ];
        return view('admin/reprint', $data);
    }

    /**
     * Accept reprint request
     */
    public function accReprint($id_midtrans)
    {
        $this->pemesananModel->where(['id_midtrans' => $id_midtrans])->set(['status_print' => 'siap'])->update();
        $this->ajukanPrintModel->where(['id_midtrans' => $id_midtrans])->delete();
        $this->kartuStokModel->where(['id_pesanan' => $id_midtrans])->set(['pending' => true])->update();
        return redirect()->to('/admin/reprint');
    }

    /**
     * Display marketplace orders
     */
    public function marketplace()
    {
        $pemesanan = $this->pemesananModel->getPemesananMarket();
        $pemesananBelumKonfirmasi = [];
        foreach ($pemesanan as $p) {
            $pemesananGudang = $this->pemesananGudangModel->where([
                'id_pesanan' => $p['id_midtrans'],
            ])->first();
            if (!$pemesananGudang) {
                $items = json_decode($p['items'], true);
                $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
                $data_mid = json_decode($p['data_mid'], true);
                $data_mid['transaction_time'] = date('d', strtotime($data_mid['transaction_time'])) . ' ' . $bulan[date('m', strtotime($data_mid['transaction_time'])) - 1] . ' ' . date('Y', strtotime($data_mid['transaction_time']));
                $kurir = json_decode($p['kurir'], true);

                $pemesanan_curr = $p;
                $pemesanan_curr['items'] = $items;
                $pemesanan_curr['data_mid'] = $data_mid;
                $pemesanan_curr['kurir'] = $kurir;

                array_push($pemesananBelumKonfirmasi, $pemesanan_curr);
            }
        }

        $data = [
            'title' => 'Konfirmasi Marketplace',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'val' => [
                'msg' => session()->getFlashdata('msg')
            ],
            'pemesanan' => $pemesananBelumKonfirmasi,
            'pemesananJson' => json_encode($pemesananBelumKonfirmasi),
        ];
        return view('admin/marketplace', $data);
    }

    /**
     * Confirm marketplace order
     */
    public function confirmMarketplace($id)
    {
        $pemesanan = $this->pemesananModel->where([
            'id' => $id,
        ])->first();
        $items_curr = json_decode($pemesanan['items'], true);
        foreach ($items_curr as $i) {
            if ($i['name'] != "Voucher" && $i['name'] != "Biaya Admin") {
                for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                    $this->pemesananGudangModel->insert([
                        'id_pesanan' => $pemesanan['id_midtrans'],
                        'tanggal' => json_decode($pemesanan['data_mid'], true)['transaction_time'],
                        'nama' => $i['name'],
                        'id_barang' => $i['id'],
                        'packed' => false
                    ]);
                }
            }
        }
        return redirect()->to('/admin/marketplace');
    }

    /**
     * Process store order
     */
    public function orderToko($ind_add)
    {
        $email = session()->get('email');
        $nama = session()->get('nama');
        $nohp = session()->get('nohp');
        $keranjang = session()->get('keranjang');
        $alamat = session()->get('alamat')[$ind_add];

        $hargaTotal = 0;
        $items = [];
        foreach ($keranjang as $index => $k) {
            $produk = $this->barangModel->getBarang($k['id_barang']);
            foreach (json_decode($produk['varian'], true) as $v) {
                if ($v['nama'] == $k['varian']) {
                    $keranjang[$index]['src_gambar'] = "/viewvar/" . $k['id_barang'] . "/" . explode(',', $v['urutan_gambar'])[0];
                }
            }
            $keranjang[$index]['detail'] = $produk;
            $hargaTotal += $produk['harga'] * $k['jumlah'] * (100 - $produk['diskon']) / 100;

            $item = [
                'id' => $k['id_barang'],
                'price' => $produk['harga'],
                'quantity' => $k['jumlah'],
                'name' => $produk['nama'] . " " . json_decode($produk['deskripsi'], true)['dimensi']['asli']['panjang'] . ' (' . $k['varian'] . ')',
            ];
            array_push($items, $item);
        }

        $pesananke = $this->pemesananModel->orderBy('id', 'desc')->first();
        $idFix = "TOKO" . (sprintf("%08d", $pesananke ? ((int)$pesananke['id'] + 1) : 1));
        $randomId = "TOKO" . rand();
        $tanggal = date('Y-m-d H:i:s', strtotime('+7 hours'));
        $data = [
            'data_mid'          => json_encode([
                'transaction_time'  => $tanggal,
                'order_id'          => $idFix,
                'gross_amount'      => $hargaTotal,
                'payment_type'      => 'toko'
            ]),
            'id_midtrans'       => $idFix,
            'email'             => $email,
            'kurir'             => json_encode([]),
            'resi'              => 'Menunggu pengiriman',
            'harga'             => $hargaTotal,
            'nama'              => $nama,
            'nohp'              => $nohp,
            'alamat'            => $alamat['alamat_lengkap'],
            'status'            => 'Proses',
            'id_marketplace'    => '',
            'items'             => json_encode($items),
        ];
        $this->pemesananModel->insert($data);

        foreach ($items as $i) {
            if ($i['name'] != "Biaya Ongkir" && $i['name'] != "Biaya Admin") {
                for ($x = 1; $x <= (int)$i['quantity']; $x++) {
                    $this->pemesananGudangModel->insert([
                        'id_pesanan' => $idFix,
                        'tanggal' => $tanggal,
                        'nama' => $i['name'],
                        'id_barang' => $i['id'],
                        'packed' => false
                    ]);
                }
            }
        }

        $dataTransaksiFulDariDatabase = $this->pemesananModel->where('id_midtrans', $idFix)->first();
        $dataTransaksiFulDariDatabase_items = json_decode($dataTransaksiFulDariDatabase['items'], true);
        foreach ($dataTransaksiFulDariDatabase_items as $item) {
            $barangCurr = $this->barangModel->where('id', $item['id'])->first();
            $varianBarangCurr = json_decode($barangCurr['varian'], true);
            foreach ($varianBarangCurr as $ind_v => $v) {
                if ($v['nama'] == rtrim(explode("(", $item['name'])[1], ")")) {
                    $varianBarangCurr[$ind_v]['stok'] = (int)$v['stok'] - $item['quantity'];
                }
            }
            $this->barangModel->where('id', $item['id'])->set([
                'varian' => json_encode($varianBarangCurr)
            ])->update();
        }

        return redirect()->to('/order');
    }

    /**
     * Display product labels
     */
    public function labelBarang($id)
    {
        $pemesanan = $this->pemesananModel->where(['id_midtrans' => $id])->first();
        $pemesanan['items'] = json_decode($pemesanan['items'], true);
        $data = [
            'title' => 'Label Barang',
            'apikey_img_ilena' => $this->apikey_img_ilena,
            'pemesanan' => $pemesanan
        ];
        return view('admin/labelBarang', $data);
    }
}
