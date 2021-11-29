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

Route::post('login', 'Admin\BrandsController@login');

Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::get('/get_brands', 'Admin\BrandsController@getBrands');
    Route::post('/brand_create', 'Admin\BrandsController@create');
    Route::post('/update_brand', 'Admin\BrandsController@update');
    Route::post('/delete_brand', 'Admin\BrandsController@deleteBrand');
   
    Route::post('/edit_brand', 'Admin\BrandsController@editBrand');
    Route::get('/get_emails', 'Admin\BrandsController@getEmails');

   
   
});
Route::get('/download_predictions', 'Admin\BrandsController@downloadPredictions');
Route::post('/save_predictions', 'Admin\BrandsController@predictions');
Route::post('/save_email', 'Admin\BrandsController@saveEmail');
Route::post('/offline_brand', 'Admin\BrandsController@offlineBrands');
Route::post('/post_sagemaker', 'Admin\BrandsController@sagemaker');