<?php

use App\Models\CouponCampaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

it('displays coupon campaign statistics on dashboard', function () {
    $admin = User::factory()->admin()->create();
    CouponCampaign::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Total Campaigns')
        ->assertSee('Active Campaigns')
        ->assertSee('Total Coupons')
        ->assertSee('Unused Coupons');
});

it('displays recent campaigns on dashboard', function () {
    $admin = User::factory()->admin()->create();
    $campaign = CouponCampaign::factory()->create(['title' => 'Test Dashboard Campaign']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Test Dashboard Campaign');
});

it('shows empty state when no campaigns exist', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('No campaigns found')
        ->assertSee('Create your first campaign');
});
