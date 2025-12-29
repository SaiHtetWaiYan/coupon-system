<?php

use App\Http\Controllers\CouponRedemptionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/coupons', [CouponRedemptionController::class, 'index'])->name('coupons.index');
    Route::post('/coupons/search', [CouponRedemptionController::class, 'search'])->name('coupons.search');
    Route::post('/coupons/redeem', [CouponRedemptionController::class, 'redeem'])->name('coupons.redeem');
});

require __DIR__.'/auth.php';
