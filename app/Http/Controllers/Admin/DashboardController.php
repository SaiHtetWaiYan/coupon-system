<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponCampaign;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_campaigns' => CouponCampaign::query()->count(),
            'active_campaigns' => CouponCampaign::query()->where('is_active', true)->where('expires_at', '>', now())->count(),
            'total_coupons' => Coupon::query()->count(),
            'unused_coupons' => Coupon::query()->unused()->count(),
            'used_coupons' => Coupon::query()->used()->count(),
            'expired_coupons' => Coupon::query()->expired()->count(),
            'total_campaign_value' => CouponCampaign::query()->sum('total_amount'),
            'redeemed_value' => Coupon::query()->used()->sum('value'),
        ];

        $recentCampaigns = CouponCampaign::query()
            ->withCount(['coupons', 'coupons as used_coupons_count' => fn ($q) => $q->used()])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentCampaigns'));
    }
}
