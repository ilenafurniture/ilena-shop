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
        const [modalKeranjang, setModalKeranjang] = useState(false);
        const [keranjang, setKeranjang] = useState([]);
        const [notif, setNotif] = useState({
            show: false,
            teks: 'Stok tidak cukup'
        });
        const [totalHarga, setTotalHarga] = useState(0);
        const [formData, setFormData] = useState({
            provinsi: '',
            kabupaten: '',
            kecamatan: '',
            kelurahan: '',
            kodepos: '',
            detail: '',
            provinsiTagihan: '',
            kabupatenTagihan: '',
            kecamatanTagihan: '',
            kelurahanTagihan: '',
            kodeposTagihan: '',
            detailTagihan: '',
            totalAkhir: 0,
            jenis: 'sale',
            tanggal: '',
            items: [
                // {
                //     id: '',
                //     nama: 'qwdqw',
                //     jumlah: 2,
                //     varian: 'HITAM',
                //     harga: 124000
                // },
            ],
            nama: '',
            nohp: '',
            npwp: '',
            keterangan: '',
            po: '',
        });
        const [kabupaten, setKabupaten] = useState([]);
        const [kecamatan, setKecamatan] = useState([]);
        const [kelurahan, setKelurahan] = useState([]);
        const [kabupatenTagihan, setKabupatenTagihan] = useState([]);
        const [kecamatanTagihan, setKecamatanTagihan] = useState([]);
        const [kelurahanTagihan, setKelurahanTagihan] = useState([]);
        const [canSave, setCanSave] = useState(false);
        const [potongan, setPotongan] = useState({
            nominal: 0,
            satuan: 'persen'
        });
        const [alamatTagihanSama, setAlamatTagihanSama] = useState(false);
        
        useEffect(()=>{
            if(formData.provinsi) {
                const idprov = formData.provinsi.split("-")[0];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkota/" + idprov);
                    const kota = await response.json();
                    const hasil = kota.rajaongkir.results;
                    setKabupaten(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, kabupaten: '', kecamatan: '', kelurahan: '', kodepos: ''})
            setKecamatan([]);
            setKelurahan([]);
        }, [formData.provinsi])

        useEffect(()=>{
            if(formData.kabupaten) {
                const idkab = formData.kabupaten.split("-")[0];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkec/" + idkab);
                    const kota = await response.json();
                    const hasil = kota.rajaongkir.results;
                    setKecamatan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, kecamatan: '', kelurahan: '', kodepos: ''})
            setKelurahan([]);
        }, [formData.kabupaten])

        useEffect(()=>{
            if(formData.kecamatan) {
                const idkec = formData.kecamatan.split("-")[1];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkode/" + idkec);
                    const hasil = await response.json();
                    setKelurahan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, kelurahan: '', kodepos: ''})
        }, [formData.kecamatan])

        useEffect(()=>{
            if(formData.kelurahan){
                setFormData({...formData, kodepos: formData.kelurahan.split('-')[1]})
            }
        },[formData.kelurahan])
        {/* TAGIHAN */}
        useEffect(()=>{
            if(formData.provinsiTagihan) {
                const idprov = formData.provinsiTagihan.split("-")[0];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkota/" + idprov);
                    const kota = await response.json();
                    const hasil = kota.rajaongkir.results;
                    setKabupatenTagihan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, kabupatenTagihan: '', kecamatanTagihan: '', kelurahanTagihan: '', kodeposTagihan: ''})
            setKecamatanTagihan([]);
            setKelurahanTagihan([]);
        }, [formData.provinsiTagihan])

        useEffect(()=>{
            if(formData.kabupatenTagihan) {
                const idkab = formData.kabupatenTagihan.split("-")[0];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkec/" + idkab);
                    const kota = await response.json();
                    const hasil = kota.rajaongkir.results;
                    setKecamatanTagihan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, kecamatanTagihan: '', kelurahanTagihan: '', kodeposTagihan: ''})
            setKelurahanTagihan([]);
        }, [formData.kabupatenTagihan])

        useEffect(()=>{
            if(formData.kecamatanTagihan) {
                const idkec = formData.kecamatanTagihan.split("-")[1];
                async function fetchRajaOngkir() {
                    const response = await fetch("/getkode/" + idkec);
                    const hasil = await response.json();
                    setKelurahanTagihan(hasil)
                }
                fetchRajaOngkir()
            }
            setFormData({...formData, kelurahanTagihan: '', kodeposTagihan: ''})
        }, [formData.kecamatanTagihan])
        
        useEffect(()=>{
            if(formData.kelurahanTagihan){
                setFormData({...formData, kodeposTagihan: formData.kelurahanTagihan.split('-')[1]})
            }
        },[formData.kelurahanTagihan])

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
            const {
                provinsi, kabupaten, kecamatan, kelurahan, kodepos, detail,
                totalAkhir, jenis, tanggal,
                items,
                nama, nohp,
                provinsiTagihan, kabupatenTagihan, kecamatanTagihan, kelurahanTagihan, kodeposTagihan, detailTagihan,
            } = formData;
            console.log('jenis')
            console.log(jenis)
            console.log('alanat tagihan')
            console.log(alamatTagihanSama)
            const isFormValidKhusus = () => {
                if(jenis == 'sale' && !alamatTagihanSama) {
                    const fieldsFilled = [
                        provinsiTagihan, kabupatenTagihan, kecamatanTagihan, kelurahanTagihan, kodeposTagihan, detailTagihan
                    ].every(val => val && val !== '');
                    return fieldsFilled;
                } else return true;
            }
            const isFormValid = () => {
                const fieldsFilled = [
                    provinsi, kabupaten, kecamatan, kelurahan, kodepos, detail,
                    jenis, tanggal, nama, nohp
                ].every(val => val && val !== '');
                const totalValid = totalAkhir > 0;
                const itemsValid = items.length > 0;
                return fieldsFilled && totalValid && itemsValid;
            };
            if(isFormValid(formData) && isFormValidKhusus(jenis)) {
                setCanSave(true)
            } else {
                setCanSave(false)
            }
        }, [formData, alamatTagihanSama])

        useEffect(()=>{
            if(formData.items.length > 0) {
                const hargaTotal = formData.items.map((k) => {
                    return Number(k.harga) * Number(k.jumlah)
                }).reduce((prev, cur) => {
                    return prev + cur;
                }, 0)
                setFormData({...formData, totalAkhir: Math.ceil(hargaTotal - (potongan.satuan == 'rupiah' ? potongan.nominal : potongan.nominal / 100 * hargaTotal))})
            } else {
                setFormData({...formData, totalAkhir: 0})
            }
        }, [formData.items, potongan])

        const handlePilih = (produk, varian) => {
            // const nama = `${produk.nama} ${produk.dimensi.panjang} (${varian.nama})`;
            const stok = Number(varian.stok);
            const cekExistData = formData.items.find((k) => k.id == produk.id && k.varian == varian.nama);
            if(stok == 0) {
                setNotif({teks: 'Stok habis', show: true});
                setTimeout(() => {
                    setNotif({teks: 'Stok habis', show: false});
                }, 3000);
                return;
            }
            if(cekExistData) {
                setFormData({
                    ...formData,
                    items: formData.items.map((item) => {
                        if(item.id == produk.id && item.varian == varian.nama) {
                            return {
                                ...item,
                                jumlah: (item.jumlah + 1) > stok ? item.jumlah : (item.jumlah + 1)
                            }
                        } else return item
                    })
                })
            } else {
                setFormData({
                    ...formData, 
                    items: [
                        ...formData.items,
                        {
                            id: produk.id,
                            nama: produk.nama,
                            jumlah: 1,
                            varian: varian.nama,
                            harga: produk.harga * 75 / 100,
                            detail: {produk, varian}
                        }
                    ]
                })
            }
        }

        const handleCount = (index, max, tambah) =>{
            setFormData({
                ...formData,
                items: formData.items.map((item, ind_i) => {
                    if(index == ind_i) {
                        return {
                            ...item,
                            jumlah: (item.jumlah + tambah) > max ? item.jumlah : (item.jumlah + tambah)
                        }
                    } else return item
                }).filter((item) => item.jumlah > 0)
            });
        }

        const handleSubmit = () => {
            const formDataAkhir = {
                ...formData,
                provinsi: formData.provinsi.split("-")[1],
                kabupaten: formData.kabupaten.split("-")[1],
                kecamatan: formData.kecamatan.split("-")[1],
                kelurahan: formData.kelurahan.split("-")[0],
                provinsiTagihan: alamatTagihanSama ? formData.provinsi.split("-")[1] : formData.provinsiTagihan.split("-")[1],
                kabupatenTagihan: alamatTagihanSama ? formData.kabupaten.split("-")[1] : formData.kabupatenTagihan.split("-")[1],
                kecamatanTagihan: alamatTagihanSama ? formData.kecamatan.split("-")[1] : formData.kecamatanTagihan.split("-")[1],
                kelurahanTagihan: alamatTagihanSama ? formData.kelurahan.split("-")[0] : formData.kelurahanTagihan.split("-")[0],
                kodeposTagihan: alamatTagihanSama ? formData.kodepos : formData.kodeposTagihan,
                detailTagihan: alamatTagihanSama ? formData.detail : formData.detailTagihan,
                items: formData.items.map((item) => {
                    return {
                        id: item.id,
                        nama: item.nama,
                        varian: item.varian,
                        jumlah: item.jumlah,
                        harga: item.harga,
                    }
                })
            }
            const formDataAkhir1 = {
                ...formDataAkhir,
                provinsiTagihan: formDataAkhir.jenis == 'display' ? null : formDataAkhir.provinsiTagihan,
                kabupatenTagihan: formDataAkhir.jenis == 'display' ? null : formDataAkhir.kabupatenTagihan,
                kecamatanTagihan: formDataAkhir.jenis == 'display' ? null : formDataAkhir.kecamatanTagihan,
                kelurahanTagihan: formDataAkhir.jenis == 'display' ? null : formDataAkhir.kelurahanTagihan,
                kodeposTagihan: formDataAkhir.jenis == 'display' ? null : formDataAkhir.kodeposTagihan,
                detailTagihan: formDataAkhir.jenis == 'display' ? null : formDataAkhir.detailTagihan,
                npwp: formDataAkhir.jenis == 'display' ? null : formDataAkhir.npwp,
            }

            console.log(formDataAkhir1)

            async function fetchSubmit() {
                const response = await fetch(
                    `/admin/order-offline/add`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify(formDataAkhir1),
                    }
                );
                const responseJson = await response.json()
                console.log(responseJson)
                if(response.status == 200) {
                    window.alert('Berhasil menambahkan pesanan')
                    window.location.href = `/admin/order/offline/${formData.jenis}`
                }else {
                    window.alert('Gagal menambahkan pesanan')
                    console.log(responseJson)
                }
            }
            fetchSubmit()
        }
        return (
            <>
                
                {modalKeranjang && 
                    <div className="d-flex justify-content-center align-items-center" style={{position: 'fixed', top: 0, right: 0, width: '100vw', height: '100svh', backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 9}}>
                        <div className="bg-white p-4 d-flex flex-column" style={{width: '80%', height: '80%', borderRadius: '10px'}}>
                            <div className="d-flex justify-content-end">
                                <button className="btn-close" onClick={()=>{setModalKeranjang(false)}}><i className="material-icons">close</i></button>
                            </div>
                            <h3 className="text-center">Keranjang</h3>
                            <hr />
                            <div className="d-flex gap-2" style={{flex: 1}}>
                            <div style={{flex: 1}} className="d-flex flex-column">
                                    <div className="d-flex gap-2">
                                        <div style={{flex: 1}}>
                                            <input type="text" onChange={(e)=>{setCari(e.target.value)}} className="form-control mb-3" placeholder="Cari barang" />
                                        </div>
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
                                <div style={{flex: 1, position: 'relative'}}>
                                    <div className="w-100 h-100" style={{position: 'absolute', overflow: 'auto'}}>
                                        <div className="d-flex flex-column gap-2">
                                            {formData.items.length > 0 ?
                                                <>
                                                {formData.items.map((i, ind_i) => (
                                                    <div key={ind_i} className="item-keranjang-admin w-100">
                                                        <div className="d-flex gap-2 align-items-center w-100">
                                                            <img src={i.detail.produk.gambar} alt={i.nama} />
                                                            <div style={{flex: 1}}>
                                                                <p className="m-0 fw-bold" style={{textWrap: 'nowrap'}}>{i.nama}</p>
                                                                <p className="m-0" style={{fontSize: '12px'}}><b>{i.detail.produk.dimensi.panjang}</b> | {i.varian}</p>
                                                            </div>
                                                            <div className="counter">
                                                                <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), -1)}}>-</div>
                                                                <div className="angka">{i.jumlah}</div>
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
                            </div>
                        </div>
                    </div>
                }
                <div className={`notif ${notif.show ? 'show' : ''}`}>{notif.teks}</div>
                <div className="d-flex gap-4 w-100" style={{ height: 'fit-content' }}>
                    <div className="d-flex flex-column gap-4" style={{flex: 1}}>
                        <div style={{ width: '100%', flex: 1, position: 'relative', minHeight: '400px'}} className="mb-1">
                            <div style={{position: 'absolute', border: '1px solid rgb(167, 8, 8)', padding: '1em', borderRadius: '5px'}} className="w-100 h-100 d-flex flex-column">
                                <div className="d-flex gap-1 justify-content-between">
                                    <div className="d-flex flex-column">
                                        <p className="fw-bold m-0" style={{fontSize: '14px',}}>Keranjang kamu</p>
                                        <p className="m-0" style={{fontSize: '14px',letterSpacing: '-1px'}}>{formData.items.length} Produk</p>
                                    </div>
                                    {formData.items.length > 0 ?
                                        <a onClick={()=>{setModalKeranjang(true)}} style={{cursor: 'pointer', color: 'rgb(167, 8, 8)', fontSize: '40px', fontWeight: 'bold', display: 'flex', alignItems: 'center',justifyContent: 'center',  textDecoration: 'none', lineHeight: '0px'}}><i className="material-icons">edit</i></a>
                                        :
                                        <a onClick={()=>{setModalKeranjang(true)}} style={{cursor: 'pointer', color: 'rgb(167, 8, 8)', fontSize: '40px', fontWeight: 'bold', display: 'flex', alignItems: 'center',justifyContent: 'center',  textDecoration: 'none', lineHeight: '0px'}}><i className="material-icons">add_shopping_cart</i></a>
                                    }
                                </div>
                                <hr/>
                                <div className="d-flex flex-column gap-1 w-100" style={{ overflow: 'auto', flex: 1 }}>
                                    {formData.items.length > 0 ?
                                        <>
                                        {formData.items.map((i, ind_i) => (
                                            <div key={ind_i} className="item-keranjang-admin w-100">
                                                <div className="d-flex gap-2 align-items-center w-100">
                                                    <img src={i.detail.produk.gambar} alt={i.nama} />
                                                    <div style={{flex: 1}}>
                                                        <p className="m-0 fw-bold" style={{textWrap: 'nowrap'}}>{i.nama}</p>
                                                        <p className="m-0" style={{fontSize: '12px'}}><b>{i.detail.produk.dimensi.panjang}</b> | {i.varian}</p>
                                                    </div>
                                                    <div className="counter">
                                                        <div className="action" onClick={()=>{handleCount(ind_i, Number(i.detail.varian.stok), -1)}}>-</div>
                                                        <div className="angka">{i.jumlah}</div>
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
                                <hr />
                                <div className="d-flex justify-content-between">
                                    <div className="d-flex flex-column">
                                        <p className="m-0" style={{fontSize: '14px',letterSpacing: '-1px'}}><b>Potongan</b></p>
                                        <p className="m-0" style={{fontSize: '14px',letterSpacing: '-1px'}}><b>Total Biaya</b></p>
                                    </div>
                                    <div className="d-flex flex-column align-items-end">    
                                        <p className="m-0" style={{fontSize: '14px',letterSpacing: '-1px'}}><b>{potongan.satuan == 'rupiah' ? `Rp ${potongan.nominal.toLocaleString('id-ID')}` : `${potongan.nominal}%`}</b></p>
                                        <p className="m-0" style={{fontSize: '14px',letterSpacing: '-1px'}}><b>Rp. {formData.totalAkhir.toLocaleString('id-ID')}</b></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="d-flex flex-column gap-1">
                            <p className="m-0" style={{letterSpacing: '-1px',fontSize: '16px'}}>Detail :</p>
                            <div className="d-flex gap-1 mb-1">
                                <input type="text w-50" className="form-control" placeholder="Nama penerima" value={formData.nama} onChange={(e)=>{setFormData({...formData, nama: e.target.value})}} />
                                <input type="text w-50" className="form-control" placeholder="Nohp penerima" value={formData.nohp} onChange={(e)=>{setFormData({...formData, nohp: e.target.value})}} />
                            </div>
                            <div className="d-flex gap-1 mb-1">
                                {formData.jenis == 'sale' &&
                                    <input type="text" className="w-100 form-control" placeholder="No NPWP" value={formData.npwp} onChange={(e)=>{setFormData({...formData, npwp: e.target.value})}} />
                                }
                                <input type="datetime-local" placeholder="Tanggal" className="w-100 form-control" value={formData.tanggal} onChange={(e)=>{setFormData({...formData, tanggal: e.target.value})}} />
                            </div>
                            <div className="d-flex gap-1 mb-1">
                                <input type="text w-50" className="form-control" placeholder="Keterangan (opt)" value={formData.keterangan} onChange={(e)=>{setFormData({...formData, keterangan: e.target.value})}} />
                                <input type="text w-50" className="form-control" placeholder="PO (opt)" value={formData.po} onChange={(e)=>{setFormData({...formData, po: e.target.value})}} />
                            </div>
                            <select value={formData.jenis} onChange={(e)=>{setFormData({...formData, jenis: e.target.value})}} className="form-select mb-1">
                                <option value="sale">Sale</option>
                                <option value="display">Display</option>
                            </select>
                        </div>
                    </div>
                    <div className="d-flex flex-column" style={{flex: 1}}>
                        {/* <Form Pengiriman> */}
                        <div className={"d-flex flex-column gap-1"}>
                            <p className="m-0" style={{letterSpacing: '-1px',fontSize: '16px'}}>Alamat Pengiriman</p>
                            <div className="mb-1 d-flex gap-1 align-items-center">
                                    <select className="form-select" value={formData.provinsi} onChange={(e)=>{setFormData({...formData, provinsi: e.target.value})}}>
                                        <option value="">-- Pilih provinsi --</option>
                                        <?php foreach ($provinsi as $p) { ?>
                                            <option value="<?= $p['province_id']; ?>-<?= $p['province']; ?>">
                                                <?= $p['province']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <select className="form-select" value={formData.kabupaten} onChange={(e)=>{setFormData({...formData,kabupaten: e.target.value})}}>
                                        <option value="">-- Pilih kabupaten --</option>
                                        {kabupaten.map((k, ind_k)=>(
                                            <option key={ind_k} value={`${k.city_id}-${k.city_name}`}>{k.city_name}</option>
                                        ))}
                                    </select>
                                </div>
                            <div className="mb-1 d-flex gap-1 align-items-center">
                                <select className="form-select" value={formData.kecamatan} onChange={(e)=>{setFormData({...formData, kecamatan: e.target.value})}}>
                                    <option value="">-- Pilih kecamatan --</option>
                                    {kecamatan.map((k, ind_k)=>(
                                        <option key={ind_k} value={`${k.subdistrict_id}-${k.subdistrict_name}`}>{k.subdistrict_name}</option>
                                    ))}
                                </select>
                                <select className="form-select" value={formData.kelurahan} onChange={(e)=>{setFormData({...formData,kelurahan: e.target.value})}}>
                                    <option value="">-- Pilih kelurahan --</option>
                                    {kelurahan.map((k, ind_k)=>(
                                        <option key={ind_k} value={`${k.DesaKelurahan}-${k.KodePos}`}>{k.DesaKelurahan}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="mb-1 d-flex gap-1 align-items-center">
                                <input type="text" className="form-control" placeholder="Kodepos" value={formData.kodepos} onChange={(e)=>{setFormData({...formData, kodepos: e.target.value})}} />
                            </div>
                            <input type="text" className="form-control mb-1" placeholder="Jalan, NO.Rumah, RT/RW," value={formData.detail} onChange={(e)=>{setFormData({...formData, detail: e.target.value})}} />
                        </div>
                        {/* <Form Tagihan> */}
                        {formData.jenis == 'sale' &&
                            <div className={"d-flex flex-column gap-1 my-4"}>
                                {!alamatTagihanSama && <>
                                    <p className="m-0" style={{letterSpacing: '-1px',fontSize: '16px'}}>Alamat Tagihan</p>
                                    <div className="mb-1 d-flex gap-1 align-items-center">
                                            <select className="form-select" value={formData.provinsiTagihan} onChange={(e)=>{setFormData({...formData, provinsiTagihan: e.target.value})}}>
                                                <option value="">-- Pilih provinsi --</option>
                                                <?php foreach ($provinsi as $p) { ?>
                                                    <option value="<?= $p['province_id']; ?>-<?= $p['province']; ?>">
                                                        <?= $p['province']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <select className="form-select" value={formData.kabupatenTagihan} onChange={(e)=>{setFormData({...formData,kabupatenTagihan: e.target.value})}}>
                                                <option value="">-- Pilih kabupaten --</option>
                                                {kabupatenTagihan.map((k, ind_k)=>(
                                                    <option key={ind_k} value={`${k.city_id}-${k.city_name}`}>{k.city_name}</option>
                                                ))}
                                            </select>
                                        </div>
                                    <div className="mb-1 d-flex gap-1 align-items-center">
                                        <select className="form-select" value={formData.kecamatanTagihan} onChange={(e)=>{setFormData({...formData, kecamatanTagihan: e.target.value})}}>
                                            <option value="">-- Pilih kecamatan --</option>
                                            {kecamatanTagihan.map((k, ind_k)=>(
                                                <option key={ind_k} value={`${k.subdistrict_id}-${k.subdistrict_name}`}>{k.subdistrict_name}</option>
                                            ))}
                                        </select>
                                        <select className="form-select" value={formData.kelurahanTagihan} onChange={(e)=>{setFormData({...formData,kelurahanTagihan: e.target.value})}}>
                                            <option value="">-- Pilih kelurahan --</option>
                                            {kelurahanTagihan.map((k, ind_k)=>(
                                                <option key={ind_k} value={`${k.DesaKelurahan}-${k.KodePos}`}>{k.DesaKelurahan}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="mb-1 d-flex gap-1 align-items-center">
                                        <input type="text" className="form-control" placeholder="Kodepos" value={formData.kodeposTagihan} onChange={(e)=>{setFormData({...formData, kodeposTagihan: e.target.value})}} />
                                    </div>
                                    <input type="text" className="form-control mb-1" placeholder="Jalan, NO.Rumah, RT/RW," value={formData.detailTagihan} onChange={(e)=>{setFormData({...formData, detailTagihan: e.target.value})}} />
                                </>}
                                <label className="d-flex gap-2 align-items-center">
                                    <input type="checkbox" onChange={(e) => setAlamatTagihanSama(e.target.checked)} checked={alamatTagihanSama} />
                                    <p className="m-0">Samakan alamat pengiriman</p>
                                </label>
                                <hr />
                                <p className="m-0" style={{letterSpacing: '-1px',fontSize: '16px'}}>Potongan</p>
                                <div className="d-flex gap-1 mb-1">
                                    <div style={{flex: 1}}>
                                        <input type="number" value={potongan.nominal} onChange={(e)=>{setPotongan({...potongan, nominal: Number(e.target.value)})}} className="form-control mb-1" placeholder="Nominal"/>
                                    </div>
                                    <div style={{flex: 1}}>
                                        <select className="form-select" value={potongan.satuan} onChange={(e)=>{setPotongan({...potongan, satuan: e.target.value})}}>
                                            <option value="rupiah">Rupiah</option>
                                            <option value="persen">Persen</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        }

                        <button type="button" onClick={handleSubmit} className={`btn-default-merah w-100 ${canSave ? '' : 'disabled'}`}>Buat</button>
                    </div>
                </div>
            </>
        )
    }
    ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>