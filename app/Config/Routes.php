<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Pages::index');
$routes->get('/product', 'Pages::product');
$routes->get('/product/(:any)', 'Pages::product/$1');

$routes->get('/find/(:any)', 'Pages::find/$1');

$routes->get('/cart', 'Pages::cart');
$routes->get('/addcart/(:any)/(:any)/(:any)', 'Pages::addCart/$1/$2/$3');
$routes->get('/deletecart/(:any)', 'Pages::deleteCart/$1');
$routes->get('/reducecart/(:any)', 'Pages::reduceCart/$1');


$routes->get('/getkota/(:any)', 'Pages::getKota/$1');
$routes->get('/getkec/(:any)', 'Pages::getKec/$1');
$routes->get('/address', 'Pages::address');
$routes->post('/addaddress', 'Pages::addAddress');
$routes->get('/deleteaddress/(:any)', 'Pages::deleteAddress/$1');

$routes->get('/shipping/(:any)', 'Pages::shipping/$1');

$routes->get('/payment/(:any)', 'Pages::payment/$1');
$routes->get('/actionpay/(:any)', 'Pages::actionPay/$1');
$routes->post('/updatetransaction', 'Pages::updateTransaction');
$routes->get('/progresspay/(:any)', 'Pages::progressPay/$1');
$routes->get('/actioncheckpay/(:any)', 'Pages::actionCheckPay/$1');
$routes->get('/succespay', 'Pages::succesPay');
$routes->get('/cencelpay', 'Pages::cencelPay');

$routes->get('/wishlist', 'Pages::wishlist');
$routes->get('/addwishlist/(:any)', 'Pages::addWishlist/$1');
$routes->get('/delwishlist/(:any)', 'Pages::delWishlist/$1');
$routes->get('/wishlisttocart', 'Pages::wishlistToCart');



$routes->get('/viewpic/(:any)', 'GambarController::tampilGambarBarang/$1');
$routes->get('/viewvar/(:any)/(:any)', 'GambarController::tampilGambarVarian/$1/$2');
