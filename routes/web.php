<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\WebMiddleware;
use App\Notifications\ResetPasswordNotification;
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

    // reset password..
    Route::get('/reset-password', [AuthController::class, 'reset'])->name('reset-password');
    Route::post('/reset-password/{token}', [AuthController::class, 'resetHandler'])->name('reset-password-handler');


    Route::middleware(WebMiddleware::class)->group(function () {
        Route::prefix('me')->group(function () {
            Route::get('/', [UserController::class, 'me'])->name('me');
            Route::put('/', [UserController::class, 'edit'])->name('edit-me');

            // user-address
            Route::get('/address', [UserAddressController::class, 'address'])->name('user-address');
            Route::get('/address/{id}', [UserAddressController::class, 'detail'])->name('detail-user-address');
            Route::get('/address-store', [UserAddressController::class, 'store'])->name('add-user-address-page');
            Route::post('/address', [UserAddressController::class, 'storeHandler'])->name('add-user-address');
            Route::put('/address/{id}', [UserAddressController::class, 'editHandler'])->name('edit-user-address');
            Route::delete('/address/{id}', [UserAddressController::class, 'removeHandler'])->name('delete-user-address');


            Route::get('/orders', [TransactionController::class, 'orders'])->name('orders');
            Route::get('/orders/{id}', [TransactionController::class, 'detail'])->name('detail-order');
        });
        Route::post('/signout', [AuthController::class, 'signout'])->name('signout');
    });
});

// tambahin role juga untuk middlewwarenya
Route::name('admin.')->prefix('admin')->group(function () {


    Route::get('/signin', [\App\Http\Controllers\Admin\AuthController::class, 'signin'])->name('signin');
    
    Route::get('/dashboard', [\App\Http\Controllers\Admin\MainController::class, 'index'])->name('dashboard');

    // theme
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'detail'])->name('detail-category');
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('add-category');
    // kategori
    Route::get('/themes', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('themes');
    Route::get('/themes/{id}', [\App\Http\Controllers\Admin\ThemeController::class, 'detail'])->name('detail-theme');
    Route::get('/themes', [\App\Http\Controllers\Admin\ThemeController::class, 'store'])->name('add-theme');
    // menu
    Route::get('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menus');
    Route::get('/menus/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'detail'])->name('detail-menu');
    Route::get('/menus', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('add-menu');
    // jadwal menu
    Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'index'])->name('schedules');
    Route::get('/schedules/{id}', [\App\Http\Controllers\Admin\ScheduleController::class, 'detail'])->name('detail-schedule');
    Route::get('/schedules', [\App\Http\Controllers\Admin\ScheduleController::class, 'store'])->name('add-schedule');
    // pemesanan
    Route::get('/orders', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\TransactionController::class, 'detail'])->name('detail-order');
    Route::get('/orders', [\App\Http\Controllers\Admin\TransactionController::class, 'store'])->name('add-order');
    // account
    Route::get('/accounts', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('accounts');
    Route::get('/accounts/{id}', [\App\Http\Controllers\Admin\AccountController::class, 'detail'])->name('detail-account');
    Route::get('/accounts', [\App\Http\Controllers\Admin\AccountController::class, 'store'])->name('add-account');
    // setting

    // Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Route::get('/menus', [AdminController::class, 'menus'])->name('menus');
    // Route::get('/schedules', [AdminController::class, 'schedules'])->name('schedules');
    // Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    // Route::get('/orders/{id}', [AdminController::class, 'detailOrder'])->name('detail-order');
})->middleware([
    WebMiddleware::class
]);


Route::get('/testing-notificiation', function (){
    $token = 'asaswasas';
    return (new ResetPasswordNotification($token))
    ->toMail(auth()->user());
})->middleware(WebMiddleware::class);