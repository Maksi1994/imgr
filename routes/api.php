<?php

use Illuminate\Http\Request;

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


Route::group([
    'prefix' => 'users'
], function () {
    Route::post('regist', 'UsersController@regist');
    Route::post('login', 'UsersController@login');
    Route::get('accept-registration/{token}', 'UsersController@acceptRegistration');
    Route::get('logout', 'UsersController@logout')->middleware('auth:api');
    Route::get('get-curr-user', 'UsersController@getCurrUser')->middleware('auth:api');

});

Route::group([
    'prefix' => 'posts'
], function () {

    Route::post('save', 'PostsController@save')->middleware('auth:api');
    Route::get('delete/{id}', 'PostsController@delete')->middleware('auth:api');

});
