<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router
    ->group(
        [
            'middleware' => 'version'
        ],
        function(Router $router) {
            $router
                ->group(
                    [
                        'prefix' => 'users',
                    ],
                    function(Router $router) {
                        $router->post('', [
                            'uses' => 'UsersController@store',
                            'middleware' => 'not_auth:api'
                        ]);
                        $router->get('me', [
                            'uses' => 'UsersController@me',
                            'middleware' => 'auth:api'
                        ]);
                    }
                );

            $router
                ->group(
                    [
                        'prefix' => 'recipes',
                        'middleware' => 'auth:api'
                    ],
                    function(Router $router) {
                        $router->get('', 'RecipesController@index');
                        $router->post('', [
                            'uses' => 'RecipesController@store',
                            'middleware' => 'restriction'
                        ]);
                        $router->get('{id:[0-9]+}', 'RecipesController@show');
                        $router->put('{id:[0-9]+}', [
                            'uses' => 'RecipesController@update',
                            'middleware' => 'restriction'
                        ]);
                        $router->delete('{id:[0-9]+}', 'RecipesController@destroy');
                    }
                );
        }
    );