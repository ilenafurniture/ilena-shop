<?php

use App\Controllers\GambarController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/faq', 'Pages::faq', ['filter' => 'customerFilter']);
$routes->get('/tentang', 'Pages::tentang', ['filter' => 'customerFilter']);
$routes->get('/syarat', 'Pages::syarat', ['filter' => 'customerFilter']);
$routes->get('/kebijakan', 'Pages::kebijakan', ['filter' => 'customerFilter']);

$routes->get('/', 'Pages::index', ['filter' => 'customerFilter']);
$routes->get('/product', 'Pages::product', ['filter' => 'customerFilter']);
$routes->get('/product/(:any)', 'Pages::product/$1', ['filter' => 'customerFilter']);

$routes->post('/actionfind', 'Pages::actionFind', ['filter' => 'customerFilter']);
$routes->get('/find/(:any)', 'Pages::find/$1', ['filter' => 'customerFilter']);

$routes->get('/cart', 'Pages::cart', ['filter' => 'customerFilter']);
$routes->get('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3', ['filter' => 'customerFilter']);
$routes->get('/deletecart/(:any)', 'Pages::deleteCart/$1', ['filter' => 'customerFilter']);
$routes->get('/reducecart/(:any)', 'Pages::reduceCart/$1', ['filter' => 'customerFilter']);

$routes->get('/getkota/(:any)', 'Pages::getKota/$1', ['filter' => 'customerFilter']);
$routes->get('/getkec/(:any)', 'Pages::getKec/$1', ['filter' => 'customerFilter']);
$routes->get('/address', 'Pages::address', ['filter' => 'customerFilter']);
$routes->post('/addaddress', 'Pages::addAddress');
$routes->get('/deleteaddress/(:any)/(:any)', 'Pages::deleteAddress/$1/$2', ['filter' => 'customerFilter']);
$routes->post('/editaddress/(:any)', 'Pages::editAddress/$1', ['filter' => 'customerFilter']);

$routes->get('/shipping/(:any)', 'Pages::shipping/$1', ['filter' => 'customerShippingFilter']);
$routes->get('/tracking', 'Pages::tracking', ['filter' => 'customerFilter']);

$routes->get('/payment/(:any)', 'Pages::payment/$1', ['filter' => 'customerShippingFilter']);
$routes->get('/actionpay/(:any)', 'Pages::actionPay/$1');
$routes->post('/actionpaysnap', 'Pages::actionPaySnap');
$routes->post('/updatetransaction', 'Pages::updateTransaction');
$routes->get('/progresspay/(:any)', 'Pages::progressPay/$1', ['filter' => 'customerFilter']);
$routes->get('/successpay/(:any)', 'Pages::successPay/$1', ['filter' => 'customerFilter']);
$routes->get('/cencelpay', 'Pages::cencelPay', ['filter' => 'customerFilter']);

$routes->get('/wishlist', 'Pages::wishlist', ['filter' => 'customerFilter']);
$routes->get('/addwishlist/(:any)', 'Pages::addWishlist/$1', ['filter' => 'customerFilter']);
$routes->get('/delwishlist/(:any)', 'Pages::delWishlist/$1', ['filter' => 'customerFilter']);
$routes->get('/wishlisttocart', 'Pages::wishlistToCart', ['filter' => 'customerFilter']);

$routes->get('/order', 'Pages::order', ['filter' => 'customerFilter']);
$routes->get('/order/(:any)', 'Pages::order/$1', ['filter' => 'customerFilter']);
$routes->get('/cancelorder/(:any)', 'Pages::cancelOrder/$1', ['filter' => 'customerFilter']); //harus admin
$routes->get('/invoice/(:any)', 'Pages::invoice/$1', ['filter' => 'customerFilter']);
$routes->get('/account', 'Pages::account', ['filter' => 'customerLoginFilter']);

$routes->get('/register', 'Pages::register', ['filter' => 'customerLogoutFilter']);
$routes->post('/actionregister', 'Pages::actionRegister', ['filter' => 'customerFilter']);
$routes->get('/verify', 'Pages::verify', ['filter' => 'customerFilter']);
$routes->post('/actionverify', 'Pages::actionVerify', ['filter' => 'customerFilter']);
$routes->get('/login', 'Pages::login', ['filter' => 'customerLogoutFilter']);
$routes->post('/actionlogin', 'Pages::actionLogin', ['filter' => 'customerFilter']);
$routes->post('/kirimotp', 'Pages::kirimOTP', ['filter' => 'customerFilter']);
$routes->post('/editsandi/(:any)', 'Pages::editSandi/$1', ['filter' => 'customerFilter']);
$routes->get('/logout', 'Pages::actionLogout');




// GambarController
$routes->get('/viewpic/(:any)', 'GambarController::tampilGambarBarang/$1');
$routes->get('/viewvar/(:any)/(:any)', 'GambarController::tampilGambarVarian/$1/$2');
$routes->get('/viewvar3000/(:any)/(:any)', 'GambarController::tampilGambarVarian3000/$1/$2');
$routes->get('/changepic/(:any)/(:any)', 'GambarController::gantiResolusiGambar/$1/$2');
// $routes->get('/cobainput', 'GambarController::formCobaInput');
$routes->get('/cobainput', 'GambarController::actionCobaInput');
$routes->get('/gamwm', 'GambarController::tampilGambarVarWM');


// Admin Controller
$routes->get('/admin/product', 'AdminController::listProduct', ['filter' => 'adminFilter']);
$routes->get('/admin/customer', 'AdminController::customer', ['filter' => 'adminFilter']);
$routes->get('/admin/addproduct', 'AdminController::addProduct', ['filter' => 'adminFilter']);
$routes->post('/admin/addproduct', 'AdminController::actionAddProduct', ['filter' => 'adminFilter']);
$routes->get('/admin/editproduct/(:any)', 'AdminController::editProduct/$1', ['filter' => 'adminFilter']);
$routes->post('/admin/editproduct', 'AdminController::actionEditProduct', ['filter' => 'adminFilter']);
$routes->get('/admin/deleteproduct/(:any)', 'AdminController::deleteProduct/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/activeproduct/(:any)', 'AdminController::activeProduct/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/order', 'AdminController::order', ['filter' => 'adminFilter']);
$routes->post('/admin/actioneditresi', 'AdminController::actionEditResi', ['filter' => 'adminFilter']);
$routes->get('/admin/reprint', 'AdminController::reprint', ['filter' => 'adminFilter']);
$routes->get('/admin/marketplace', 'AdminController::marketplace', ['filter' => 'adminFilter']);
$routes->get('/admin/confirm-mp/(:any)', 'AdminController::confirmMarketplace/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/accreprint/(:any)', 'AdminController::accReprint/$1', ['filter' => 'adminFilter']);
$routes->get('/admin/denyreprint/(:any)', 'AdminController::denyReprint/$1', ['filter' => 'adminFilter']);
$routes->get('/gantiukuran', 'AdminController::gantiUkuran');
$routes->get('/admin/ordertoko/(:any)', 'AdminController::orderToko/$1', ['filter' => 'loginToko']);

//GUDANG Controller
$routes->get('/gudang/listorder', 'GudangController::listOrder', ['filter' => 'gudangFilter']);
$routes->get('/gudang/listordertable', 'GudangController::listOrderTable');
$routes->get('/gudang/listorderafter', 'GudangController::listOrderAfter', ['filter' => 'gudangFilter']);
$routes->get('/gudang/mutasi', 'GudangController::mutasi', ['filter' => 'gudangFilter']);
$routes->get('/gudang/mutasi/(:any)', 'GudangController::mutasi/$1', ['filter' => 'gudangFilter']);
$routes->get('/gudang/actionscan/(:any)/(:any)', 'GudangController::actionScan/$1/$2', ['filter' => 'gudangFilter']);
$routes->post('/gudang/actionaddmutasi', 'GudangController::actionAddMutasi', ['filter' => 'gudangFilter']);
$routes->get('/gudang/suratjalan/(:any)', 'GudangController::suratJalan/$1', ['filter' => 'gudangFilter']);
$routes->post('/gudang/ajukanprint', 'GudangController::ajukanPrint', ['filter' => 'gudangFilter']);



// Marketplace Controller
$routes->get('/market/product', 'MarketplaceController::product', ['filter' => 'marketFilter']);
$routes->post('/market/actionfind', 'MarketplaceController::actionFind', ['filter' => 'marketFilter']);
$routes->get('/market/find/(:any)', 'MarketplaceController::find/$1', ['filter' => 'marketFilter']);
$routes->get('/market/cart', 'MarketplaceController::cart', ['filter' => 'marketFilter']);
$routes->get('/market/addcart/(:any)/(:any)', 'MarketplaceController::addCart/$1/$2', ['filter' => 'marketFilter']);
$routes->get('/market/reducecart/(:any)', 'MarketplaceController::reduceCart/$1', ['filter' => 'marketFilter']);
$routes->post('/maket/submitorder', 'MarketplaceController::submitOrder', ['filter' => 'marketFilter']);
