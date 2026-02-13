<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([])->group(function() {
    
    Route::get('/menus', [ DocumentationController::class, 'userMenus' ])->name('menus');
    Route::get('/menus/{id}', [ DocumentationController::class, 'userDetailMenu' ])->name('detail-menu');

    Route::get('/menus-weekly', [ DocumentationController::class, 'userWeeklyMenus' ])->name('weekly-menu');
    Route::get('/achievements', [ DocumentationController::class, 'achievements' ] )->name('achievements');

    Route::get('/contact', [ DocumentationController::class, 'contact' ])->name('contact');

    Route::get('/additional', [ DocumentationController::class, 'additional' ])->name('additional');

    Route::post('/signin', [ AuthController::class, 'signin' ])->name('signin');
    Route::post('/signup', [ AuthController::class, 'signup' ])->name('signup');

})->name('user');


// Route::get('/auth', [ AuthController::class, "signin" ]);
