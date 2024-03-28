<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Pages::index');
$routes->get('/product', 'Pages::product');
$routes->get('/product/(:any)', 'Pages::product/$1');
$routes->get('/cart', 'Pages::cart');