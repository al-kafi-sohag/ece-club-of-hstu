<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Backend\AuthenticationController as BackendAuthenticationController;
use App\Http\Controllers\Backend\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [AuthController::class, 'index'])->name('login');
Route::post('post-login', [AuthController::class, 'postLogin'])->name('login.post');
Route::get('registration', [AuthController::class, 'registration'])->name('register');
Route::post('post-registration', [AuthController::class, 'postRegistration'])->name('register.post');
Route::get('dashboard', [AuthController::class, 'dashboard']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');




//Backend Routes
Route::group(['as' => 'backend.', 'prefix' => 'backend'], function () {

    //Without permission and authentication middleware
    Route::controller(BackendAuthenticationController::class)->name('auth.')->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'submit')->name('submit');
        Route::get('forgot-password', 'forgotPassword')->name('fp');
        Route::post('forgot-password', 'forgotPasswordSubmit')->name('fp.submit');
        Route::get('reset-password/{token}', 'resetPassword')->name('rp');
        Route::post('reset-password', 'resetPasswordSubmit')->name('rp.submit');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::group(['middleware' => ['admin']], function () {
        Route::controller(DashboardController::class)->name('dashboard.')->group(function () {
            Route::get('dashboard', 'index')->name('index');
        });
    });

});
