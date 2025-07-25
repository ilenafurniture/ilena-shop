<?php

use App\Controllers\GambarController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/faq', 'Pages::faq', ['filter' => 'customerFilter']);

// Artikel
$routes->get('/article', 'Pages::article', ['filter' => 'customerFilter']);
$routes->post('/actionsearcharticle', 'Pages::actionSearchArticle', ['filter' => 'customerFilter']);
$routes->get('/article/find/(:any)', 'Pages::findArticle/$1', ['filter' => 'customerFilter']);
$routes->get('/article/category/(:any)', 'Pages::articleCategory/$1', ['filter' => 'customerFilter']);
$routes->get('/article/(:any)', 'Pages::article/$1', ['filter' => 'customerFilter']);


$routes->get('/tentang', 'Pages::tentang', ['filter' => 'customerFilter']);
$routes->get('/partner', 'Pages::partner', ['filter' => 'customerFilter']);
// $routes->get('/contact', 'Pages::contact', ['filter' => 'customerFilter']);
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
// $routes->get('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3', ['filter' => 'customerFilter']);
$routes->post('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3', ['filter' => 'customerFilter']);
$routes->get('/deletecart/(:any)', 'Pages::deleteCart/$1', ['filter' => 'customerFilter']);
// $routes->get('/reducecart/(:any)', 'Pages::reduceCart/$1', ['filter' => 'customerFilter']);
$routes->post('/reducecart/(:any)', 'Pages::reduceCart/$1', ['filter' => 'customerFilter']);

$routes->get('/getkota/(:any)', 'Pages::getKota/$1', ['filter' => 'customerFilter']);
$routes->get('/getkec/(:any)', 'Pages::getKec/$1', ['filter' => 'customerFilter']);
$routes->get('/getkode/(:any)', 'Pages::getKode/$1', ['filter' => 'customerFilter']);
$routes->get('/address', 'Pages::address', ['filter' => 'customerFilter']);
$routes->post('/addaddress', 'Pages::addAddress');
$routes->get('/deleteaddress/(:any)/(:any)', 'Pages::deleteAddress/$1/$2', ['filter' => 'customerFilter']);
$routes->post('/editaddress/(:any)', 'Pages::editAddress/$1', ['filter' => 'customerFilter']);

// $routes->get('/shipping/(:any)', 'Pages::shipping/$1', ['filter' => 'customerShippingFilter']);
// $routes->get('/tracking', 'Pages::tracking', ['filter' => 'customerFilter']);

$routes->get('/usevoucher/(:any)', 'Pages::useVoucher/$1');
$routes->get('/cancelvoucher/(:any)', 'Pages::cancelVoucher/$1');
$routes->get('/payment/method/(:any)/(:any)', 'Pages::paymentMethod/$1/$2', ['filter' => 'customerShippingFilter']);
$routes->get('/payment/(:any)', 'Pages::payment/$1', ['filter' => 'customerShippingFilter']);
$routes->get('/actionpay/(:any)', 'Pages::actionPay/$1');
$routes->post('/actionpaysnap', 'Pages::actionPaySnap');
$routes->get('/actionpaycore/(:any)', 'Pages::actionPayCore/$1');
$routes->post('/updatetransaction', 'Pages::updateTransaction');
// $routes->get('/progresspay/(:any)', 'Pages::progressPay/$1', ['filter' => 'customerFilter']);
// $routes->get('/successpay/(:any)', 'Pages::successPay/$1', ['filter' => 'customerFilter']);
// $routes->get('/cencelpay', 'Pages::cencelPay', ['filter' => 'customerFilter']);

$routes->get('/wishlist', 'Pages::wishlist', ['filter' => 'customerFilter']);
// $routes->get('/addwishlist/(:any)', 'Pages::addWishlist/$1', ['filter' => 'customerFilter']);
$routes->post('/addwishlist/(:any)', 'Pages::addWishlist/$1', ['filter' => 'customerFilter']);
// $routes->get('/delwishlist/(:any)', 'Pages::delWishlist/$1', ['filter' => 'customerFilter']);
$routes->post('/delwishlist/(:any)', 'Pages::delWishlist/$1', ['filter' => 'customerFilter']);
$routes->get('/wishlisttocart', 'Pages::wishlistToCart', ['filter' => 'customerFilter']);

$routes->get('/order', 'Pages::order', ['filter' => 'customerFilter']);
$routes->get('/orderdetail/(:any)', 'Pages::orderDetail/$1', ['filter' => 'customerFilter']);
// $routes->get('/cancelorder/(:any)', 'Pages::cancelOrder/$1', ['filter' => 'customerFilter']); //harus admin
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

// GambarController
// $routes->get('/viewpic/(:any)', 'GambarController::tampilGambarBarang/$1');
// $routes->get('/viewpichover/(:any)', 'GambarController::tampilGambarBarangHover/$1');
// $routes->get('/viewvar/(:any)/(:any)', 'GambarController::tampilGambarVarian/$1/$2');
// $routes->get('/viewvar3000/(:any)/(:any)', 'GambarController::tampilGambarVarian3000/$1/$2');
$routes->get('/changepic/(:any)', 'GambarController::gantiUkuran/$1', ['filter' => 'corsFilter']);
$routes->get('/gantilokasi/(:any)', 'GambarController::gantiLokasi/$1', ['filter' => 'corsFilter']);
// $routes->get('/cobainput', 'GambarController::formCobaInput');
$routes->get('/cobainput', 'GambarController::actionCobaInput');
$routes->get('/gamwm', 'GambarController::tampilGambarVarWM');
$routes->get('/imgart/(:any)', 'GambarController::tampilGambarArtikel/$1');
$routes->get('/imgart/(:any)/(:any)', 'GambarController::tampilGambarArtikel/$1/$2');
$routes->get('/imgheader/(:any)', 'GambarController::tampilGambarHeader/$1');
$routes->get('/imgheaderhp/(:any)', 'GambarController::tampilGambarHeaderHp/$1');


// Admin Controller
$routes->get('/admin/producttable', 'AdminController::listProductTable', ['filter' => 'adminFilter']);
$routes->get('/admin/product', 'AdminController::listProduct', ['filter' => 'adminFilter']);
$routes->get('/admin/article', 'AdminController::article', ['filter' => 'adminFilter']);
$routes->get('/admin/article/(:any)', 'AdminController::article/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/deletearticle/(:any)', 'AdminController::deleteArticle/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/addarticle', 'AdminController::addArticle', ['filter' => 'adminFilter']);
$routes->post('/admin/addarticle', 'AdminController::actionAddArticle', ['filter' => 'adminFilter']);
$routes->get('/admin/editarticle/(:any)', 'AdminController::editArticle/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/editarticle/(:any)', 'AdminController::actionEditArticle/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/addgaleriarticle', 'AdminController::actionAddGaleriArticle', ['filter' => 'adminFilter']);
// $routes->get('/admin/customer', 'AdminController::customer', ['filter' => 'adminFilter']);

$routes->get('/admin/addproduct', 'AdminController::addProduct', ['filter' => 'adminFilter']);
$routes->post('/admin/product/(:any)', 'AdminController::actionEditProduct/$1');
$routes->post('/admin/product', 'AdminController::actionAddProduct');
// $routes->match(['POST', 'PUT'], '/admin/product/(:any)', 'AdminController::actionEditProduct');
$routes->get('/admin/editproduct/(:any)', 'AdminController::editProduct/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/deleteproduct/(:any)', 'AdminController::deleteProduct/$1', ['filter' => 'adminFilter']);

$routes->get('/admin/activeproduct/(:any)', 'AdminController::activeProduct/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/order/online', 'AdminController::order', ['filter' => 'adminFilter']);
$routes->get('/admin/order/add', 'AdminController::orderAdd', ['filter' => 'adminFilter']);
$routes->post('/admin/order/add', 'AdminController::actionOrderAdd', ['filter' => 'adminFilter']);
$routes->post('/admin/actioneditresi', 'AdminController::actionEditResi', ['filter' => 'adminFilter']);
$routes->get('/admin/reprint', 'AdminController::reprint', ['filter' => 'adminFilter']);
$routes->get('/admin/suratjalan/(:any)', 'AdminController::suratJalan/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/marketplace', 'AdminController::marketplace', ['filter' => 'adminFilter']);
$routes->get('/admin/confirm-mp/(:any)', 'AdminController::confirmMarketplace/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/accreprint/(:any)', 'AdminController::accReprint/$1', ['filter' => 'adminFilter']);
// $routes->get('/admin/denyreprint/(:any)', 'AdminController::denyReprint/$1', ['filter' => 'adminFilter']);
$routes->get('/gantiukuran/(:any)/(:any)', 'AdminController::gantiUkuran/$1/$2');
$routes->get('/admin/ordertoko/(:any)', 'AdminController::orderToko/$1', ['filter' => 'loginToko']);
$routes->get('/admin/mutasiconfirm', 'AdminController::mutasiConfirm', ['filter' => 'adminFilter']);
$routes->get('/admin/mutasi', 'AdminController::mutasi', ['filter' => 'adminFilter']);
$routes->get('/admin/mutasi/(:any)', 'AdminController::mutasi/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/actionaddmutasi', 'AdminController::actionAddMutasi', ['filter' => 'adminFilter']);
$routes->get('/admin/accmutasi/(:any)', 'AdminController::accMutasi/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/denymutasi/(:any)', 'AdminController::denyMutasi/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/labelbarang/(:any)', 'AdminController::labelBarang/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/homelayout', 'AdminController::homeLayout', ['filter' => 'adminFilter']);
$routes->post('/admin/homelayout', 'AdminController::actionHomeLayout', ['filter' => 'adminFilter']);
$routes->get('/admin/changepic', 'AdminController::changePic', ['filter' => 'adminFilter']);
$routes->get('/admin/order/offline/(:any)', 'AdminController::orderOffline/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/getitemsoffline/(:any)', 'AdminController::getItemsOffline/$1');
$routes->get('/admin/order-offline/add', 'AdminController::orderOfflineAdd', ['filter' => 'adminFilter']);
$routes->get('/admin/invoice-offline/(:any)', 'AdminController::suratInvoice/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/invoice-offline-dp/(:any)', 'AdminController::suratInvoiceDP/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/surat-offline/(:any)', 'AdminController::suratOffline/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/order-offline/add', 'AdminController::actionAddOrderOffline');
$routes->post('/admin/order-offline/koreksisp', 'AdminController::actionKoreksiSP');
$routes->get('/admin/surat-koreksi/(:any)', 'AdminController::suratKoreksi/$1');
$routes->get('/admin/benerinsurat', 'AdminController::benerinSurat');
$routes->post('/admin/actionbuatinvoice', 'AdminController::actionBuatInvoice');
$routes->post('/admin/actionbuatdp', 'AdminController::actionBuatDP');
$routes->get('/admin/actionaccorderoffline/(:any)', 'AdminController::actionAccOrderOffline/$1', ['filter' => 'adminFilter']);

#region GUDANG
$routes->get('/gudang/listorder', 'GudangController::listOrder', ['filter' => 'gudangFilter']);
$routes->get('/gudang/listordertable', 'GudangController::listOrderTable');
$routes->get('/gudang/listorderafter', 'GudangController::listOrderAfter', ['filter' => 'gudangFilter']);
$routes->get('/gudang/mutasi', 'GudangController::mutasi', ['filter' => 'gudangFilter']);
$routes->get('/gudang/mutasi/(:any)', 'GudangController::mutasi/$1', ['filter' => 'gudangFilter']);
$routes->get('/gudang/actionscan/(:any)/(:any)', 'GudangController::actionScan/$1/$2', ['filter' => 'gudangFilter']);
$routes->post('/gudang/actionaddmutasi', 'GudangController::actionAddMutasi', ['filter' => 'gudangFilter']);
$routes->get('/gudang/suratjalan/(:any)', 'GudangController::suratJalan/$1', ['filter' => 'gudangFilter']);
$routes->post('/gudang/ajukanprint', 'GudangController::ajukanPrint', ['filter' => 'gudangFilter']);

#region Fixing
$routes->get('/fixmutasi', 'GudangController::fixMutasi');
$routes->get('/fixnama', 'Pages::fixNama');
$routes->get('/fixid', 'Pages::fixId');
$routes->get('/gantijenis/(:any)/(:any)', 'Pages::gantiJenis/$1/$2');
$routes->get('/fixset', 'Pages::fixSet');
$routes->get('/gantinamakekecil', 'Pages::gantinamakekecil');
$routes->get('/fix-id-barang', 'FixingController::fixIdBarang');

#region Marketplace
$routes->get('/market/product', 'MarketplaceController::product', ['filter' => 'marketFilter']);
$routes->post('/market/actionfind', 'MarketplaceController::actionFind', ['filter' => 'marketFilter']);
$routes->get('/market/find/(:any)', 'MarketplaceController::find/$1', ['filter' => 'marketFilter']);
$routes->get('/market/cart', 'MarketplaceController::cart', ['filter' => 'marketFilter']);
$routes->get('/market/addcart/(:any)/(:any)', 'MarketplaceController::addCart/$1/$2', ['filter' => 'marketFilter']);
$routes->get('/market/reducecart/(:any)', 'MarketplaceController::reduceCart/$1', ['filter' => 'marketFilter']);
$routes->post('/maket/submitorder', 'MarketplaceController::submitOrder', ['filter' => 'marketFilter']);

// routes untuk nyolong raja ongkir
$routes->post('/ro/provinsi', 'MarketplaceController::submitOrder', ['filter' => 'marketFilter']);


$routes->get('/(:any)', 'Pages::notFound');