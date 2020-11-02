<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['namespace' => 'Home'], function () {
	Route::get('/', 'IndexController@index');
	Route::get('/maintance','IndexController@maintance');

	//customer
	Route::group(['prefix' => 'profile'], function () {
		Route::get('/', 'CustomersController@profile');
		Route::post('/updateProfile', 'CustomersController@updateProfile');
		Route::post('/updateMyPassword', 'CustomersController@updateMyPassword');

		Route::get('/wishlist', 'CustomersController@wishlist');
		Route::post('likeProduct', 'CustomersController@likeProduct');
		Route::get('unlikeProduct/{id}', 'CustomersController@unlikeProduct');

		Route::get('/orders', 'CustomersController@orders');
		Route::get('/view-order/{id}', 'CustomersController@viewOrder');

		Route::get('/shipping-address', 'CustomersController@shippingAddress');
		Route::post('/addAddress', 'CustomersController@addAddress');
		Route::post('/update-address', 'CustomersController@updateAddress');
		Route::get('/delete-address/{id}', 'CustomersController@deleteAddress');
		
		/*
		Route::post('/defaultAddress', 'ShippingAddressController@defaultAddress');
		Route::post('/update-address', 'ShippingAddressController@updateAddress');
		Route::get('/delete-address/{id}', 'ShippingAddressController@deleteAddress');
		*/
	});
	

	Route::get('/cate/{slug}','CategoriesController@index');

	Route::get('/product/{slug}','ProductsController@index');
});




