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


Route::post('category/create', 'CategoryController@create');
Route::get('category/list', 'CategoryController@list');
Route::get('category/list_product', 'CategoryController@listWithProduct');

Route::post('mercahnt/create', 'MerchantController@create');
Route::get('merchant/list/{categoryId}', 'MerchantController@list');
Route::get('merchant/listByLatLng/{categoryId}/{lat}/{lng}', 'MerchantController@listByLatLng');

Route::post('product/create', 'ProductController@create');
Route::get('product/list/{merchantId}', 'ProductController@list');