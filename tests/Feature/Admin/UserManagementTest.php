<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays user list for admin', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(5)->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee($users->first()->name);
});

it('can view create user form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.create'))
        ->assertSuccessful()
        ->assertSee('Create User');
});

it('can create a new user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'New Test User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_admin' => false,
        ])
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
    ]);
});

it('can view a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['name' => 'View Test User']);

    $this->actingAs($admin)
        ->get(route('admin.users.show', $user))
        ->assertSuccessful()
        ->assertSee('View Test User');
});

it('can update a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'is_admin' => false,
        ])
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

it('prevents admin from deleting themselves', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
    ]);
});

it('displays delete confirmation modal on user list page', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Delete User')
        ->assertSee('Are you sure you want to delete')
        ->assertSee('This action cannot be undone');
});

it('shows success alert after deleting a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('success');
});
