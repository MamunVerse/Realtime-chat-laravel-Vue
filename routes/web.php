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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('userlist', 'MessageController@user_list')->name('user.list');
Route::get('usermessage/{id}', 'MessageController@usermessage')->name('user.message');
Route::get('deletesinglemessage/{id}', 'MessageController@deletesinglemessage')->name('user.deletesinglemessage');
Route::get('deleteallemessage/{id}', 'MessageController@deleteallemessage')->name('user.deleteallemessage');
Route::post('sendmessage', 'MessageController@sendmessage')->name('user.sendmessage');
