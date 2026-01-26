<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== Frontend ====================
$routes->get('/faq', 'Pages::faq', ['filter' => 'customerFilter']);

// Artikel (frontend)
$routes->get('/article', 'Pages::article', ['filter' => 'customerFilter']);
$routes->post('/actionsearcharticle', 'Pages::actionSearchArticle', ['filter' => 'customerFilter']);
$routes->get('/article/find/(:any)', 'Pages::findArticle/$1', ['filter' => 'customerFilter']);
$routes->get('/article/category/(:any)', 'Pages::articleCategory/$1', ['filter' => 'customerFilter']);
$routes->get('/article/(:any)', 'Pages::article/$1', ['filter' => 'customerFilter']);

$routes->get('/tentang', 'Pages::tentang', ['filter' => 'customerFilter']);
$routes->get('/partner', 'Pages::partner', ['filter' => 'customerFilter']);
$routes->get('/syarat', 'Pages::syarat', ['filter' => 'customerFilter']);
$routes->get('/kebijakan', 'Pages::kebijakan', ['filter' => 'customerFilter']);
$routes->get('/visimisi', 'Pages::visiMisi', ['filter' => 'customerFilter']);

$routes->get('/', 'Pages::index', ['filter' => 'customerFilter']);
$routes->get('/indexgalih', 'Pages::indexGalih', ['filter' => 'customerFilter']);
$routes->get('/product', 'Pages::product', ['filter' => 'customerFilter']);
$routes->get('/product/category/(:any)', 'Pages::productCategory/$1', ['filter' => 'customerFilter']);
$routes->get('/product/(:any)', 'Pages::product/$1', ['filter' => 'customerFilter']);
$routes->get('/product/(:any)/(:any)', 'Pages::product/$1/$2', ['filter' => 'customerFilter']);

$routes->post('/actionfind', 'Pages::actionFind', ['filter' => 'customerFilter']);
$routes->get('/find/(:any)', 'Pages::find/$1', ['filter' => 'customerFilter']);

$routes->get('/cart', 'Pages::cart', ['filter' => 'customerFilter']);
$routes->post('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3', ['filter' => 'customerFilter']);
$routes->get('/deletecart/(:any)', 'Pages::deleteCart/$1', ['filter' => 'customerFilter']);
$routes->post('/reducecart/(:any)', 'Pages::reduceCart/$1', ['filter' => 'customerFilter']);

$routes->get('/getkota/(:any)', 'Pages::getKota/$1', ['filter' => 'customerFilter']);
$routes->get('/getkec/(:any)', 'Pages::getKec/$1', ['filter' => 'customerFilter']);
$routes->get('/getkode/(:any)', 'Pages::getKode/$1', ['filter' => 'customerFilter']);
$routes->get('/address', 'Pages::address', ['filter' => 'customerFilter']);

// NOTE: kamu sebelumnya ga kasih filter di addaddress.
// Kalau memang harus login customer, saran: pakai customerLoginFilter/customerFilter sesuai sistemmu.
$routes->post('/addaddress', 'Pages::addAddress', ['filter' => 'customerFilter']);

$routes->get('/deleteaddress/(:any)/(:any)', 'Pages::deleteAddress/$1/$2', ['filter' => 'customerFilter']);
$routes->post('/editaddress/(:any)', 'Pages::editAddress/$1', ['filter' => 'customerFilter']);

// Voucher (frontend)
$routes->post('/redeemvoucher/(:num)', 'Pages::redeemVoucher/$1', ['filter' => 'customerFilter']);
$routes->get('/usevoucher/(:any)', 'Pages::useVoucher/$1', ['filter' => 'customerFilter']);
$routes->get('/cancelvoucher/(:any)', 'Pages::cancelVoucher/$1', ['filter' => 'customerFilter']);
$routes->get('/removevoucher/(:num)', 'Pages::removeVoucher/$1', ['filter' => 'customerFilter']);

$routes->get('/payment/method/(:any)/(:any)', 'Pages::paymentMethod/$1/$2', ['filter' => 'customerShippingFilter']);
$routes->get('/payment/(:any)', 'Pages::payment/$1', ['filter' => 'customerShippingFilter']);
$routes->get('/actionpay/(:any)', 'Pages::actionPay/$1', ['filter' => 'customerFilter']);
$routes->post('/actionpaysnap', 'Pages::actionPaySnap', ['filter' => 'customerFilter']);
$routes->get('/actionpaycore/(:any)', 'Pages::actionPayCore/$1', ['filter' => 'customerFilter']);
$routes->post('/updatetransaction', 'Pages::updateTransaction', ['filter' => 'customerFilter']);

$routes->get('/wishlist', 'Pages::wishlist', ['filter' => 'customerFilter']);
$routes->post('/addwishlist/(:any)', 'Pages::addWishlist/$1', ['filter' => 'customerFilter']);
$routes->post('/delwishlist/(:any)', 'Pages::delWishlist/$1', ['filter' => 'customerFilter']);
$routes->get('/wishlisttocart', 'Pages::wishlistToCart', ['filter' => 'customerFilter']);

$routes->get('/order', 'Pages::order', ['filter' => 'customerFilter']);
$routes->get('/orderdetail/(:any)', 'Pages::orderDetail/$1', ['filter' => 'customerFilter']);
$routes->get('/invoice/(:any)', 'Pages::invoice/$1', ['filter' => 'customerFilter']);
$routes->get('/account', 'Pages::account', ['filter' => 'customerLoginFilter']);

$routes->get('/register', 'Pages::register', ['filter' => 'customerLogoutFilter']);
$routes->post('/actionregister', 'Pages::actionRegister', ['filter' => 'customerFilter']);
$routes->get('/verify', 'Pages::verify', ['filter' => 'customerFilter']);
$routes->post('/actionverify', 'Pages::actionVerify', ['filter' => 'customerFilter']);
$routes->get('/login', 'Pages::login', ['filter' => 'customerLogoutFilter']);
$routes->post('/actionlogin', 'Pages::actionLogin', ['filter' => 'customerFilter']);
$routes->get('/kirimotp', 'Pages::kirimOTP', ['filter' => 'customerFilter']);
$routes->post('/editsandi/(:any)', 'Pages::editSandi/$1', ['filter' => 'customerFilter']);
$routes->get('/logout', 'Pages::actionLogout');


// ==================== GambarController ====================
$routes->get('/changepic/(:any)', 'GambarController::gantiUkuran/$1', ['filter' => 'corsFilter']);
$routes->get('/gantilokasi/(:any)', 'GambarController::gantiLokasi/$1', ['filter' => 'corsFilter']);
$routes->get('/cobainput', 'GambarController::actionCobaInput');
$routes->get('/gamwm', 'GambarController::tampilGambarVarWM');
$routes->get('/imgart/(:any)', 'GambarController::tampilGambarArtikel/$1');
$routes->get('/imgart/(:any)/(:any)', 'GambarController::tampilGambarArtikel/$1/$2');
$routes->get('/imgheader/(:any)', 'GambarController::tampilGambarHeader/$1');
$routes->get('/imgheaderhp/(:any)', 'GambarController::tampilGambarHeaderHp/$1');


// ==================== Admin Controller ====================
$routes->group('admin', ['filter' => 'adminFilter'], static function($routes) {

    // Produk
    $routes->get('producttable', 'AdminController::listProductTable');
    $routes->get('product', 'AdminController::listProduct');

    // Artikel (admin)
    $routes->get('article', 'AdminController::article');
    $routes->get('article/category/(:any)', 'AdminController::articleCategory/$1');
    $routes->post('deletearticle/(:any)', 'AdminController::deleteArticle/$1');
    $routes->get('addarticle', 'AdminController::addArticle');
    $routes->post('addarticle', 'AdminController::actionAddArticle');
    $routes->get('editarticle/(:any)', 'AdminController::editArticle/$1');
    $routes->post('editarticle/(:any)', 'AdminController::actionEditArticle/$1');
    $routes->post('addgaleriarticle', 'AdminController::actionAddGaleriArticle');

    // Produk (admin)
    $routes->get('addproduct', 'AdminController::addProduct');
    $routes->get('editproduct/(:any)', 'AdminController::editProduct/$1');
    $routes->post('editproduct/(:any)', 'AdminController::actionEditProduct/$1');
    $routes->post('product', 'AdminController::actionAddProduct');
    $routes->post('deleteproduct/(:any)', 'AdminController::deleteProduct/$1');
    $routes->get('activeproduct/(:any)', 'AdminController::activeProduct/$1');

    // legacy (kalau masih dipakai)
    $routes->post('product/(:any)', 'AdminController::actionEditProduct/$1');
    $routes->post('product-old', 'AdminController::actionEditProductOld');

    // Order Online (admin)
    $routes->get('order/online', 'AdminController::order');
    $routes->get('order/add', 'AdminController::orderAdd');
    $routes->post('order/add', 'AdminController::actionOrderAdd');
    $routes->post('actioneditresi', 'AdminController::actionEditResi');
    $routes->get('reprint', 'AdminController::reprint');
    $routes->get('suratjalan/(:any)', 'AdminController::suratJalan/$1');
    $routes->get('marketplace', 'AdminController::marketplace');
    $routes->get('confirm-mp/(:any)', 'AdminController::confirmMarketplace/$1');
    $routes->get('accreprint/(:any)', 'AdminController::accReprint/$1');
    $routes->get('ordertoko/(:any)', 'AdminController::orderToko/$1', ['filter' => 'loginToko']); // khusus toko

    // Mutasi (admin)
    $routes->get('mutasiconfirm', 'AdminController::mutasiConfirm');
    $routes->get('mutasi', 'AdminController::mutasi');
    $routes->get('mutasi/(:any)', 'AdminController::mutasi/$1');
    $routes->post('actionaddmutasi', 'AdminController::actionAddMutasi');
    $routes->get('accmutasi/(:any)', 'AdminController::accMutasi/$1');
    $routes->get('denymutasi/(:any)', 'AdminController::denyMutasi/$1');
    $routes->get('labelbarang/(:any)', 'AdminController::labelBarang/$1');

    // Home layout (admin)
    $routes->get('homelayout', 'AdminController::homeLayout');
    $routes->post('homelayout', 'AdminController::actionHomeLayout');
    $routes->get('changepic', 'AdminController::changePic');

    // ==================== OFFLINE ORDER (admin) ====================

    // Penting: route spesifik harus di atas route (:any)
    $routes->get('order/offline/interior', 'AdminController::interiorList'); // kalau method ini beneran ada

    // halaman offline list (sale/interior/whatever)
    $routes->get('order/offline/(:any)', 'AdminController::orderOffline/$1');

    // API ambil items offline (buat modal/detail)
    $routes->get('getitemsoffline/(:segment)', 'AdminController::getItemsOffline/$1');

    // Form add offline + action
    $routes->get('order-offline/add', 'AdminController::orderOfflineAdd');
    $routes->post('order-offline/add', 'AdminController::actionAddOrderOffline');

    // Update offline
    $routes->post('order-offline/update', 'AdminController::orderOfflineUpdate');

    // Finalize offline (lock nomor/invoice dll)
    $routes->post('order-offline/finalize', 'AdminController::orderOfflineFinalize');

    // Koreksi SP
    $routes->post('order-offline/koreksisp', 'AdminController::actionKoreksiSP');
    $routes->get('surat-koreksi/(:any)', 'AdminController::suratKoreksi/$1');

    // Invoice / DP / Surat offline
    $routes->get('invoice-offline/(:any)', 'AdminController::suratInvoice/$1');
    $routes->get('invoice-offline-dp/(:any)', 'AdminController::suratInvoiceDP/$1');
    $routes->get('surat-offline/(:any)', 'AdminController::suratOffline/$1');

    // Action lain
    $routes->get('benerinsurat', 'AdminController::benerinSurat');
    $routes->post('actionbuatinvoice', 'AdminController::actionBuatInvoice');
    $routes->post('actionbuatdp', 'AdminController::actionBuatDP');
    $routes->get('actionaccorderoffline/(:any)', 'AdminController::actionAccOrderOffline/$1');

    // ==================== SURAT JALAN OFFLINE (admin) ====================
    // Halaman edit SJ (dipakai OFFLINE & INTERIOR)
    $routes->get(
        'surat-jalan/offline/(:num)/edit',
        'AdminController::suratJalanOfflineEdit/$1'
    );
    //EDIT SJ SAVE
    $routes->get('surat-jalan/offline/(:num)/edit', 'AdminController::suratJalanOfflineEdit/$1');
    $routes->post('surat-jalan/offline/(:num)/edit-save', 'AdminController::suratJalanOfflineEditSave/$1');

    // Simpan perubahan SJ (qty, tanggal, dll)
    $routes->post(
        'surat-jalan/offline/(:num)/edit',
        'AdminController::suratJalanOfflineEditSave/$1'
    );  
    // Create SJ harus POST (bikin data)
    $routes->post('surat-jalan/offline/create/(:segment)', 'AdminController::createSuratJalanOffline/$1');
    $routes->get('surat-jalan/offline/create/(:segment)', 'AdminController::createSuratJalanOffline/$1');

    // Buka/print/edit SJ
    $routes->get('surat-jalan/offline/(:num)', 'AdminController::suratJalanOffline/$1');

    // Finalize SJ OFFLINE (kunci nomor & status)
    $routes->post('surat-jalan/offline/(:num)/finalize', 'AdminController::suratJalanOfflineFinalize/$1');

    // ==================== SEO helper (admin) — optional ====================
    $routes->get('seo-check/(:any)', 'AdminController::seoCheck/$1');

    // ==================== Voucher System (admin) ====================
    $routes->get('voucher', 'AdminController::voucher');
    $routes->post('voucher/add', 'AdminController::actionAddVoucher');
    $routes->get('voucher/delete/(:any)', 'AdminController::deleteVoucher/$1');
    $routes->get('voucher/edit/(:num)', 'AdminController::editVoucher/$1');
    $routes->post('voucher/edit/(:num)', 'AdminController::editVoucher/$1');
    $routes->post('voucher/toggle/(:num)', 'AdminController::toggleVoucher/$1');
    $routes->get('voucher/usage', 'AdminController::voucherUsage');
    $routes->get('voucher/usage/delete/(:num)', 'AdminController::deleteVoucherUsage/$1');

    // ==================== PROJECT INTERIOR (ADMIN) ====================
    $routes->get('project-interior', 'AdminController::projectInteriorList');
    $routes->get('project-interior/add', 'AdminController::projectInteriorAdd');
    $routes->post('project-interior/add', 'AdminController::actionProjectInteriorAdd');
    $routes->post('project-interior/(:segment)/sj/create', 'AdminController::projectInteriorCreateSuratJalan/$1');


    $routes->get('project-interior/(:segment)', 'AdminController::projectInteriorDetail/$1');
    $routes->post('project-interior/(:segment)/payment', 'AdminController::actionProjectInteriorAddPayment/$1');
    $routes->get('project-interior/(:segment)/invoice', 'AdminController::projectInteriorCreateInvoice/$1');
    $routes->get('project-interior/(:segment)/sj', 'AdminController::projectInteriorSuratJalan/$1');
    $routes->get('project-interior/(:segment)/payment-invoice/(:num)', 'AdminController::projectInteriorPaymentInvoice/$1/$2');

    $routes->get(
    'project-interior/(:segment)/sj/create',
    'AdminController::projectInteriorCreateSuratJalan/$1'
    );
    $routes->post('project-interior/(:segment)/surat-jalan/create', 'AdminController::projectInteriorCreateSuratJalan/$1');

    // Optional (kalau kamu pakai create SJ parsial interior)
    // $routes->post('project-interior/(:segment)/surat-jalan/create', 'AdminController::projectInteriorCreateSuratJalan/$1');
    // $routes->get('project-interior/(:segment)/surat-jalan/(:num)', 'AdminController::projectInteriorSuratJalanPrint/$1/$2');
});


// ==================== Gudang ====================
$routes->get('/gudang/listorder', 'GudangController::listOrder', ['filter' => 'gudangFilter']);
$routes->get('/gudang/listordertable', 'GudangController::listOrderTable');
$routes->get('/gudang/listorderafter', 'GudangController::listOrderAfter', ['filter' => 'gudangFilter']);
$routes->get('/gudang/mutasi', 'GudangController::mutasi', ['filter' => 'gudangFilter']);
$routes->get('/gudang/mutasi/(:any)', 'GudangController::mutasi/$1', ['filter' => 'gudangFilter']);
$routes->get('/gudang/actionscan/(:any)/(:any)', 'GudangController::actionScan/$1/$2', ['filter' => 'gudangFilter']);
$routes->post('/gudang/actionaddmutasi', 'GudangController::actionAddMutasi', ['filter' => 'gudangFilter']);
$routes->get('/gudang/suratjalan/(:any)', 'GudangController::suratJalan/$1', ['filter' => 'gudangFilter']);
$routes->post('/gudang/ajukanprint', 'GudangController::ajukanPrint', ['filter' => 'gudangFilter']);


// ==================== Fixing ====================
$routes->get('/fixmutasi', 'GudangController::fixMutasi');
$routes->get('/fixnama', 'Pages::fixNama');
$routes->get('/fixid', 'Pages::fixId');
$routes->get('/gantijenis/(:any)/(:any)', 'Pages::gantiJenis/$1/$2');
$routes->get('/fixset', 'Pages::fixSet');
$routes->get('/gantinamakekecil', 'Pages::gantinamakekecil');
$routes->get('/fix-id-barang', 'FixingController::fixIdBarang');


// ==================== Marketplace ====================
$routes->get('/market/product', 'MarketplaceController::product', ['filter' => 'marketFilter']);
$routes->post('/market/actionfind', 'MarketplaceController::actionFind', ['filter' => 'marketFilter']);
$routes->get('/market/find/(:any)', 'MarketplaceController::find/$1', ['filter' => 'marketFilter']);
$routes->get('/market/cart', 'MarketplaceController::cart', ['filter' => 'marketFilter']);
$routes->get('/market/addcart/(:any)/(:any)', 'MarketplaceController::addCart/$1/$2', ['filter' => 'marketFilter']);
$routes->get('/market/reducecart/(:any)', 'MarketplaceController::reduceCart/$1', ['filter' => 'marketFilter']);
$routes->post('/maket/submitorder', 'MarketplaceController::submitOrder', ['filter' => 'marketFilter']);

// routes untuk nyolong raja ongkir (sesuai punya kamu - tapi ini aneh karena actionnya submitOrder)
$routes->post('/ro/provinsi', 'MarketplaceController::submitOrder', ['filter' => 'marketFilter']);


// ==================== Analytics (admin) ====================
$routes->get('/analytics', 'Analytics::index', ['filter' => 'adminFilter']);
$routes->get('/analytics/live', 'Analytics::live', ['filter' => 'adminFilter']);
$routes->get('/analytics/exportCsv', 'Analytics::exportCsv', ['filter' => 'adminFilter']);
$routes->get('/analytics/exportPdf', 'Analytics::exportPdf', ['filter' => 'adminFilter']);
$routes->post('/analytics/blacklist/add', 'Analytics::addBlacklist', ['filter' => 'adminFilter']);
$routes->get('/analytics/blacklist/del/(:any)', 'Analytics::delBlacklist/$1', ['filter' => 'adminFilter']);


// Tracking
$routes->post('/track/hit', 'Track::hit');


// ==================== 404 fallback ====================
$routes->get('/(:any)', 'Pages::notFound');