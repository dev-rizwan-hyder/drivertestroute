<?php

use App\Http\Controllers\Admin\DrivingRouteAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DrivingRouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [DrivingRouteController::class, 'home'])->name('home');
Route::get('/routes', [DrivingRouteController::class, 'index'])->name('routes.index');
Route::view('/about', 'pages.about')->name('about');
Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'topic' => ['required', 'string', 'max:80'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    return back()->with('success', 'Thanks for reaching out. We will review your message and respond soon.');
})->name('contact.submit');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/driving-routes', [DrivingRouteController::class, 'index'])->name('driving-routes.index');

Route::middleware('auth')->group(function () {
    Route::get('/my-routes', [DrivingRouteController::class, 'myRoutes'])->name('driving-routes.my');
    Route::get('/driving-routes/{drivingRoute}/checkout', [DrivingRouteController::class, 'checkout'])->name('driving-routes.checkout');
    Route::post('/driving-routes/{drivingRoute}/payment-intent', [DrivingRouteController::class, 'paymentIntent'])->name('driving-routes.payment-intent');
    Route::post('/driving-routes/{drivingRoute}/checkout', [DrivingRouteController::class, 'checkoutStore'])->name('driving-routes.checkout.store');
    Route::post('/driving-routes/{drivingRoute}/buy', [DrivingRouteController::class, 'buy'])->name('driving-routes.buy');
    Route::post('/driving-routes/{drivingRoute}/start', [DrivingRouteController::class, 'start'])->name('driving-routes.start');
    Route::get('/driving-routes/{drivingRoute}', [DrivingRouteController::class, 'show'])->name('driving-routes.show');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::resource('cities', CityController::class)->except(['create', 'show', 'edit']);
        Route::resource('driving-routes', DrivingRouteAdminController::class);
    });
});
