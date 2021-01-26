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

// Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Route::get('staff-login', [App\Http\Controllers\Auth\StaffLoginController::class, 'showLoginForm'])->name('staff-login-form');
Route::post('staff-login', [App\Http\Controllers\Auth\StaffLoginController::class, 'login'])->name('staff-login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'doLogin'])->name('login');
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::match(['get', 'post'], 'daftar', [App\Http\Controllers\HomeController::class, 'welcome'])->name('welcome');
// Route::match(['get', 'post'], 'tiket', [App\Http\Controllers\HomeController::class, 'tiket'])->name('formulir');
Route::match(['get', 'post'], 'check', [App\Http\Controllers\HomeController::class, 'check'])->name('check');
Route::get('payment', [App\Http\Controllers\HomeController::class, 'paymentGateway'])->name('payment');
Route::get('payment-success', [App\Http\Controllers\HomeController::class, 'paymentSuccess'])->name('payment-success');

Route::middleware(['auth:staff'])->prefix('staff')->name('staff.')->group(function(){
    Route::get('/', [App\Http\Controllers\Staff\HomeController::class, 'index'])->name('index');
    Route::resource('pembayaran', App\Http\Controllers\Staff\PembayaranController::class);
    Route::get('pembayaran/check/{contact}',[App\Http\Controllers\Staff\PembayaranController::class,'check'])->name('pembayaran.check');
    Route::get('pembayaran/approve/{contact}',[App\Http\Controllers\Staff\PembayaranController::class,'approve'])->name('pembayaran.approve');
    
    Route::get('siswa/kelulusan',[App\Http\Controllers\Staff\SiswaController::class,'kelulusan'])->name('siswa.kelulusan');
    Route::resource('siswa', App\Http\Controllers\Staff\SiswaController::class);
    Route::get('siswa/delete/{formulir}',[App\Http\Controllers\Staff\SiswaController::class,'delete'])->name('siswa.delete');
    Route::get('siswa/approve/{formulir}',[App\Http\Controllers\Staff\SiswaController::class,'approve'])->name('siswa.approve');
    Route::get('siswa/decline/{formulir}',[App\Http\Controllers\Staff\SiswaController::class,'decline'])->name('siswa.decline');
    Route::get('siswa/lulus/{formulir}',[App\Http\Controllers\Staff\SiswaController::class,'lulus'])->name('siswa.lulus');
    Route::get('siswa/gagal/{formulir}',[App\Http\Controllers\Staff\SiswaController::class,'gagal'])->name('siswa.gagal');
});

Route::middleware(['auth'])->group(function(){
    Route::match(['get', 'post'], 'formulir', [App\Http\Controllers\HomeController::class, 'formulir'])->name('formulir');
    Route::get('send', [App\Http\Controllers\HomeController::class, 'send'])->name('send');
    Route::get('home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
    Route::get('siswa', [App\Http\Controllers\HomeController::class, 'siswa'])->name('siswa');
    Route::get('pembayaran', [App\Http\Controllers\HomeController::class, 'pembayaran'])->name('pembayaran');
    // Route::get('kartu', [App\Http\Controllers\HomeController::class, 'kartu'])->name('kartu');
    // Route::get('pernyataan', [App\Http\Controllers\HomeController::class, 'pernyataan'])->name('pernyataan');
    Route::get('isian', [App\Http\Controllers\HomeController::class, 'isian'])->name('isian');
    Route::get('berkas', [App\Http\Controllers\HomeController::class, 'berkas'])->name('berkas');
    Route::get('download/{row}', [App\Http\Controllers\HomeController::class, 'download'])->name('download');
});


