<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::name('user')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/menus', [MenuController::class, 'all'])->name('menus');
    Route::get('/menus/{id}', [MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/schedule', [MenuController::class, 'weekly'])->name('schedules');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/contact', [ContactController::class, 'contact'])->name('contact');

    // buat autentikasi disini banh

    Route::get('/signup', [ AuthController::class, 'signup' ])->name('signup');
    Route::get('/signin', [ AuthController::class, 'signin' ])->name('signin');
    Route::post('/signin', [ AuthController::class, 'signinHandler' ])->name('signin-handler');
    Route::post('/signup', [ AuthController::class, 'signupHandler' ])->name('signup-handler');

    Route::get('/signout', [ AuthController::class, 'signout' ])->name('signout');


});

Route::name('admin')->prefix('admin')->group(function (){

    // dashboar, kelola menu, jdwal dan pemesanan

    Route::get('/dashboard', [ AdminController::class, 'dashboard' ])->name('dashboard');
    Route::get('/menus', [ AdminController::class, 'menus' ])->name('menus');
    Route::get('/schedules', [ AdminController::class, 'schedules' ])->name('schedules');
    Route::get('/orders', [ AdminController::class, 'orders' ])->name('orders');
    Route::get('/orders/{id}', [ AdminController::class, 'detailOrder' ])->name('detail-order');
    Route::get('/signin', [ AdminController::class, 'signin' ])->name('signin');
});





Route::name('testing')->prefix('testing')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('home');
    Route::get('/menus', [UserController::class, 'menus'])->name('menus');
    Route::get('/menus/{id}', [UserController::class, 'detailMenu'])->name('detail-menu');
    Route::get('/schedule', [UserController::class, 'schedules'])->name('schedules');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/contact', [UserController::class, 'contact'])->name('contact');

    // buat autentikasi disini banh

    Route::get('/signup', [ App\Http\Controllers\Testing\AuthController::class, 'signup' ])->name('signup');
    Route::get('/signin', [ App\Http\Controllers\Testing\AuthController::class, 'signin' ])->name('signin');
    Route::post('/signin', [ AuthController::class, 'signinHandler' ])->name('signin-handler');
    Route::post('/signup', [ AuthController::class, 'signupHandler' ])->name('signup-handler');
    
    Route::get('/authorized', [App\Http\Controllers\Testing\AuthController::class, 'authorized'])->name('authorized');

});
