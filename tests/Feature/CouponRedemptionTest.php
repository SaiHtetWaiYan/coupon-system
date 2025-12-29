<?php

use App\Models\Coupon;
use App\Models\CouponCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays the coupon redemption page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('coupons.index'))
        ->assertSuccessful()
        ->assertSee('Redeem Coupon');
});

it('requires authentication to access coupon redemption', function () {
    $this->get(route('coupons.index'))
        ->assertRedirect(route('login'));
});

it('can search for a valid coupon', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create(['is_active' => true]);
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'status' => 'unused',
        'code' => 'TESTCODE',
    ]);

    $this->actingAs($user)
        ->postJson(route('coupons.search'), ['code' => 'TESTCODE'])
        ->assertSuccessful()
        ->assertJson([
            'found' => true,
            'status' => 'valid',
            'coupon' => [
                'code' => 'TESTCODE',
            ],
        ]);
});

it('returns not found for invalid coupon code', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('coupons.search'), ['code' => 'NOTFOUND'])
        ->assertSuccessful()
        ->assertJson([
            'found' => false,
            'message' => 'Coupon not found.',
        ]);
});

it('returns already used status for used coupon', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create(['is_active' => true]);
    $coupon = Coupon::factory()->for($campaign, 'campaign')->used()->create([
        'code' => 'USEDCODE',
    ]);

    $this->actingAs($user)
        ->postJson(route('coupons.search'), ['code' => 'USEDCODE'])
        ->assertSuccessful()
        ->assertJson([
            'found' => true,
            'status' => 'already_used',
        ]);
});

it('returns expired status for expired coupon', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create(['is_active' => true]);
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'code' => 'EXPIRED1',
        'status' => 'expired',
    ]);

    $this->actingAs($user)
        ->postJson(route('coupons.search'), ['code' => 'EXPIRED1'])
        ->assertSuccessful()
        ->assertJson([
            'found' => true,
            'status' => 'expired',
        ]);
});

it('returns campaign inactive status for inactive campaign', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->inactive()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'code' => 'INACTIVE',
        'status' => 'unused',
    ]);

    $this->actingAs($user)
        ->postJson(route('coupons.search'), ['code' => 'INACTIVE'])
        ->assertSuccessful()
        ->assertJson([
            'found' => true,
            'status' => 'campaign_inactive',
        ]);
});

it('can redeem a valid coupon', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create(['is_active' => true]);
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'status' => 'unused',
        'code' => 'REDEEMME',
        'value' => 25.00,
    ]);

    $this->actingAs($user)
        ->post(route('coupons.redeem'), ['code' => 'REDEEMME'])
        ->assertRedirect()
        ->assertSessionHas('success');

    $coupon->refresh();
    expect($coupon->status)->toBe('used');
    expect($coupon->used_by)->toBe($user->id);
    expect($coupon->used_at)->not->toBeNull();
});

it('cannot redeem an already used coupon', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create(['is_active' => true]);
    $coupon = Coupon::factory()->for($campaign, 'campaign')->used()->create([
        'code' => 'ALRDUSED',
    ]);

    $this->actingAs($user)
        ->post(route('coupons.redeem'), ['code' => 'ALRDUSED'])
        ->assertRedirect()
        ->assertSessionHas('error');
});

it('cannot redeem a coupon from inactive campaign', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->inactive()->create();
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'status' => 'unused',
        'code' => 'INACTCMP',
    ]);

    $this->actingAs($user)
        ->post(route('coupons.redeem'), ['code' => 'INACTCMP'])
        ->assertRedirect()
        ->assertSessionHas('error');

    $coupon->refresh();
    expect($coupon->status)->toBe('unused');
});

it('validates coupon code format', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('coupons.redeem'), ['code' => 'SHORT'])
        ->assertSessionHasErrors(['code']);
});

it('handles case insensitive coupon codes', function () {
    $user = User::factory()->create();
    $campaign = CouponCampaign::factory()->create(['is_active' => true]);
    $coupon = Coupon::factory()->for($campaign, 'campaign')->create([
        'status' => 'unused',
        'code' => 'ABCD1234',
    ]);

    $this->actingAs($user)
        ->postJson(route('coupons.search'), ['code' => 'abcd1234'])
        ->assertSuccessful()
        ->assertJson([
            'found' => true,
            'status' => 'valid',
        ]);
});
