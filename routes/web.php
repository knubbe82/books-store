<?php

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


Route::get('/', 'BookController@index')->name('welcome');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/payment/{id}', 'PaymentController@paymentForm')->name('payment.form');
Route::post('/payment', 'PaymentController@pay')->name('pay');

Route::get('/user/books', 'BookController@userBooks')->name('user.books');

Route::post('/refund', 'PaymentController@refund')->name('payment.refund');
