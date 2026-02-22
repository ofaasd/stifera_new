<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NexadashController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');
});
    