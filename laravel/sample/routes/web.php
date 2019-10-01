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
    return view('layouts\welcome');
});

// 練習問題登録
// nameを付けないとpost命令のactionの名前で利用できない
Route::get('/drills/new', 'DrillsController@new')->name('drills.new');
Route::post('/drills', 'DrillsController@create')->name('drills.create');
Route::get('/drills', 'DrillsController@index')->name('drills');
// idをURLに埋め込んでコントローラーにidを引数で渡す
Route::get('/drills/{id}/edit', 'DrillsController@edit')->name('drills.edit');
Route::post('/drills/{id}', 'DrillsController@update')->name('drills.update');
Route::post('/drills/{id}/delete', 'DrillsController@destory')->name('drills.destory');
Route::get('/drills/{id}', 'DrillsController@show')->name('drills.show');
// checkのミドルウェアを追加
Route::get('/mypage', 'DrillsController@mypage')->name('drills.mypage')->middleware('auth');




Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
