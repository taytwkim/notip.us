<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/map', 'Page::map');
$routes->get('/list', 'Page::list');

$routes->get('/search', 'Page::search');
$routes->get('/place/info/(:any)', 'Place::info/$1');
$routes->get('/place/list', 'Place::list');

$routes->post('/review/add', 'Review::add');
$routes->get('/review/list/(:any)', 'Review::list/$1');

$routes->get('/file/download/(:any)', 'File::download/$1');
$routes->get('/file/image/(:any)/(:any)', 'File::image/$1/$2');
$routes->get('/file/view/(:any)', 'File::view/$1');


$routes->post('/place/save', 'Place::save');
$routes->post('/user/auth', 'User::auth');
$routes->post('/user/signOut', 'User::signOut');
