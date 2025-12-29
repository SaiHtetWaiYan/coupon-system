<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays settings page for admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertSuccessful()
        ->assertSee('Settings');
});

it('displays existing settings', function () {
    $admin = User::factory()->admin()->create();
    Setting::setValue('site_name', 'Test Site');

    $this->actingAs($admin)
        ->get(route('admin.settings.index'))
        ->assertSuccessful()
        ->assertSee('Test Site');
});

it('can update settings', function () {
    $admin = User::factory()->admin()->create();
    Setting::setValue('site_name', 'Old Name');

    $this->actingAs($admin)
        ->put(route('admin.settings.update'), [
            'settings' => [
                'site_name' => 'New Site Name',
            ],
        ])
        ->assertRedirect(route('admin.settings.index'))
        ->assertSessionHas('success');

    expect(Setting::getValue('site_name'))->toBe('New Site Name');
});
