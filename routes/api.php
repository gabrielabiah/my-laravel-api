<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChangePasswordController;

Route::group(['middleware' => 'api',], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/sendPasswordResetLink', [AuthController::class, 'sendPasswordResetLink']);
    Route::post('/resetPassword', [ChangePasswordController::class, 'proccess']);



    Route::get('/test', [AuthController::class, 'test']);
});
