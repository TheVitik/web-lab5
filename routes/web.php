<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Add login route
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Add logout route
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Add main route requires auth
Route::get('/', [MainController::class, 'index'])->middleware('auth')->name('main');

// Add users route requires auth
Route::apiResource('users', UserController::class)->middleware('auth');