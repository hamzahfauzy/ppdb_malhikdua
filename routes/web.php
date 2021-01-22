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

Route::match(['get', 'post'], '/', [App\Http\Controllers\HomeController::class, 'welcome'])->name('welcome');
Route::match(['get', 'post'], 'formulir', [App\Http\Controllers\HomeController::class, 'formulir'])->name('formulir');
Route::match(['get', 'post'], 'tiket', [App\Http\Controllers\HomeController::class, 'tiket'])->name('formulir');
Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('siswa', [App\Http\Controllers\HomeController::class, 'siswa'])->name('siswa');
Route::get('pembayaran', [App\Http\Controllers\HomeController::class, 'pembayaran'])->name('pembayaran');
Route::get('kartu', [App\Http\Controllers\HomeController::class, 'kartu'])->name('kartu');
Route::get('pernyataan', [App\Http\Controllers\HomeController::class, 'pernyataan'])->name('pernyataan');
Route::get('isian', [App\Http\Controllers\HomeController::class, 'isian'])->name('isian');
