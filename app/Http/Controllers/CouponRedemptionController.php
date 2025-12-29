<?php

namespace App\Http\Controllers;

use App\Events\CouponRedeemed;
use App\Http\Requests\RedeemCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponRedemptionController extends Controller
{
    public function index(): View
    {
        return view('coupons.redeem');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:8'],
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (! $coupon) {
            return response()->json([
                'found' => false,
                'message' => 'Coupon not found.',
            ]);
        }

        $status = match (true) {
            $coupon->status === 'used' => 'already_used',
            $coupon->status === 'disabled' => 'disabled',
            $coupon->status === 'expired' || $coupon->isExpired() => 'expired',
            ! $coupon->campaign->is_active => 'campaign_inactive',
            default => 'valid',
        };

        return response()->json([
            'found' => true,
            'status' => $status,
            'coupon' => [
                'code' => $coupon->code,
                'value' => $coupon->value,
                'expires_at' => $coupon->expires_at->format('M d, Y'),
                'campaign' => $coupon->campaign->title,
            ],
            'message' => match ($status) {
                'valid' => 'Coupon is valid and ready to use!',
                'already_used' => 'This coupon has already been used.',
                'disabled' => 'This coupon has been disabled.',
                'expired' => 'This coupon has expired.',
                'campaign_inactive' => 'This campaign is no longer active.',
            },
        ]);
    }

    public function redeem(RedeemCouponRequest $request): RedirectResponse
    {
        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (! $coupon) {
            return back()->with('error', 'Coupon not found.');
        }

        if (! $coupon->isUsable()) {
            return back()->with('error', 'This coupon cannot be redeemed.');
        }

        if (! $coupon->campaign->is_active) {
            return back()->with('error', 'This campaign is no longer active.');
        }

        $coupon->markAsUsed(auth()->id());

        CouponRedeemed::dispatch(
            $coupon->fresh(),
            auth()->user()->name,
            auth()->user()->email
        );

        return back()->with('success', "Coupon redeemed successfully! You received \${$coupon->value} discount.");
    }
}
