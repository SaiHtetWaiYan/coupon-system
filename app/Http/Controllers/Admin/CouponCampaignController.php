<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponCampaignRequest;
use App\Http\Requests\Admin\UpdateCouponCampaignRequest;
use App\Models\Coupon;
use App\Models\CouponCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponCampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = CouponCampaign::query()
            ->withCount('coupons')
            ->latest()
            ->paginate(15);

        return view('admin.coupon-campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('admin.coupon-campaigns.create');
    }

    public function store(StoreCouponCampaignRequest $request): RedirectResponse
    {
        $campaign = CouponCampaign::query()->create([
            'title' => $request->validated('title'),
            'total_amount' => $request->validated('total_amount'),
            'coupon_value' => $request->validated('coupon_value'),
            'total_coupons' => $request->total_coupons,
            'expires_at' => $request->validated('expires_at'),
            'is_active' => true,
        ]);

        $campaign->generateCoupons();

        return redirect()->route('admin.coupon-campaigns.show', $campaign)
            ->with('success', "Campaign created successfully with {$campaign->total_coupons} coupons.");
    }

    public function show(Request $request, CouponCampaign $couponCampaign): View
    {
        $status = $request->query('status');

        $couponsQuery = $couponCampaign->coupons()->with('usedByUser')->latest();

        if ($status && in_array($status, ['unused', 'used', 'expired', 'disabled'])) {
            $couponsQuery->where('status', $status);
        }

        $coupons = $couponsQuery->paginate(20)->withQueryString();

        $statusCounts = [
            'unused' => $couponCampaign->coupons()->unused()->count(),
            'used' => $couponCampaign->coupons()->used()->count(),
            'expired' => $couponCampaign->coupons()->expired()->count(),
            'disabled' => $couponCampaign->coupons()->disabled()->count(),
        ];

        return view('admin.coupon-campaigns.show', compact('couponCampaign', 'coupons', 'status', 'statusCounts'));
    }

    public function edit(CouponCampaign $couponCampaign): View
    {
        return view('admin.coupon-campaigns.edit', compact('couponCampaign'));
    }

    public function update(UpdateCouponCampaignRequest $request, CouponCampaign $couponCampaign): RedirectResponse
    {
        $couponCampaign->update([
            'title' => $request->validated('title'),
            'expires_at' => $request->validated('expires_at'),
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->has('expires_at')) {
            $couponCampaign->coupons()
                ->where('status', 'unused')
                ->update(['expires_at' => $request->validated('expires_at')]);
        }

        return redirect()->route('admin.coupon-campaigns.show', $couponCampaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(CouponCampaign $couponCampaign): RedirectResponse
    {
        $couponCampaign->delete();

        return redirect()->route('admin.coupon-campaigns.index')
            ->with('success', 'Campaign deleted successfully.');
    }

    public function disableCoupon(Coupon $coupon): RedirectResponse
    {
        $coupon->disable();

        return redirect()->route('admin.coupon-campaigns.show', $coupon->coupon_campaign_id)
            ->with('success', 'Coupon disabled successfully.');
    }

    public function enableCoupon(Coupon $coupon): RedirectResponse
    {
        $coupon->enable();

        return redirect()->route('admin.coupon-campaigns.show', $coupon->coupon_campaign_id)
            ->with('success', 'Coupon enabled successfully.');
    }

    public function deleteCoupon(Coupon $coupon): RedirectResponse
    {
        $campaignId = $coupon->coupon_campaign_id;
        $coupon->delete();

        return redirect()->route('admin.coupon-campaigns.show', $campaignId)
            ->with('success', 'Coupon deleted successfully.');
    }
}
