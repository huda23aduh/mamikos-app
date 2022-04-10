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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::get('kosts', 'App\Http\Controllers\KostController@index');
Route::get('kosts/{id}', 'App\Http\Controllers\KostController@show');

Route::group(['middleware' => ['auth:api']], function() {
    //prefix users
    Route::prefix('users')->group(function () {
        Route::post('details', 'App\Http\Controllers\UserController@details');
        Route::put('upgrade', 'App\Http\Controllers\UserController@upgradeStatus');
        //prefix users/activities
        Route::prefix('activities')->group(function () {
            Route::post('ask', 'App\Http\Controllers\ActivityController@askQuestion');
        });
    });
    //prefix owner
    Route::prefix('owner')->group(function () {
        Route::get('kosts', 'App\Http\Controllers\OwnerController@getKostList');
    });

    Route::post('logout', 'App\Http\Controllers\UserController@logout');

    //kost
    Route::resource('kosts', 'App\Http\Controllers\KostController', ['except' => 'index', 'show']);
});
