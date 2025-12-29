<?php

use App\Models\Coupon;
use App\Models\CouponCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays campaign list for admin', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.coupon-campaigns.index'))
        ->assertSuccessful()
        ->assertSee($campaign->title);
});

it('denies non-admin access to campaign list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.coupon-campaigns.index'))
        ->assertForbidden();
});

it('can view create campaign form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.coupon-campaigns.create'))
        ->assertSuccessful()
        ->assertSee('Create Coupon Campaign');
});

it('can create a new campaign with auto-generated coupons', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.coupon-campaigns.store'), [
            'title' => 'Test Campaign',
            'total_amount' => 1000,
            'coupon_value' => 10,
            'expires_at' => now()->addMonth()->format('Y-m-d'),
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('coupon_campaigns', [
        'title' => 'Test Campaign',
        'total_amount' => 1000,
        'coupon_value' => 10,
        'total_coupons' => 100,
    ]);

    $campaign = CouponCampaign::where('title', 'Test Campaign')->first();
    expect($campaign->coupons()->count())->toBe(100);
});

it('validates campaign creation data', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.coupon-campaigns.store'), [
            'title' => '',
            'total_amount' => 0,
            'coupon_value' => 0,
            'expires_at' => now()->subDay()->format('Y-m-d'),
        ])
        ->assertSessionHasErrors(['title', 'total_amount', 'coupon_value', 'expires_at']);
});

it('validates coupon value cannot exceed total amount', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.coupon-campaigns.store'), [
            'title' => 'Test Campaign',
            'total_amount' => 50,
            'coupon_value' => 100,
            'expires_at' => now()->addMonth()->format('Y-m-d'),
        ])
        ->assertSessionHasErrors(['coupon_value']);
});

it('can view campaign details', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create(['title' => 'View Test Campaign']);
    $campaign->generateCoupons();

    $this->actingAs($admin)
        ->get(route('admin.coupon-campaigns.show', $campaign))
        ->assertSuccessful()
        ->assertSee('View Test Campaign')
        ->assertSee($campaign->coupons->first()->code);
});

it('can filter coupons by status', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();

    Coupon::factory()->for($campaign, 'campaign')->create(['status' => 'unused']);
    Coupon::factory()->for($campaign, 'campaign')->used()->create();
    Coupon::factory()->for($campaign, 'campaign')->expired()->create();
    Coupon::factory()->for($campaign, 'campaign')->disabled()->create();

    $this->actingAs($admin)
        ->get(route('admin.coupon-campaigns.show', ['coupon_campaign' => $campaign, 'status' => 'unused']))
        ->assertSuccessful();

    $this->actingAs($admin)
        ->get(route('admin.coupon-campaigns.show', ['coupon_campaign' => $campaign, 'status' => 'used']))
        ->assertSuccessful();
});

it('can update a campaign', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();

    $newExpirationDate = now()->addMonths(2)->format('Y-m-d');

    $this->actingAs($admin)
        ->put(route('admin.coupon-campaigns.update', $campaign), [
            'title' => 'Updated Campaign Title',
            'expires_at' => $newExpirationDate,
            'is_active' => false,
        ])
        ->assertRedirect(route('admin.coupon-campaigns.show', $campaign));

    $this->assertDatabaseHas('coupon_campaigns', [
        'id' => $campaign->id,
        'title' => 'Updated Campaign Title',
        'is_active' => false,
    ]);
});

it('can delete a campaign and its coupons', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();
    $campaign->generateCoupons();

    $couponCount = $campaign->coupons()->count();
    expect($couponCount)->toBeGreaterThan(0);

    $this->actingAs($admin)
        ->delete(route('admin.coupon-campaigns.destroy', $campaign))
        ->assertRedirect(route('admin.coupon-campaigns.index'));

    $this->assertDatabaseMissing('coupon_campaigns', ['id' => $campaign->id]);
    $this->assertDatabaseMissing('coupons', ['coupon_campaign_id' => $campaign->id]);
});

it('can disable a coupon', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create(['status' => 'unused']);

    $this->actingAs($admin)
        ->patch(route('admin.coupons.disable', $coupon))
        ->assertRedirect(route('admin.coupon-campaigns.show', $campaign));

    $this->assertDatabaseHas('coupons', [
        'id' => $coupon->id,
        'status' => 'disabled',
    ]);
});

it('can enable a disabled coupon', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->disabled()->create();

    $this->actingAs($admin)
        ->patch(route('admin.coupons.enable', $coupon))
        ->assertRedirect(route('admin.coupon-campaigns.show', $campaign));

    $this->assertDatabaseHas('coupons', [
        'id' => $coupon->id,
        'status' => 'unused',
    ]);
});

it('can delete a coupon', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create();

    $this->actingAs($admin)
        ->delete(route('admin.coupons.destroy', $coupon))
        ->assertRedirect(route('admin.coupon-campaigns.show', $campaign));

    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});

it('generates unique coupon codes', function () {
    $campaign = CouponCampaign::factory()->create([
        'total_amount' => 500,
        'coupon_value' => 10,
        'total_coupons' => 50,
    ]);

    $campaign->generateCoupons();

    $codes = $campaign->coupons()->pluck('code')->toArray();
    $uniqueCodes = array_unique($codes);

    expect(count($codes))->toBe(50);
    expect(count($uniqueCodes))->toBe(50);
});

it('marks coupon as used', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create(['status' => 'unused']);

    expect($coupon->isUsable())->toBeTrue();

    $coupon->markAsUsed($user->id);

    $coupon->refresh();

    expect($coupon->status)->toBe('used');
    expect($coupon->used_by)->toBe($user->id);
    expect($coupon->used_at)->not->toBeNull();
    expect($coupon->isUsable())->toBeFalse();
});

it('prevents reusing a used coupon', function () {
    $campaign = CouponCampaign::factory()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->used()->create();

    expect($coupon->markAsUsed())->toBeFalse();
});

it('correctly identifies expired coupons', function () {
    $campaign = CouponCampaign::factory()->create();
    $expiredCoupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'expires_at' => now()->subDay(),
        'status' => 'unused',
    ]);

    expect($expiredCoupon->isExpired())->toBeTrue();
    expect($expiredCoupon->isUsable())->toBeFalse();
});

it('updates unused coupon expiration dates when campaign is updated', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create();
    $campaign->generateCoupons();

    $unusedCoupon = $campaign->coupons()->unused()->first();
    $usedCoupon = Coupon::factory()->for($campaign, 'campaign')->used()->create();
    $originalUsedExpiry = $usedCoupon->expires_at;

    $newExpirationDate = now()->addMonths(6)->format('Y-m-d');

    $this->actingAs($admin)
        ->put(route('admin.coupon-campaigns.update', $campaign), [
            'title' => $campaign->title,
            'expires_at' => $newExpirationDate,
            'is_active' => true,
        ]);

    $unusedCoupon->refresh();
    $usedCoupon->refresh();

    expect($unusedCoupon->expires_at->format('Y-m-d'))->toBe($newExpirationDate);
    expect($usedCoupon->expires_at->format('Y-m-d H:i:s'))->toBe($originalUsedExpiry->format('Y-m-d H:i:s'));
});
