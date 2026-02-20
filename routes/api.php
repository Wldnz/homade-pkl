<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::name('api')->group(function() {
    
    Route::get('/test', function(){
        return response()->json(['message' => 'hello']);
    });

    // Route::get('/menus', [ DocumentationController::class, 'userMenus' ])->name('menus');
    // Route::get('/menus/{id}', [ DocumentationController::class, 'userDetailMenu' ])->name('detail-menu');

    // Route::get('/menus-weekly', [ DocumentationController::class, 'userWeeklyMenus' ])->name('weekly-menu');
    // Route::get('/achievements', [ DocumentationController::class, 'achievements' ] )->name('achievements');

    // Route::get('/contact', [ DocumentationController::class, 'contact' ])->name('contact');

    // Route::get('/additional', [ DocumentationController::class, 'additional' ])->name('additional');

    
    Route::post('/signin', [ AuthController::class, 'signin' ])->name('signin');
    Route::post('/signup', [ AuthController::class, 'signup' ])->name('signup');
    
    Route::middleware('auth:api')->group(function (){
        Route::get('/me', [ UserController::class, 'me' ])->name('me');
        Route::get('/orders', [ TransactionController::class, 'all' ])->name('orders');
        Route::get('/orders/{id}', [ TransactionController::class, 'detailTransaction' ])->name('detail-order');
    });

});

// Route::get('/auth', [ AuthController::class, "signin" ]);
