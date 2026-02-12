<?php
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::name('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('home');
    Route::get('/menus', [UserController::class, 'menus'])->name('menus');
    Route::get('/menus/{id}', [UserController::class, 'detailMenu'])->name('detail-menu');
    Route::get('/schedule', [UserController::class, 'schedules'])->name('schedules');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/contact', [UserController::class, 'contact'])->name('contact');

    // buat autentikasi disini banh

    Route::get('/signup', [ UserController::class, 'signup' ])->name('signup');
    Route::get('/signin', [ UserController::class, 'signin' ])->name('signup');

});



