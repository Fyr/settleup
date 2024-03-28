<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->get('/login/{id:[0-9]+}/{password:[a-z0-9]+}[/{carrierId:[0-9]+}]', 'AuthController@login');
    $router->get('/reset/{id:[0-9]+}/{hash:[a-f0-9]{32}}/{password:[a-f0-9]{32}}', 'AuthController@reset');
    $router->get('/hash/{id:[0-9]+}', 'AuthController@hash');
});

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('/', 'UserController@store');
    $router->post('/sso', 'UserController@storeSso');
    $router->put('/{id:[0-9]+}', 'UserController@update');
    $router->get('/{id:[0-9]+}', 'UserController@show');
    $router->delete('/{id:[0-9]+}', 'UserController@destroy');
});

$router->group(['prefix' => 'carrier-keys'], function () use ($router) {
    $router->get('/{carrierId:[0-9]+}', 'CarrierKeyController@show');
});
