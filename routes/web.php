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

Route::get('/seed', function () {
    Artisan::call('db:seed');
});
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', function () {
    return view('login');
});

Route::view('/{a}', 'home');
Route::view('/{a}/{b}', 'home');
Route::view('/{a}/{b}/{c}', 'home');
Route::view('/{a}/{b}/{c}/{d}', 'home');
Route::view('/{a}/{b}/{c}/{d}/{e}', 'home');
