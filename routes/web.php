<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\GaleryController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\PostinformasiyController;
use App\Http\Controllers\PostgaleryController;
use App\Http\Controllers\PosttestimoniController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Language switcher route
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/language/{locale}', [LocaleController::class, 'switch'])->name('language.switch');

// Public routes
Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/index', [IndexController::class, 'index'])->name('index');
Route::get('/profil', function () {
    return view('profil');
})->name('profil');

Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi.index');
Route::get('/gallery', [GaleryController::class, 'index'])->name('gallery.index');
Route::get('/testimoni', [TestimoniController::class, 'index'])->name('testimoni.index');

// Auth routes (protected with nocache)
Route::group(['middleware' => 'nocache'], function () {

    // Guest login (redirects to /homeadmin if already authenticated)
    Route::middleware(['admin'])->group(function () {
        Route::get('adminpanel', [LoginController::class, 'login'])->name('login');
        Route::post('adminpanel', [LoginController::class, 'login_action'])->name('login.action');
    });

    // Authenticated admin routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/homeadmin', function () {
            $countGalery    = \App\Models\Postgalery::count();
            $countInformasi = \App\Models\Postinformasi::count();
            $countTestimoni = \App\Models\Posttestimoni::count();
            return view('homeadmin', compact('countGalery', 'countInformasi', 'countTestimoni'));
        })->name('admin.dashboard');

        Route::resource('/postsinformasi', PostinformasiyController::class);
        Route::resource('/postsgalery', PostgaleryController::class);
        Route::resource('/posttestimoni', PosttestimoniController::class);

        Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
    });
});