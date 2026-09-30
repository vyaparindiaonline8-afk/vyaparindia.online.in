<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Admin\CategoryController;

Route::middleware(['auth', 'is_admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('categories/merge', [CategoryController::class, 'merge'])->name('categories.merge');
    Route::resource('categories', CategoryController::class);
});
