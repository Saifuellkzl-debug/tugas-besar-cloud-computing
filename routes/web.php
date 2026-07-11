<?php

/** @var \Boy\S3Materi\Router $router */

$router->get('/', 'HomeController@index');
$router->get('/materials', 'MaterialController@index');
$router->get('/upload', 'MaterialController@create');
$router->post('/upload', 'MaterialController@store');
$router->get('/download', 'MaterialController@download');
$router->post('/delete', 'MaterialController@destroy');
