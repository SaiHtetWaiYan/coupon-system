<?php

use App\Http\Controllers\Admin\CouponCampaignController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CouponImageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified.if_required', 'admin'])->prefix('dashboard')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);

    Route::resource('coupon-campaigns', CouponCampaignController::class);
    Route::patch('coupons/{coupon}/disable', [CouponCampaignController::class, 'disableCoupon'])->name('coupons.disable');
    Route::patch('coupons/{coupon}/enable', [CouponCampaignController::class, 'enableCoupon'])->name('coupons.enable');
    Route::delete('coupons/{coupon}', [CouponCampaignController::class, 'deleteCoupon'])->name('coupons.destroy');
    Route::get('coupons/{coupon}/image', [CouponImageController::class, 'generate'])->name('coupons.image');
    Route::get('coupons/{coupon}/image/download', [CouponImageController::class, 'download'])->name('coupons.image.download');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo.upload');
    Route::delete('settings/logo', [SettingController::class, 'deleteLogo'])->name('settings.logo.delete');
});
