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

Route::group(['prefix' => 'post'], function() {
	Route::post('create', 'API\PostController@create');
	Route::get('read/{id?}', 'API\PostController@read');
	Route::post('update/{id}', 'API\PostController@update');
	Route::delete('delete/{id}', 'API\PostController@delete');
	Route::post('addTag/{tagId}/{postId}', 'API\PostController@addTag');
	Route::delete('deleteTag/{tagId}/{postId}', 'API\PostController@deleteTag');
});

Route::group(['prefix' => 'tag'], function() {
	Route::post('create', 'API\TagController@create');
	Route::get('read/{id?}', 'API\TagController@read');
	Route::post('update/{id}', 'API\TagController@update');
	Route::delete('delete/{id}', 'API\TagController@delete');
});

Route::group(['prefix' => 'gallery'], function() {
	Route::post('create', 'API\GalleryController@create');
	Route::delete('delete/{id}', 'API\GalleryController@delete');
	Route::get('list', 'API\GalleryController@list');
	Route::post('addImg', 'API\GalleryController@addImg');
	Route::get('getImg', 'API\GalleryController@getImg');
});