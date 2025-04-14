<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<div style="padding: 2em;">
    <div id="container-react"></div>
</div>
<script type="text/babel">
    const { useState, useEffect, useRef } = React;
    const koleksi = JSON.parse('<?= $koleksiJson; ?>')
    const jenis = JSON.parse('<?= $jenisJson; ?>')
    const App = () => {
        const [formData, setFormData] = useState({
            id: '',
            nama: '',
            harga: '',
            deskripsi: {
                deskripsi: '',
                dimensi: {
                    asli: {
                        panjang: "",
                        lebar: "",
                        tinggi: "",
                        berat: ""
                    },
                    paket: {
                        panjang: "",
                        lebar: "",
                        tinggi: "",
                        berat: ""
                    }
                },
                perawatan: ''
            },
            kategori: '',
            subkategori: '',
            diskon: '',
            varian: [
                // {
                //     nama: "HITAM",
                //     kode: "#000000",
                //     stok: "2",
                //     urutan_gambar: "1,2,3,4"
                // }
            ],
            shopee: '',
            tokped: '',
            tiktok: '',
            ruang_tamu: false,
            ruang_keluarga: false,
            ruang_tidur: false
        });

        useEffect(()=>{
            console.log(formData)
        }, [formData])

        useEffect(() => {
            const name = event.target.name;
            const value = event.target.value;
            const idInputElm = document.querySelector('input[name="id"]');
            let idStrArr = idStr.split("-");
            switch (name) {
                case 'kategori':
                    idStrArr[1] = value.toString().padStart(2, '0');
                    idStr = idStrArr.join("-");
                    idInputElm.value = idStrArr.join("")
                    break;
                case 'subkategori':
                    idStrArr[2] = value.toString().padStart(3, '0');
                    idStr = idStrArr.join("-");
                    idInputElm.value = idStrArr.join("")
                    break;
            }
        }, [formData.kategori, formData.subkategori])

        return (
            <>
                <h1 className="teks-sedang mb-3">Tambah Produk</h1>
                <div className="pemberitahuan my-1">
                    ini alert
                </div>
                <form>
                    <div className="baris-ke-kolom">
                        <div className="limapuluh-ke-seratus">
                            <table className="table-input w-100">
                                <tbody>
                                    <tr>
                                        <td>Nama Produk</td>
                                        <td>
                                            <div className="baris">
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    value={formData.nama}
                                                    onChange={(e) => {setFormData({...formData, nama: e.target.value})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Harga Produk</td>
                                        <td>
                                            <div className="baris">
                                                <input
                                                    type="number"
                                                    className="form-control"
                                                    value={formData.harga}
                                                    onChange={(e) => {setFormData({...formData, harga: e.target.value})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Diskon Produk</td>
                                        <td>
                                            <div className="baris">
                                                <div className="input-group">
                                                    <input
                                                        type="number"
                                                        className="form-control"
                                                        step="any"
                                                        value={formData.diskon}
                                                        onChange={(e) => {setFormData({...formData, diskon: e.target.value})}}
                                                    />
                                                    <span className="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Koleksi</td>
                                        <td>
                                            <div className="baris">
                                                <select 
                                                    className="form-select" 
                                                    value={formData.kategori}
                                                    onChange={(e) => {setFormData({...formData, kategori: e.target.value})}}
                                                >
                                                    <option value="">-- Pilih koleksi --</option>
                                                    {koleksi.map((k, ind_k) => (
                                                        <option key={ind_k} value={k.id}>{k.nama}</option>
                                                    ))}
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Jenis</td>
                                        <td>
                                        <div className="baris">
                                                <select 
                                                    className="form-select" 
                                                    value={formData.subkategori}
                                                    onChange={(e) => {setFormData({...formData, subkategori: e.target.value})}}
                                                >
                                                    <option value="">-- Pilih jenis --</option>
                                                    {jenis.map((j, ind_j) => (
                                                        <option key={ind_j} value={j.id}>{j.nama}</option>
                                                    ))}
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>ID Produk</td>
                                        <td>
                                            <div className="baris">
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    name="id"
                                                    required=""
                                                    placeholder="akan tergenerate"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Link Shopee</td>
                                        <td>
                                            <div className="baris">
                                                <input 
                                                    type="text" 
                                                    className="form-control"
                                                    value={formData.shopee}
                                                    onChange={(e) => {setFormData({...formData, shopee: e.target.value})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Link Tokopedia</td>
                                        <td>
                                            <div className="baris">
                                                <input 
                                                    type="text" 
                                                    className="form-control"
                                                    value={formData.tokped}
                                                    onChange={(e) => {setFormData({...formData, tokped: e.target.value})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Link Tiktok</td>
                                        <td>
                                            <div className="baris">
                                                <input 
                                                    type="text" 
                                                    className="form-control"
                                                    value={formData.tiktok}
                                                    onChange={(e) => {setFormData({...formData, tiktok: e.target.value})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Deskripsi</td>
                                        <td>
                                            <div className="baris">
                                                <textarea
                                                    type="text"
                                                    className="form-control"
                                                    value={formData.deskripsi.deskripsi}
                                                    onChange={(e) => {setFormData({...formData, deskripsi: {
                                                        ...formData.deskripsi,
                                                        deskripsi: e.target.value
                                                    }})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Spesifikasi Produk</td>
                                        <td>
                                            <div className="baris">
                                                <textarea
                                                    type="text"
                                                    className="form-control"
                                                    name="perawatan"
                                                    value={formData.deskripsi.perawatan}
                                                    onChange={(e) => {setFormData({...formData, deskripsi: {
                                                        ...formData.deskripsi,
                                                        perawatan: e.target.value
                                                    }})}}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ruangan</td>
                                        <td>
                                            <div className="baris">
                                                <div>
                                                    <input 
                                                        type="checkbox" 
                                                        id="check_tamu"
                                                        checked={formData.ruang_tamu}
                                                        onChange={(e) => {setFormData({...formData, ruang_tamu: e.target.checked})}}
                                                    />
                                                    <label htmlFor="check_tamu">Ruang Tamu</label>
                                                </div>
                                                <div>
                                                    <input
                                                        type="checkbox"
                                                        id="check_keluarga"
                                                        checked={formData.ruang_keluarga}
                                                        onChange={(e) => {setFormData({...formData, ruang_keluarga: e.target.checked})}}
                                                    />
                                                    <label htmlFor="check_keluarga">Ruang Keluarga</label>
                                                </div>
                                                <div>
                                                    <input
                                                        type="checkbox"
                                                        id="check_tidur"
                                                        checked={formData.ruang_tidur}
                                                        onChange={(e) => {setFormData({...formData, ruang_tidur: e.target.checked})}}
                                                    />
                                                    <label htmlFor="check_tidur">Ruang Tidur</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5 className="mt-4">Bentuk Paket</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dimensi Panjang (cm)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="text"
                                                className="form-control"
                                                value={formData.deskripsi.dimensi.paket.panjang}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                paket: {
                                                                    ...formData.deskripsi.dimensi.paket,
                                                                    panjang: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dimensi Lebar (cm)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="text"
                                                className="form-control"
                                                value={formData.deskripsi.dimensi.paket.lebar}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                paket: {
                                                                    ...formData.deskripsi.dimensi.paket,
                                                                    lebar: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dimensi Tinggi (cm)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="text"
                                                className="form-control"
                                                value={formData.deskripsi.dimensi.paket.tinggi}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                paket: {
                                                                    ...formData.deskripsi.dimensi.paket,
                                                                    tinggi: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Berat (kg)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="number"
                                                className="form-control"
                                                step="any"
                                                value={formData.deskripsi.dimensi.paket.berat}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                paket: {
                                                                    ...formData.deskripsi.dimensi.paket,
                                                                    berat: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h5 className="mt-4">Bentuk Asli</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dimensi Panjang (cm)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="text"
                                                className="form-control"
                                                value={formData.deskripsi.dimensi.asli.panjang}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                asli: {
                                                                    ...formData.deskripsi.dimensi.asli,
                                                                    panjang: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dimensi Lebar (cm)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="text"
                                                className="form-control"
                                                value={formData.deskripsi.dimensi.asli.lebar}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                asli: {
                                                                    ...formData.deskripsi.dimensi.asli,
                                                                    lebar: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dimensi Tinggi (cm)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="text"
                                                className="form-control"
                                                value={formData.deskripsi.dimensi.asli.tinggi}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                asli: {
                                                                    ...formData.deskripsi.dimensi.asli,
                                                                    tinggi: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Berat (kg)</td>
                                        <td>
                                            <div className="baris">
                                            <input
                                                type="number"
                                                className="form-control"
                                                step="any"
                                                value={formData.deskripsi.dimensi.asli.berat}
                                                onChange={(e) => {setFormData(
                                                    {
                                                        ...formData, 
                                                        deskripsi: {
                                                            ...formData.deskripsi,
                                                            dimensi: {
                                                                ...formData.deskripsi.dimensi,
                                                                asli: {
                                                                    ...formData.deskripsi.dimensi.asli,
                                                                    berat: e.target.value
                                                                }
                                                            }
                                                        }
                                                    }
                                                )}}
                                            />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div className=" show-flex-ke-hide mt-4 justify-content-center gap-2">
                                <button className="btn-default" type="submit">
                                    Simpan
                                </button>
                            </div>
                        </div>
                        <div className="limapuluh-ke-seratus">
                            <h5 className="jdl=section">Gambar Hover</h5>
                            <img
                                id="imghover-preview"
                                src="/img/nopic.jpg"
                                alt=""
                                className="limapuluh-ke-seratus mb-1"
                                style={{ aspectRatio: "1 / 1", objectFit: "cover" }}
                            />
                            <div className="mb-2">
                                <input
                                    name="gambar_hover"
                                    type="file"
                                    className="form-control"
                                    onchange="uploadFileGambarHover(event)"
                                />
                            </div>
                            <h5 className="jdl-section">Varian</h5>
                            <div id="container-varian">
                                <div className="item-varian">
                                    <div className="container-gambar" id="container-gambar1">
                                        <div id="container-input-gambar1">
                                            <div>
                                            <input
                                                type="file"
                                                id="input-gambar-1-1"
                                                name="gambar-1-1"
                                                style={{ display: "none" }}
                                                onchange="uploadFile(event)"
                                            />
                                            <label htmlFor="input-gambar-1-1" className="btn-default">
                                                +
                                            </label>
                                            </div>
                                        </div>
                                    </div>
                                    <table className="table-input w-100 mt-2">
                                        <tbody>
                                            <tr>
                                                <td>Nama</td>
                                                <td>
                                                    <div className="baris">
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            name="nama-var1"
                                                            required=""
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Kode Warna</td>
                                                <td>
                                                    <div className="baris">
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            name="kode-var1"
                                                            required=""
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Stok</td>
                                                <td>
                                                    <div className="baris">
                                                        <input
                                                            type="text"
                                                            className="form-control"
                                                            name="stok-var1"
                                                            required=""
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button
                                        type="button"
                                        className="btn-teks-aja m-0 ms-auto mt-1"
                                    >Hapus</button>
                                </div>
                            </div>
                            <button
                                className="btn-default-merah mt-2"
                                type="button"
                            >
                                Tambah Varian
                            </button>
                        </div>
                    </div>
                    <div className="hide-ke-show-flex justify-content-center mt-3">
                        <button className="btn-default" type="submit">
                            Simpan
                        </button>
                    </div>
                    <input
                        type="text"
                        name="hitung-varian"
                        style={{ display: "none" }}
                        defaultValue={1}
                    />
                </form>
            </>
        );
    }
    ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>