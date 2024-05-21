<?php

use App\Controllers\GambarController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Pages::index', ['filter' => 'keamananRoute']);
$routes->get('/product', 'Pages::product', ['filter' => 'keamananRoute']);
$routes->get('/product/(:any)', 'Pages::product/$1', ['filter' => 'keamananRoute']);

$routes->get('/find/(:any)', 'Pages::find/$1', ['filter' => 'keamananRoute']);

$routes->get('/cart', 'Pages::cart', ['filter' => 'keamananRoute']);
$routes->get('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3');
$routes->get('/deletecart/(:any)', 'Pages::deleteCart/$1');
$routes->get('/reducecart/(:any)', 'Pages::reduceCart/$1');

$routes->get('/getkota/(:any)', 'Pages::getKota/$1');
$routes->get('/getkec/(:any)', 'Pages::getKec/$1');
$routes->get('/address', 'Pages::address', ['filter' => 'keamananRoute']);
$routes->post('/addaddress', 'Pages::addAddress');
$routes->get('/deleteaddress/(:any)', 'Pages::deleteAddress/$1');
$routes->post('/editaddress/(:any)', 'Pages::editAddress/$1');

$routes->get('/shipping/(:any)', 'Pages::shipping/$1');
$routes->get('/tracking', 'Pages::tracking');

$routes->get('/payment/(:any)', 'Pages::payment/$1');
$routes->get('/actionpay/(:any)', 'Pages::actionPay/$1');
$routes->post('/updatetransaction', 'Pages::updateTransaction');
$routes->get('/progresspay/(:any)', 'Pages::progressPay/$1');
$routes->get('/successpay/(:any)', 'Pages::successPay/$1');
$routes->get('/cencelpay', 'Pages::cencelPay');

$routes->get('/wishlist', 'Pages::wishlist', ['filter' => 'keamananRoute']);
$routes->get('/addwishlist/(:any)', 'Pages::addWishlist/$1');
$routes->get('/delwishlist/(:any)', 'Pages::delWishlist/$1');
$routes->get('/wishlisttocart', 'Pages::wishlistToCart');

$routes->get('/order', 'Pages::order', ['filter' => 'harusLogin']);
$routes->get('/order/(:any)', 'Pages::order/$1');
$routes->get('/cancelorder/(:any)', 'Pages::cancelOrder/$1'); //harus admin
$routes->get('/invoice/(:any)', 'Pages::invoice/$1');
$routes->get('/account', 'Pages::account', ['filter' => 'harusLogin']);

$routes->get('/register', 'Pages::register');
$routes->post('/actionregister', 'Pages::actionRegister');
$routes->get('/verify', 'Pages::verify');
$routes->post('/actionverify', 'Pages::actionVerify');
$routes->get('/login', 'Pages::login');
$routes->post('/actionlogin', 'Pages::actionLogin');
$routes->post('/kirimotp', 'Pages::kirimOTP');
$routes->get('/logout', 'Pages::actionLogout');

$routes->get('/faq', 'Pages::faq');
$routes->get('/tentang', 'Pages::tentang');
$routes->get('/syarat', 'Pages::syarat');
$routes->get('/kebijakan', 'Pages::kebijakan');


// GambarController
$routes->get('/viewpic/(:any)', 'GambarController::tampilGambarBarang/$1');
$routes->get('/viewvar/(:any)/(:any)', 'GambarController::tampilGambarVarian/$1/$2');


// Admin Controller
$routes->get('/listproduct', 'AdminController::listProduct');
$routes->get('/customer', 'AdminController::customer');
$routes->get('/addproduct', 'AdminController::addProduct');
$routes->post('/addproduct', 'AdminController::actionAddProduct');
$routes->get('/editproduct/(:any)', 'AdminController::editProduct/$1');
$routes->get('/deleteproduct/(:any)', 'AdminController::deleteProduct/$1');
$routes->get('/activeproduct/(:any)', 'AdminController::activeProduct/$1');
$routes->get('/orderadmin', 'AdminController::order');

//GUDANG Controller
$routes->get('/gudang/listorder', 'GudangController::listOrder');
$routes->get('/gudang/scanorder', 'GudangController::scanOrder');
$routes->post('/gudang/actionscan', 'GudangController::actionScan');
