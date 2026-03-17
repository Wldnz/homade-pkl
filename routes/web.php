<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\WebMiddleware;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Route;

Route::name('user.')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/menus', [MenuController::class, 'all'])->name('menus');
    Route::get('/menus/{id}', [MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/schedule', [MenuController::class, 'weekly'])->name('schedules');

    Route::get('/select-menu', [MenuController::class, 'select'])->name('select-menu');
    Route::get('/select-menu-weekly', [MenuController::class, 'selectWeekly'])->name('select-menu-weekly');

    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
    // buat autentikasi disini banh

    Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
    Route::get('/signin', [AuthController::class, 'signin'])->name('signin');
    Route::post('/signin', [AuthController::class, 'signinHandler'])->name('signin-handler');
    Route::post('/signup', [AuthController::class, 'signupHandler'])->name('signup-handler');

    // reset password..
    Route::get('/forgot-password', [AuthController::class, 'forgot'])->name('forgot-password-page');
    Route::post('/forgot-password', [AuthController::class, 'forgotHandler'])->name('forgot-password');

    Route::get('/reset-password', [AuthController::class, 'reset'])->name('reset-password');
    Route::post('/reset-password/{token}', [AuthController::class, 'resetHandler'])->name('reset-password-handler');

    Route::middleware(WebMiddleware::class)->group(function () {
        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me'])->name('me');
            Route::put('/', [UserController::class, 'edit'])->name('edit-me');
            Route::get('/change-password', [AuthController::class, 'changePassword'])->name('change-password-page');
            Route::put('/change-password', [AuthController::class, 'changePasswordHandler'])->name('change-password');

            // user-address
            Route::get('/address', [UserAddressController::class, 'address'])->name('user-address');
            Route::get('/address/{id}', [UserAddressController::class, 'detail'])->name('detail-user-address');
            Route::get('/address-store', [UserAddressController::class, 'store'])->name('add-user-address-page');
            Route::post('/address', [UserAddressController::class, 'storeHandler'])->name('add-user-address');
            Route::put('/address/{id}', [UserAddressController::class, 'editHandler'])->name('edit-user-address');
            Route::delete('/address/{id}', [UserAddressController::class, 'removeHandler'])->name('delete-user-address');

            Route::get('/orders', [TransactionController::class, 'orders'])->name('orders');
            Route::get('/orders/{id}', [TransactionController::class, 'detail'])->name('detail-order');

            // uploud & uploud bukti pembayaran
            Route::post('/order/payment-proof/{id}', [TransactionController::class, 'uploudPaymentProofHandler'])->name('uploud-payment-proof');
            // batalkan trasanksi
            Route::post('/order/cancell/{id}', [TransactionController::class, 'cancelledTransactionHandler'])->name('cancell-order');

        });

        Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout-page');
        Route::post('/checkout', [TransactionController::class, 'preCheckoutHandler'])->name('checkout');

        Route::post('/transaction', [TransactionController::class, 'createTransaction'])->name('create-order');
        Route::get('/after-transaction', [TransactionController::class, 'afterTransactionHandler'])->name('after-transaction');

        Route::get('/signout', [AuthController::class, 'signout'])->name('signout');
    });
});

// tambahin role juga untuk middlewwarenya
Route::middleware([
    WebMiddleware::class,
    AdminMiddleware::class,
])->name('admin.')->prefix('admin')->group(function () {

    Route::withoutMiddleware([WebMiddleware::class, AdminMiddleware::class])->group(function () {
        Route::get('/signin', [\App\Http\Controllers\Admin\AuthController::class, 'signin'])->name('signin');
        Route::post('/signin', [\App\Http\Controllers\Admin\AuthController::class, 'signinHandler'])->name('signin-handler');
    });

    Route::get('/dashboard', [\App\Http\Controllers\Admin\MainController::class, 'index'])->name('dashboard');

    // theme
    Route::get('/themes', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('themes');
    Route::get('/themes/{id}', [\App\Http\Controllers\Admin\ThemeController::class, 'detail'])->name('detail-theme');
    Route::get('/theme-create', [\App\Http\Controllers\Admin\ThemeController::class, 'store'])->name('add-theme-page');
    Route::post('/themes', [\App\Http\Controllers\Admin\ThemeController::class, 'storeHandler'])->name('add-theme');
    Route::put('/theme/{id}', [\App\Http\Controllers\Admin\ThemeController::class, 'editHandler'])->name('edit-theme');
    Route::delete('/themes/{id}', [\App\Http\Controllers\Admin\ThemeController::class, 'deleteHandler'])->name('delete-theme');
    // kategori
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'detail'])->name('detail-category');

    Route::get('/category-create', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('add-category-page');
    Route::post('/category', [\App\Http\Controllers\Admin\CategoryController::class, 'storeHandler'])->name('add-category');
    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'editHandler'])->name('edit-category');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('delete-category');

    // package
    Route::get('/packages', [\App\Http\Controllers\Admin\PackageController::class, 'index'])->name('packages');
    Route::get('/packages/{id}', [\App\Http\Controllers\Admin\PackageController::class, 'detail'])->name('detail-package');
    Route::get('/package-create', [\App\Http\Controllers\Admin\PackageController::class, 'store'])->name('add-package-page');
    Route::post('/package-create', [\App\Http\Controllers\Admin\PackageController::class, 'storeHandler'])->name('add-package');
    Route::put('/packages/{id}', [\App\Http\Controllers\Admin\PackageController::class, 'editHandler'])->name('edit-package');
    Route::delete('/packages/{id}', [\App\Http\Controllers\Admin\PackageController::class, 'deleteHandler'])->name('delete-package');

    // menu
    Route::get('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menus');
    Route::get('/menus/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/menu-create', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('add-menu-page');
    Route::post('/menu-create', [\App\Http\Controllers\Admin\MenuController::class, 'storeHandler'])->name('add-menu');
    Route::put('/menus/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'editHandler'])->name('edit-menu');
    Route::delete('/menus/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'deleteHandler'])->name('delete-menu');
    // jadwal menu
    Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules');
    // Route::get('/schedules/{id}', [\App\Http\Controllers\Admin\ScheduleController::class, 'detail'])->name('detail-schedule');
    Route::post('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'storeOrUpdateHandler'])->name('add-or-update-schedules');
    // pemesanan
    Route::get('/orders', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'detail'])->name('detail-order');
    Route::get('/order-create', [\App\Http\Controllers\Admin\TransactionController::class, 'store'])->name('add-order-page');
    Route::post('/order-create', [\App\Http\Controllers\Admin\TransactionController::class, 'storeHandler'])->name('add-order');

    // note: id yang diberikan adalah id transaksi
    Route::put('/order/change-shipping-cost/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'changeShippingCostHandler'])->name('change-shipping-cost');
    Route::post('/order/reject/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'rejectTheTransactionHandler'])->name('reject-order');
    // butki pembayaran handler
    Route::post('/order/create-payment-proof/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'uploudThePaymentProofHandler'])->name('uploud-payment-proof');
    Route::post('/order/accept-payment-proof/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'acceptThePaymentProof'])->name('accept-payment-proof');
    Route::post('/order/reject-payment-proof/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'rejectThePaymentProof'])->name('reject-payment-proof');
    // ubah status pengiriman
    Route::put('/order/status-delivery/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'changeDeliveryStatusHandler'])->name('change-status-delivery-order');
    // complete the transaction
    Route::post('/order/complete/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'completeTheTransactionHandler'])->name('complete-order');

    // Partner / Perusahaan Yang Bekerja Sama
    Route::get('/partners', [\App\Http\Controllers\Admin\PartnerController::class, 'index'])->name('partners');
    Route::get('/partners/{id}', [\App\Http\Controllers\Admin\PartnerController::class, 'detail'])->name('detail-partner');
    Route::get('/partner-create', [\App\Http\Controllers\Admin\PartnerController::class, 'store'])->name('add-partner-page');
    Route::post('/partner-create', [\App\Http\Controllers\Admin\PartnerController::class, 'storeHandler'])->name('add-partner');
    Route::put('/partners/{id}', [\App\Http\Controllers\Admin\PartnerController::class, 'editHandler'])->name('edit-partner');
    Route::delete('/partners/{id}', [\App\Http\Controllers\Admin\PartnerController::class, 'deleteHandler'])->name('delete-partner');
    // Achivement / Prestasi
    Route::get('/achievements', [\App\Http\Controllers\Admin\AchievementController::class, 'index'])->name('achievements');
    Route::get('/achievements/{id}', [\App\Http\Controllers\Admin\AchievementController::class, 'detail'])->name('detail-achievement');
    Route::get('/achievement-create', [\App\Http\Controllers\Admin\AchievementController::class, 'store'])->name('add-achievement-page');
    Route::post('/achievement-create', [\App\Http\Controllers\Admin\AchievementController::class, 'storeHandler'])->name('add-achievement');
    Route::put('/achievements/{id}', [\App\Http\Controllers\Admin\AchievementController::class, 'editHandler'])->name('edit-achievement');
    Route::delete('/achievements/{id}', [\App\Http\Controllers\Admin\AchievementController::class, 'deleteHandler'])->name('delete-achievement');
    // account
    Route::get('/accounts', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('accounts');
    Route::get('/accounts/{id}', [\App\Http\Controllers\Admin\AccountController::class, 'detail'])->name('detail-account');
    Route::get('/account-create', [\App\Http\Controllers\Admin\AccountController::class, 'store'])->name('add-account');
    // setting
});

Route::get('/testing-notificiation', function () {
    $token = 'asaswasas';

    return (new ResetPasswordNotification($token))
        ->toMail(auth()->user());
})->middleware(WebMiddleware::class);
