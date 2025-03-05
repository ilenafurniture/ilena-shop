<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
.notif {
    position: fixed;
    bottom: 50px;
    right: 0px;
    padding: 0.6em 2em;
    color: white;
    border-radius: 7px;
    color: #e84a49;
    letter-spacing: -1px;
    font-size: 15px;
    background-color: #e8494911;
    transition: 0.5s;
    transform: translateX(100%);
}

.notif.show {
    right: 50px;
    transform: translateX(0%);
    transition: 0.5s;
}

.item-produk {
    border-radius: 12px;
}

.item-produk img {
    width: 50px;
    border-radius: 10px;
}

.item-produk .item-varian {
    cursor: pointer;
    outline: 1px solid gray;
    border: 1px solid white;
    border-radius: 2em;
    width: 14px;
    height: 14px;
    margin: 0;
    padding: 0;
}

.item-produk .item-varian:hover {
    outline: 1px solid var(--merah);
}

.item-keranjang-admin img {
    width: 50px;
    border-radius: 10px;
    height: 50px;
    object-fit: cover;
}

.item-keranjang-admin .counter {
    display: flex;
    align-items: center;
}

.item-keranjang-admin .counter .action {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 20px;
    height: 20px;
    border-radius: 20px;
    background-color: #e8494911;
    color: var(--merah);
    font-weight: 500;
    cursor: pointer;
}

.item-keranjang-admin .counter .action:hover {
    background-color: var(--merah);
    color: white;
}

.item-keranjang-admin .counter .angka {
    width: 30px;
    text-align: center;
    font-weight: bold;
}

input {
    font-size: 13px;
}
</style>
<div style="padding: 2em;" class="h-100 d-flex flex-column">
    <h1 class="teks-sedang mb-4">Buat Pesanan</h1>
    <div id="container-react" style="flex: 1;" class="d-flex gap-3"></div>
</div>
<script src="https://unpkg.com/react@17/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@17/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
<script>
console.log(JSON.parse('<?= $produkJson; ?>'))
</script>
<script type="text/babel">
    const { useState, useEffect } = React;
    const produkSemua = JSON.parse('<?= $produkJson; ?>');
    function waktuSkrg() {
        const date = new Date();
        let datePart = [
            date.getFullYear(),
            date.getMonth() + 1,
            date.getDate()
        ].map((n, i) => n.toString().padStart(i === 0 ? 4 : 2, "0")).join("-");
        let timePart = [
            date.getHours(),
            date.getMinutes(),
            date.getSeconds()
        ].map((n, i) => n.toString().padStart(2, "0")).join(":");
        return datePart + " " + timePart;
    }
    const App = () => {
        const [listProduk, setListProduk] = useState(JSON.parse('<?= $produkJson; ?>'))
        const [currentPage, setCurrentPage] = useState(1);
        const [pageNumbers, setPageNumbers] = useState([]);
        const [currentItems, setCurrentItems] = useState([]); 
        const [cari, setCari] = useState('');
        const [keranjang, setKeranjang] = useState([]);
        const [notif, setNotif] = useState({
            show: false,
            teks: 'Stok tidak cukup'
        });
        const [totalHarga, setTotalHarga] = useState(0);
        const [formData, setFormData] = useState({
            nama: '',
            email: '',
            nohp: '',
            alamat: {
                pelengkap: '',
                kelurahan: '',
                kecamatan: '',
                kabupaten: '',
                provinsi: '',
            },
            keteranganSJ: '',
            hargaTotal: 0,
            waktu: waktuSkrg()
        });
        const [kabupaten, setKabupaten] = useState([])
        const [kecamatan, setKecamatan] = useState([])
        const [kelurahan, setKelurahan] = useState([])
        const [canSave, setCanSave] = useState(false);
        
        useEffect(()=>{
            if(formData.alamat.provinsi) {
                const idprov = formData.alamat.provinsi.split("-")[0];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkota/" + idprov);
                    const kota = await response.json();
                    const hasil = kota.rajaongkir.results;
                    setKabupaten(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, alamat: {...formData.alamat, kabupaten: '', kecamatan: '', kelurahan: ''}})
            setKecamatan([]);
            setKelurahan([]);
        }, [formData.alamat.provinsi])

        useEffect(()=>{
            if(formData.alamat.kabupaten) {
                const idkab = formData.alamat.kabupaten.split("-")[0];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkec/" + idkab);
                    const kota = await response.json();
                    const hasil = kota.rajaongkir.results;
                    setKecamatan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, alamat: {...formData.alamat, kecamatan: '', kelurahan: ''}})
            setKelurahan([]);
        }, [formData.alamat.kabupaten])

        useEffect(()=>{
            if(formData.alamat.kecamatan) {
                const idkec = formData.alamat.kecamatan.split("-")[1];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkode/" + idkec);
                    const hasil = await response.json();
                    setKelurahan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, alamat: {...formData.alamat, kelurahan: ''}})
        }, [formData.alamat.kecamatan])

        useEffect(()=>{
            setCurrentPage(1);
        }, [pageNumbers])

        useEffect(() => {
            setPageNumbers(Array.from({ length: Math.ceil(listProduk.length / 20) }, (_, i) => i + 1));
        }, [listProduk]); // Gunakan listProduk sebagai dependency

        useEffect(() => {
            const indexOfLastItem = currentPage * 20;
            const indexOfFirstItem = indexOfLastItem - 20;
            setCurrentItems(
                listProduk.slice(indexOfFirstItem, indexOfLastItem)
            );
        }, [currentPage, listProduk]);

        useEffect(()=>{
            const listProdukBaru = [...produkSemua].filter((produk)=>{
                return produk.nama.toLowerCase().includes(cari.toLowerCase());
            });
            setListProduk(listProdukBaru)
        }, [cari])

        useEffect(()=>{
            if(formData.nama && formData.email && formData.nohp && formData.keteranganSJ && formData.alamat.provinsi && formData.alamat.kabupaten && formData.alamat.kecamatan && formData.alamat.kelurahan && formData.alamat.pelengkap && keranjang.length > 0) {
                setCanSave(true)
            } else {
                setCanSave(false)
            }
        }, [formData, keranjang])

        useEffect(()=>{
            if(keranjang.length > 0) {
                const hargaTotal = keranjang.map((k) => {
                    return Number(k.price) * Number(k.quantity)
                }).reduce((prev, cur) => {
                    return prev + cur;
                }, 0)
                setFormData({...formData, hargaTotal: hargaTotal})
            } else {
                setFormData({...formData, hargaTotal: 0})
            }
        }, [keranjang])

        const handlePilih = (produk, varian) => {
            const name = `${produk.nama} ${produk.dimensi.panjang} (${varian.nama})`;
            const stok = Number(varian.stok);
            const cekName = keranjang.filter((k)=>{
                return k.name == name;
            });
            if(stok == 0) {
                setNotif({teks: 'Stok habis', show: true});
                setTimeout(() => {
                    setNotif({teks: 'Stok habis', show: false});
                }, 3000);
                return;
            }
            if(cekName.length > 0) {
                setKeranjang(keranjang.map((k)=>{
                    if(k.name == name) {
                        return {
                            ...k,
                            quantity: (k.quantity + 1) > stok ? k.quantity : (k.quantity + 1)
                        }
                    } else {
                        return k
                    }
                }))
            } else {
                setKeranjang([...keranjang, {
                    id: produk.id,
                    price: produk.harga,
                    quantity: 1,
                    name: name,
                    detail: {
                        produk, varian
                    }
                }]);
            }
        }

        const handleCount = (index, max, tambah) =>{
            setKeranjang(keranjang.map((k ,ind_k)=>{
                if(ind_k == index) {
                    return {
                        ...k,
                        quantity: (k.quantity + tambah) > max ? k.quantity : (k.quantity + tambah)
                    }
                } else {
                    return k
                }
            }).filter((k)=>{return k.quantity > 0}))
        }

        const handleSubmit =()=>{
            const body = {...formData, 
                alamatLengkap: `${formData.alamat.pelengkap} ${formData.alamat.kelurahan.split('-')[0]}, ${formData.alamat.kecamatan.split('-')[1]}, ${formData.alamat.kabupaten.split('-')[1]}, ${formData.alamat.provinsi.split('-')[1]} ${formData.alamat.kelurahan.split('-')[1]}`,
                keranjang: keranjang
            }

            console.log('formData')
            console.log(formData)
            console.log('bodynya')
            console.log(body)
            async function fetchSubmit() {
                const response = await fetch(
                    `/admin/order/add`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(body),
                    }
                );
                const responseJson = await response.json()
                console.log(responseJson)
                if(response.status == 200) {
                    window.alert('Berhasil menambahkan pesanan')
                    window.location.href = '/admin/order'
                }
            }
            fetchSubmit()
        }

        return (
            <>
                <div className={`notif ${notif.show ? 'show' : ''}`}>{notif.teks}</div>
                <div style={{flex: 1 }} className="d-flex flex-column">
                    <div>
                        <input type="text" onChange={(e)=>{setCari(e.target.value)}} className="form-control mb-3" placeholder="Cari barang" />
                    </div>
                    <div className="w-100 mb-4" style={{flex: 1, position: 'relative'}}>
                        <div className="w-100 h-100 pe-2" style={{position: 'absolute', overflow: 'auto'}}>
                            <div className="d-flex flex-column gap-2">
                                {currentItems.map((i, ind_i)=>(
                                    <div key={ind_i} className="item-produk">
                                        <div className="d-flex gap-2 align-items-center">
                                            <img src={i.gambar} alt={i.nama} />
                                            <div style={{flex: 1}}>
                                                <p className="m-0 fw-bold" style={{textWrap: 'nowrap'}}>{i.nama}</p>
                                                <p className="m-0" style={{fontSize: '12px'}}>{i.dimensi.panjang} x {i.dimensi.lebar} x {i.dimensi.tinggi}</p>
                                            </div>
                                            <div className="d-flex gap-2">
                                                {i.varian.map((v, ind_v)=>(
                                                    <div className="item-varian" style={{backgroundColor: v.kode}} key={ind_v} onClick={()=>{handlePilih(i, v)}}></div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                    {/* Pagination */}
                    <div className="container-pag">
                        <a
                            onClick={() =>
                                setCurrentPage((prev) =>
                                    Math.max(prev - 1, 1)
                                )
                            }
                            disabled={currentPage === 1}
                            className='item-pag'
                        >
                            <i className="material-icons">chevron_left</i>
                        </a>

                        {pageNumbers.map(
                            (number) =>
                                number >= currentPage - 2 &&
                                number <= currentPage + 2 && (
                                    <a
                                        key={number}
                                        onClick={() =>
                                            setCurrentPage(number)
                                        }
                                        className={`item-pag ${
                                            currentPage === number
                                                ? "active"
                                                : ""
                                        }`}
                                    >
                                        {number}
                                    </a>
                                )
                        )}

                        <a
                            onClick={() =>
                                setCurrentPage((prev) =>
                                    Math.min(prev + 1, pageNumbers.length)
                                )
                            }
                            disabled={currentPage === pageNumbers.length}
                            className="item-pag"
                        >
                            <i className="material-icons">chevron_right</i>
                        </a>
                    </div>
                </div>
                <div style={{flex: 1}} className="d-flex flex-column">
                    <h5 className="m-0">Keranjang</h5>
                    <hr />
                    <div className="w-100" style={{flex: 1, position: 'relative'}}>
                        <div className="w-100 h-100 pe-2" style={{position: 'absolute', overflow: 'auto'}}>
                            <div className="d-flex flex-column gap-2">
                                {keranjang.length > 0 ?
                                    <>
                                    {keranjang.map((i, ind_i)=>(
                                        <div key={ind_i} className="item-keranjang-admin w-100">
                                            <div className="d-flex gap-2 align-items-center w-100">
                                                <img src={i.detail.produk.gambar} alt={i.name} />
                                                <div style={{flex: 1}}>
                                                    <p className="m-0 fw-bold" style={{textWrap: 'nowrap'}}>{i.detail.produk.nama}</p>
                                                    <p className="m-0" style={{fontSize: '12px'}}><b>{i.detail.produk.dimensi.panjang}</b> | {i.detail.varian.nama}</p>
                                                </div>
                                                <div className="counter">
                                                    <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), -1)}}>-</div>
                                                    <div className="angka">{i.quantity}</div>
                                                    <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), 1)}}>+</div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                    </>
                                    :
                                    <div className={"text-secondary text-center"}><i>Keranjang kosong</i></div>
                                }
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div className="d-flex gap-1 mb-1">
                        <input type="text" className="form-control" placeholder="Nama penerima" value={formData.nama} onChange={(e)=>{setFormData({...formData, nama: e.target.value})}} />
                        <input type="text" className="form-control" placeholder="Email penerima" value={formData.email} onChange={(e)=>{setFormData({...formData, email: e.target.value})}} />
                    </div>
                    <div className="d-flex gap-1 mb-1">
                        <input type="text" className="form-control" placeholder="Nohp penerima" value={formData.nohp} onChange={(e)=>{setFormData({...formData, nohp: e.target.value})}} />
                        <input type="text" className="form-control" placeholder="Keterangan" value={formData.keteranganSJ} onChange={(e)=>{setFormData({...formData, keteranganSJ: e.target.value})}} />
                    </div>
                    <input type="datetime-local" className="form-control w-100" value={formData.waktu} onChange={(e)=>{setFormData({...formData, waktu: e.target.value})}} />
                    <hr />
                    <div className="mb-1 d-flex gap-1 align-items-center">
                        <select className="form-select" value={formData.alamat.provinsi} onChange={(e)=>{setFormData({...formData, alamat: {...formData.alamat, provinsi: e.target.value}})}}>
                            <option value="">-- Pilih provinsi --</option>
                            <?php foreach ($provinsi as $p) { ?>
                                <option value="<?= $p['province_id']; ?>-<?= $p['province']; ?>">
                                    <?= $p['province']; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <select className="form-select" value={formData.alamat.kabupaten} onChange={(e)=>{setFormData({...formData, alamat: {...formData.alamat, kabupaten: e.target.value}})}}>
                            <option value="">-- Pilih kabupaten --</option>
                            {kabupaten.map((k, ind_k)=>(
                                <option key={ind_k} value={`${k.city_id}-${k.city_name}`}>{k.city_name}</option>
                            ))}
                        </select>
                    </div>
                    <div className="mb-1 d-flex gap-1 align-items-center">
                        <select className="form-select" value={formData.alamat.kecamatan} onChange={(e)=>{setFormData({...formData, alamat: {...formData.alamat, kecamatan: e.target.value}})}}>
                            <option value="">-- Pilih kecamatan --</option>
                            {kecamatan.map((k, ind_k)=>(
                                <option key={ind_k} value={`${k.subdistrict_id}-${k.subdistrict_name}`}>{k.subdistrict_name}</option>
                            ))}
                        </select>
                        <select className="form-select" value={formData.alamat.kelurahan} onChange={(e)=>{setFormData({...formData, alamat: {...formData.alamat, kelurahan: e.target.value}})}}>
                            <option value="">-- Pilih kelurahan --</option>
                            {kelurahan.map((k, ind_k)=>(
                                <option key={ind_k} value={`${k.DesaKelurahan}-${k.KodePos}`}>{k.DesaKelurahan}</option>
                            ))}
                        </select>
                    </div>
                    <input type="text" className="form-control mb-3" placeholder="Jalan, NO.Rumah, RT/RW," value={formData.alamat.pelengkap} onChange={(e)=>{setFormData({...formData, alamat: {...formData.alamat, pelengkap: e.target.value}})}} />
                    <button type="button" onClick={handleSubmit} className={`btn-default-merah w-100 ${canSave ? '' : 'disabled'}`}>Buat</button>
                </div>
            </>
        )
    }
    ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>