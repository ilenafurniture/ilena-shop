<?= $this->extend("admin/template"); ?>
<?= $this->section("content"); ?>
<style>
.container-gambar-add-product {
    display: flex;
    flex-wrap: wrap;
}
</style>
<div style="padding: 2em;">
    <div id="container-react"></div>
</div>
<script type="text/babel">
    const { useState, useEffect, useRef } = React;
    const koleksi = JSON.parse('<?= $koleksiJson; ?>')
    const jenis = JSON.parse('<?= $jenisJson; ?>');
    const idProduct = '<?= isset($idProduct) ? $idProduct : ''; ?>'
    const App = () => {
        const firstRender = useRef(true);
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
                //     urutan_gambar: "1,2"
                // }qwdqw
            ],
            shopee: '',
            tokped: '',
            tiktok: '',
            ruang_tamu: false,
            ruang_keluarga: false,
            ruang_tidur: false
        });
        const [eror, setEror] = useState('');
        const [hoverSrc, setHoverSrc] = useState(null);
        const [hoverFile, setHoverFile] = useState(null);
        const [gambarSrc, setGambarSrc] = useState([]);
        const [gambarFile, setGambarFile] = useState([]);
        const idStr = useRef("1-00-000-XX");

        useEffect(() => {
            if(idProduct) {
                const currentProduct = JSON.parse('<?= isset($produkJson) ? $produkJson : ''; ?>');
                console.log(currentProduct);
                setHoverSrc('<?= base_url('img/barang/hover/' . ($produk ? $produk['id'] : '') . '.webp') ?>');
                console.log(currentProduct.varian.reduce((prev,cur) => {
                    return prev.concat(cur.urutan_gambar.split(',').map((item) => {
                        return `<?= base_url('img/barang/1000/' . ($produk ? $produk['id'] : '') . '-') ?>${item}.webp`;
                    }));
                },[]));
                
                console.log("FORM DATA ANJAYYY");
                console.log({
                    ...formData,
                    nama: currentProduct.nama,
                    harga: currentProduct.harga,
                    deskripsi: currentProduct.deskripsi,
                    kategori: currentProduct.kategori,
                    subkategori: currentProduct.subkategori,
                    diskon: currentProduct.diskon,
                    varian: currentProduct.varian,
                    shopee: currentProduct.shopee,
                    tokped: currentProduct.tokped,
                    tiktok: currentProduct.tiktok
                });
            }
        }, [])

        useEffect(()=>{
            console.log("FORM DATA")
            console.log(formData)
        }, [formData])

        useEffect(() => {
            let idStrArr = idStr.current.split("-");
            idStrArr[1] = formData.kategori.toString().padStart(2, '0');
            idStr.current = idStrArr.join("-");
            setFormData({
                ...formData,
                id: idStrArr.join("")
            })
        }, [formData.kategori])
        useEffect(() => {
            let idStrArr = idStr.current.split("-");
            idStrArr[2] = formData.subkategori.toString().padStart(3, '0');
            idStr.current = idStrArr.join("-");
            setFormData({
                ...formData,
                id: idStrArr.join("")
            })
        }, [formData.subkategori])

        useEffect(() => {
            if(firstRender.current) {
                if(idProduct) {
                    const currentProduct = JSON.parse('<?= isset($produkJson) ? $produkJson : ''; ?>');
                    
                    setFormData({
                        ...formData,
                        nama: currentProduct.nama,
                        harga: currentProduct.harga,
                        deskripsi: currentProduct.deskripsi,
                        kategori: currentProduct.kategori,
                        subkategori: currentProduct.subkategori,
                        diskon: currentProduct.diskon,
                        varian: currentProduct.varian,
                        shopee: currentProduct.shopee,
                        tokped: currentProduct.tokped,
                        tiktok: currentProduct.tiktok
                    }); 
                    setGambarSrc(currentProduct.varian.map((v) => {
                        return v.urutan_gambar.split(',').map((item) => {
                            return `<?= base_url('img/barang/1000/' . ($produk ? $produk['id'] : '') . '-') ?>${item}.webp`;
                        });
                    },[]));
                }
                firstRender.current = false;
                return
            }
            setFormData({
                ...formData,
                varian: formData.varian.map((v, ind_v) => {
                    return {
                        ...v,
                        urutan_gambar: ind_v == 0 ? 
                            gambarSrc[ind_v].reduce((prev, curr, index) => {
                                return `${prev}${index > 0 ? ',' : ''}${index + 1}`
                            }, '')
                            :
                            gambarSrc[ind_v].reduce((prev, curr, index) => {
                                return `${prev}${index > 0 ? ',' : ''}${index + 1 +  + Number(formData.varian[ind_v - 1].urutan_gambar.split(',')[formData.varian[ind_v - 1].urutan_gambar.split(',').length - 1])}`
                            }, '')
                    }
                })
            }) 
            
        }, [gambarSrc])

        const handleSubmit = () => {
            const form = new FormData();
            const keyDisallow = ['id','kategori','subkategori'];
            for (const key in formData) {
                if(idProduct) {
                    if(!keyDisallow.includes(key)) {
                        if (typeof formData[key] == 'string') {
                            form.append(key, formData[key]);
                        } else if (typeof formData[key] == 'boolean') {
                            form.append(key, formData[key] ? '1' : '0');
                        } else {
                            form.append(key, JSON.stringify(formData[key]));
                        }
                    }
                } else {
                    if (typeof formData[key] == 'string') {
                            form.append(key, formData[key]);
                        } else if (typeof formData[key] == 'boolean') {
                            form.append(key, formData[key] ? '1' : '0');
                        } else {
                            form.append(key, JSON.stringify(formData[key]));
                        }
                }
            }
            if(hoverFile) {
                form.append('gambar_hover', hoverFile);
            }
            const gambarFileFix = gambarFile.reduce((prev, curr) => {
                return prev.concat(curr);
            }, [])
            console.log(gambarFileFix);
            gambarFileFix.forEach((item, ind_i) => {
                form.append(`gambar_${ind_i}`, item);
            });

            
            (async () => {
                const responseFetch = await fetch(
                    `<?= base_url(); ?>/admin/product${
                        idProduct ? `/${idProduct}` : ""
                    }`,
                    {
                        method: idProduct ? "put" : "post",
                        body: form,
                    }
                );
                console.log(responseFetch)
                const productJson = await responseFetch.json();
                console.log(productJson);
                // if (responseFetch.status != 200) {
                //     return setEror(productJson.pesan);
                // }
            })();
        }

        return (
            <>
                <h1 className="teks-sedang mb-3">Tambah Produk</h1>
                <div className="pemberitahuan my-1">
                    ini alert
                </div>
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
                                                required
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
                                                required
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
                                                    required
                                                />
                                                <span className="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                {!idProduct &&
                                <>
                                    <tr>
                                        <td>Koleksi</td>
                                        <td>
                                            <div className="baris">
                                                <select 
                                                    className="form-select" 
                                                    value={formData.kategori}
                                                    onChange={(e) => {setFormData({...formData, kategori: e.target.value})}}
                                                    required
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
                                                    required
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
                                                    value={formData.id}
                                                    onChange={(e) => {setFormData({...formData, id: e.target.value})}}
                                                    required
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </>
                                }
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
                                                required
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
                                                required
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
                                            required
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
                                            required
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
                                            required
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
                                            required
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
                                            required
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
                                            required
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
                                            required
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
                                            required
                                        />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div className=" show-flex-ke-hide mt-4 justify-content-center gap-2">
                            <button className="btn-default" onClick={() => {handleSubmit()}}>
                                Simpan
                            </button>
                        </div>
                    </div>
                    <div className="limapuluh-ke-seratus">
                        <h5 className="jdl=section">Gambar Hover</h5>
                        <img
                            id="imghover-preview"
                            src={hoverSrc ? hoverSrc : "/img/nopic.jpg"}
                            alt=""
                            className="limapuluh-ke-seratus mb-1"
                            style={{ aspectRatio: "1 / 1", objectFit: "cover" }}
                        />
                        <div className="mb-2">
                            <input
                                onChange={(e) => {
                                    const file = e.target.files[0];
                                    if(file) {
                                        setHoverFile(e.target.files[0]);
                                        const reader = new FileReader();
                                        reader.onload = () => {
                                            setHoverSrc(reader.result);
                                        };
                                        reader.readAsDataURL(file);
                                    } else {
                                        setHoverFile(null)
                                    }
                                }}
                                name="gambar_hover"
                                type="file"
                                className="form-control"
                                onchange="uploadFileGambarHover(event)"
                            />
                        </div>
                        <h5 className="jdl-section">Varian</h5>
                        <div id="container-varian">
                            {formData.varian.map((v, ind_v) => (
                                <div key={ind_v}>
                                    <div className="container-gambar">
                                        {gambarSrc.length > 0 && gambarSrc[ind_v].map((g, ind_g) => (
                                            <div className="item-gambar" key={ind_g} onClick={() => {
                                                setGambarSrc(gambarSrc.map((gg, ind_gg) => {
                                                    if(ind_gg == ind_v) {
                                                        return gg.filter((ggg, ind_ggg) => ind_ggg != ind_g);
                                                    } else return gg;
                                                }))
                                                setGambarFile(gambarFile.map((gg, ind_gg) => {
                                                    if(ind_gg == ind_v) {
                                                        return gg.filter((ggg, ind_ggg) => ind_ggg != ind_g);
                                                    } else return gg;
                                                }))
                                            }}>
                                                <p>X</p>
                                                <img src={g ? g : "/img/nopic.jpg"} alt="" />
                                            </div>
                                        ))}
                                        <div>
                                            <input
                                                type="file"
                                                id={ind_v}
                                                style={{ display: "none" }}
                                                onChange={(e) => {
                                                    const file = e.target.files[0];
                                                    if(file) {
                                                        const reader = new FileReader();
                                                        reader.onload = () => {
                                                            const newGambarSrc = [...gambarSrc];
                                                            const newGambarFile = [...gambarFile];
                                                            newGambarSrc[ind_v].push(reader.result);
                                                            newGambarFile[ind_v].push(file);
                                                            setGambarSrc(newGambarSrc);
                                                            setGambarFile(newGambarFile);
                                                        };
                                                        reader.readAsDataURL(file);
                                                    }
                                                }}
                                            />
                                            <label htmlFor={ind_v} className="btn-default">
                                                +
                                            </label>
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
                                                            onChange={(e) => {
                                                                setFormData({
                                                                    ...formData,
                                                                    varian: formData.varian.map((vv, ind_vv) => {
                                                                        if(ind_vv === ind_v) {
                                                                            return {
                                                                                ...vv,
                                                                                nama: e.target.value
                                                                            }
                                                                        } else {
                                                                            return vv
                                                                        }
                                                                    })
                                                                })
                                                            }}
                                                            value={v.nama}
                                                            required
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
                                                            required
                                                            onChange={(e) => {
                                                                setFormData({
                                                                    ...formData,
                                                                    varian: formData.varian.map((vv, ind_vv) => {
                                                                        if(ind_vv === ind_v) {
                                                                            return {
                                                                                ...vv,
                                                                                kode: e.target.value
                                                                            }
                                                                        } else {
                                                                            return vv
                                                                        }
                                                                    })
                                                                })
                                                            }}
                                                            value={v.kode}
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
                                                            required
                                                            onChange={(e) => {
                                                                setFormData({
                                                                    ...formData,
                                                                    varian: formData.varian.map((vv, ind_vv) => {
                                                                        if(ind_vv === ind_v) {
                                                                            return {
                                                                                ...vv,
                                                                                stok: e.target.value
                                                                            }
                                                                        } else {
                                                                            return vv
                                                                        }
                                                                    })
                                                                })
                                                            }}
                                                            value={v.stok}
                                                        />
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button
                                        type="button"
                                        className="btn-teks-aja m-0 ms-auto mt-1"
                                        onClick={() => {
                                            setFormData({
                                                ...formData,
                                                varian: formData.varian.filter((vv, ind_vv) => ind_vv !== ind_v)
                                            })
                                            setGambarFile(gambarFile.filter((vv, ind_vv) => ind_vv !== ind_v));
                                            setGambarSrc(gambarSrc.filter((vv, ind_vv) => ind_vv !== ind_v));
                                        }}
                                    >Hapus</button>
                                </div>
                            ))}
                        </div>
                        <button
                            className="btn-default-merah mt-2"
                            type="button"
                            onClick={() => {
                                setFormData({
                                    ...formData,
                                    varian: [...formData.varian, { nama: "", kode: "", stok: "", urutan_gambar: "" }]
                                })
                                setGambarFile([...gambarFile, []]);
                                setGambarSrc([...gambarSrc, []]);
                            }}
                        >
                            Tambah Varian
                        </button>
                    </div>
                </div>
                <div className="hide-ke-show-flex justify-content-center mt-3">
                    <button className="btn-default" onClick={() => {handleSubmit()}}>
                        Simpan
                    </button>
                </div>
                <input
                    type="text"
                    name="hitung-varian"
                    style={{ display: "none" }}
                    defaultValue={1}
                />
            </>
        );
    }
    ReactDOM.render(<App />, document.getElementById("container-react"));
</script>
<?= $this->endSection(); ?>