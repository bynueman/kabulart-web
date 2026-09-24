<?php

use App\Http\Controllers\IndexController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});

//user akses :
Route::resource('/informasi',\App\Http\Controllers\InformasiController::class);
Route::resource('/gallery',\App\Http\Controllers\GaleryController::class);
Route::resource('/testimoni',\App\Http\Controllers\TestimoniController::class);
Route::get('/profil', function () {
    return view('profil');
});


//login page 
Route::get('adminpanel', [LoginController::class, 'login'])->name('login');
Route::post('adminpanel', [LoginController::class, 'login_action'])->name('login.action');

Route::resource('/',\App\Http\Controllers\IndexController::class);
Route::get('/index', [IndexController::class, 'index'])->name('index');

Route::group(['middleware' => 'nocache'], function () { //midel noceceh in browser

    //admin akses homeadmin, don't akses login page again
    Route::middleware(['admin'])->group(function (){
        Route::get('adminpanel', [LoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['auth']], function() { 

        //admin page home
        Route::get('/homeadmin', function () {
            $countGalery    = \App\Models\Postgalery::count();
            $countInformasi = \App\Models\Postinformasi::count();
            $countTestimoni = \App\Models\Posttestimoni::count();
            return view('homeadmin', compact('countGalery', 'countInformasi', 'countTestimoni'));
        });
        //add info
        Route::get('/postsinformasi', 'PostinformasiyController@index')->name('posts.index');
        Route::resource('/postsinformasi',\App\Http\Controllers\PostinformasiyController::class);
        //gallery
        Route::get('/postsgalery', 'PostgaleryController@index')->name('posts.index');
        Route::resource('/postsgalery',\App\Http\Controllers\PostgaleryController::class);
    
        Route::get('/posttestimoni', 'PosttestimoniController@index')->name('posts.index');
        Route::resource('/posttestimoni',\App\Http\Controllers\PosttestimoniController::class);
        //logout
        Route::get('/logout',[LoginController::class,'logout']);
    });
});