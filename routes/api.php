<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TestEmailController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\ApiMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::name('api')->group(function () {

    Route::get('/test', function () {
        return response()->json(['message' => 'hello']);
    });

    // Route::get('/menus', [ DocumentationController::class, 'userMenus' ])->name('menus');
    // Route::get('/menus/{id}', [ DocumentationController::class, 'userDetailMenu' ])->name('detail-menu');

    // Route::get('/menus-weekly', [ DocumentationController::class, 'userWeeklyMenus' ])->name('weekly-menu');
    // Route::get('/achievements', [ DocumentationController::class, 'achievements' ] )->name('achievements');

    // Route::get('/contact', [ DocumentationController::class, 'contact' ])->name('contact');

    // Route::get('/additional', [ DocumentationController::class, 'additional' ])->name('additional');


    Route::post('/signin', [AuthController::class, 'signin'])->name('signin');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

    Route::get('/menus', [MenuController::class, 'menu'])->name('menu');
    Route::get('/menus/{id}', [MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/menu-weekly', [MenuController::class, 'weekly'])->name('menu-weekly');
    Route::get('/packages', [MenuController::class, 'package'])->name('packages');

    Route::get('/achievements', [ProfileController::class, 'achievements'])->name('achievements');
    Route::get('/partners', [ProfileController::class, 'partners'])->name('partners');
    
    Route::prefix('contact')->group(function(){
        Route::get('/', [ContactController::class, 'contact'])->name('contact');
        Route::get('/full', [ContactController::class, 'full'])->name('contact-full');
        Route::get('/social-media', [ContactController::class, 'socialMedia'])->name('social-media');
        Route::get('/address', [ContactController::class, 'address'])->name('address');
        Route::get('/operational', [ContactController::class, 'operational'])->name('operational');
    });


    Route::middleware(ApiMiddleware::class)->group(function () {
        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me'])->name('me');
            Route::put('/', [UserController::class, 'edit'])->name('edit-me');

            // user address
            Route::get('/address', [UserAddressController::class, 'address'])->name('user-address');
            Route::get('/address/{id}', [UserAddressController::class, 'detail'])->name('detail-user-address');
            Route::post('/address', [UserAddressController::class, 'store'])->name('add-user-address');
            Route::put('/address/{id}', [UserAddressController::class, 'edit'])->name('edit-user-address');
            Route::delete('/address/{id}', [UserAddressController::class, 'remove'])->name('delete-user-address');

            Route::get('/orders', [TransactionController::class, 'all'])->name('orders');
            Route::get('/orders/{id}', [TransactionController::class, 'detailTransaction'])->name('detail-order');

            });
            Route::get('/test-email', [TestEmailController::class, 'local'])->name('test-email');
        Route::post('/signout', [AuthController::class, 'signout'])->name('signout');
    });

});

// Route::get('/auth', [ AuthController::class, "signin" ]);
