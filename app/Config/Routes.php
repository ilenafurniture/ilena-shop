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


$routes->get('/address', 'Pages::address');

$routes->get('/shipping', 'Pages::shipping');

$routes->get('/payment', 'Pages::payment');
$routes->get('/progresspay', 'Pages::progressPay');
$routes->get('/wishlist', 'Pages::wishlist');



$routes->get('/viewpic/(:any)', 'GambarController::tampilGambarBarang/$1');
$routes->get('/viewvar/(:any)/(:any)', 'GambarController::tampilGambarVarian/$1/$2');