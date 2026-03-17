<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TestEmailController;
use App\Http\Controllers\Api\TestingController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\ApiMiddleware;
use Illuminate\Support\Facades\Route;

Route::name('api')->group(function () {

    Route::get('/test', function () {
        return response()->json(['message' => 'hello']);
    });

    Route::prefix('file')->group(function (): void {
        Route::get('/payment-proofs', [TestingController::class, 'all'])->name('all-files');
        Route::post('/uploud', [TestingController::class, 'uploudFile'])->name('uploud-file');
        Route::get('/detail/{id}', [TestingController::class, 'getImage'])->name('get-file');
        Route::delete('/delete', [TestingController::class, 'DeleteFile'])->name('delete-file');
    });

    Route::post('/signin', [AuthController::class, 'signin'])->name('signin');
    Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->name('create-forgot-password');
    Route::put('/reset-password/{token}', [AuthController::class, 'reset'])->name('reset-password');

    Route::get('/menus', [MenuController::class, 'menu'])->name('menu');
    Route::get('/menus/{id}', [MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/menu-weekly', [MenuController::class, 'weekly'])->name('menu-weekly');
    Route::get('/menu-date', [MenuController::class, 'getByDate'])->name('menu-date');
    Route::get('/menu-weekly-populer', [MenuController::class, 'getPopulerWeeklyMenu'])->name('menu-weekly-populer');
    Route::get('/packages', [MenuController::class, 'package'])->name('packages');

    Route::get('/achievements', [ProfileController::class, 'achievements'])->name('achievements');
    Route::get('/partners', [ProfileController::class, 'partners'])->name('partners');

    // themes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    // categories
    Route::get('/themes', [ThemeController::class, 'index'])->name('themes');

    Route::prefix('contact')->group(function () {
        Route::get('/full', [ContactController::class, 'full'])->name('contact-full');
        Route::get('/', [ContactController::class, 'contact'])->name('contact');
        Route::get('/social-media', [ContactController::class, 'socialMedia'])->name('social-media');
        Route::get('/address', [ContactController::class, 'address'])->name('address');
        Route::get('/operational', [ContactController::class, 'operational'])->name('operational');
    });


    Route::middleware(ApiMiddleware::class)->group(function () {
        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me'])->name('me');
            Route::put('/', [UserController::class, 'edit'])->name('edit-me');

            // password
            Route::put('/change-password', [AuthController::class, 'change'])->name('change-password');

            // user address
            Route::get('/address', [UserAddressController::class, 'address'])->name('user-address');
            Route::get('/address/{id}', [UserAddressController::class, 'detail'])->name('detail-user-address');
            Route::post('/address', [UserAddressController::class, 'store'])->name('add-user-address');
            Route::put('/address/{id}', [UserAddressController::class, 'edit'])->name('edit-user-address');
            Route::delete('/address/{id}', [UserAddressController::class, 'remove'])->name('delete-user-address');

            Route::get('/orders', [TransactionController::class, 'all'])->name('orders');
            Route::get('/orders/{id}', [TransactionController::class, 'detailTransaction'])->name('detail-order');
        });

        // order
        Route::post('/pre-checkout', [TransactionController::class, 'preCheckout'])->name('pre-checkout');
        Route::post('/checkout', [TransactionController::class, 'checkout'])->name('checkout');

        Route::post('/uploud-payment-proof/{id}', [TransactionController::class, 'uploudPaymentProof'])->name('uploud-payment-proof');
        Route::post('/cancel-order/{id}', [TransactionController::class, 'cancelOrder'])->name('cancel-order');
        
        Route::get('/test-email', [TestEmailController::class, 'local'])->name('test-email');
        Route::post('/signout', [AuthController::class, 'signout'])->name('signout');
    });

});

// Route::get('/auth', [ AuthController::class, "signin" ]);
