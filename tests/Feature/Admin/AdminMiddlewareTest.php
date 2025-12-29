<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests from admin routes', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

it('denies non-admin users access to dashboard', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('denies non-admin users access to admin-only routes', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.settings.index'))
        ->assertForbidden();
});

it('allows admin users to access admin routes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful();
});
