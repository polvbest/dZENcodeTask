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
use Illuminate\Support\Facades\Route;
use Mews\Captcha\Captcha;


Route::post('/', "CommentController@store")->name('comment.store');
Route::get('/', "CommentController@index")->name('comment.index');
Route::get('/sort', "CommentController@sorter")->name('sorter');
Route::post('upload','CommentController@upload')->name('upload');
Route::get('images/{path}','FileController@show')->name('image.show')->where(['path' => '.*']);
Route::get('file/download/{path}','FileController@download')->name('download')->where(['path' => '.*']);

/* CAPTCHA */
Route::get('refresh_captcha', 'CaptchaController@refreshCaptcha')->name('captcha');

//Route::get('/', function () {
//    return view('comment');
//});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
