<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::name('user.')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/menus', [MenuController::class, 'all'])->name('menus');
    Route::get('/menus/{id}', [MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/schedule', [MenuController::class, 'weekly'])->name('schedules');
    Route::get('/select-menu', [MenuController::class, 'select'])->name('select-menu');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
    // buat autentikasi disini banh

    Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
    Route::get('/signin', [AuthController::class, 'signin'])->name('signin');
    Route::post('/signin', [AuthController::class, 'signinHandler'])->name('signin-handler');
    Route::post('/signup', [AuthController::class, 'signupHandler'])->name('signup-handler');


    Route::middleware('auth')->group(function () {
        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me'])->name('me');
            Route::get('/orders', [TransactionController::class, 'orders'])->name('orders');
            Route::get('/orders/{id}', [TransactionController::class, 'detail'])->name('detail-order');
        });
        Route::post('/signout', [AuthController::class, 'signout'])->name('signout');
    });



});

// tambahin role juga untuk middlewwarenya
Route::name('admin.')->prefix('admin')->group(function () {

    // dashboar, kelola menu, jdwal dan pemesanan

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/menus', [AdminController::class, 'menus'])->name('menus');
    Route::get('/schedules', [AdminController::class, 'schedules'])->name('schedules');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [AdminController::class, 'detailOrder'])->name('detail-order');
    Route::get('/signin', [AdminController::class, 'signin'])->name('signin');
});

