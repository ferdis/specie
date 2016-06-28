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

// Version and availability
$app->get('/', function () use ($app) {
    return response()->json([ 
		'status' => 'ok',
		'version' => app()->version(),
	]);
});

// Currency valuations
$app->get('/currency', 'CurrencyController@getAll');
$app->get('/currency/{code}', 'CurrencyController@getIndex');

// Exchange currency
$app->post('/exchange', 'OrderController@postIndex');
