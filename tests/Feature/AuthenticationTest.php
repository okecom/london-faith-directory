<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can log in a registered user through Fortify', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_REGISTERED_USER,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
        'password' => 'password',
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect('/user/dashboard');

    $this->assertAuthenticatedAs($user);
});