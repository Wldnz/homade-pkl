<?php
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('home');

Route::get('/menu', function () {
    return view('menus');
});

Route::get('/menu/:id', function () {
    return view('detail-menu');
});
Route::get('/schedule', function () {
    return view('schedule');
});



