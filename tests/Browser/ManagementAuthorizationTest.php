<?php

it('redirects a logged out visitor from organisation management to login', function () {
    $page = visit('/manage/organizations');

    $page->assertPathIs('/login')
        ->assertSee('Login');

    sleep(5);
});

it('forbids a registered user from organisation management', function () {
    $user = \App\Models\User::factory()->create([
        'name' => 'Registered Test User',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
        'role' => \App\Models\User::ROLE_REGISTERED_USER,
        'organization_id' => null,
        'is_active' => true,
        'must_change_password' => false,
    ]);

    expect(
        \App\Models\User::where('email', $user->email)->exists()
    )->toBeTrue();

    expect(
        \Illuminate\Support\Facades\Hash::check('password', $user->password)
    )->toBeTrue();

    $page = visit('/login');

    $page->fill('email', $user->email)
    ->fill('password', 'password')
    ->submit();

    $page->assertPathIs('/user/dashboard');

    $page->navigate('/manage/organizations');

    $page->assertSee('403');

    sleep(5);
});