<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->post('checkin/{machine_id}', 'LicenseController@checkin');
    $router->get('/', function () use ($router) {
        return response()->json([
            'data' => [
                'app'                   => config('app.name'),
                'framework_version'     => $router->app->version(),
            ],
        ]);
    });
});
