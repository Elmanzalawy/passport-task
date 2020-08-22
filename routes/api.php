<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

    
});

Route::group(['middleware' => ['cors', 'json.response']], function () {
    //public api requests go here..
    // public routes
    Route::post('/login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','Auth\ApiAuthController@register')->name('register.api');




    Route::middleware('auth:api')->group(function () {
        // our routes to be protected will go in here
        Route::get('test', 'Auth\ApiAuthController@test'); //used to debug auth issues
        //product api routes (incomplete)
        Route::apiResource('/product','ProductsController');
        //logout if user is already logged in (requires valid token)
        Route::post('/logout', 'Auth\ApiAuthController@logout')->name('logout.api');
    });
});


