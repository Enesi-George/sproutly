<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\assertDatabaseHas;

test('can register a new user', function () {
    $response = $this->postJson(route('register'), [
        'name' => 'Logic gracefully',
        'email' => 'logically@gmail.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $response->assertStatus(201);

    $userId = $response->json('data.user.id');

    assertDatabaseHas('users', [
        'name' => 'Logic gracefully',
        'email' => 'logically@gmail.com',
    ]);

    assertDatabaseHas('wallets', [
        'balance' => 0,
        'user_id' => $userId
    ]);
});


test('reject login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'logically@gmail.com',
        'password' => Hash::make('Password123'),
    ]);

    $response = $this->postJson(route('login'), [
        'email' => 'logically@gmail.com',
        'password' => 'invalid-password',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['email']);
});

test('throttled login after too many attempts', function () {
    $user = User::factory()->create([
        'email' => 'logically@gmail.com',
        'password' => Hash::make('Password123'),
    ]);

    for ($i = 0; $i < 6; $i++) {
        $this->postJson(route('login'), [
            'email' => 'throttle@example.com',
            'password' => 'invalid-password',
        ]);
    }
    $response =  $this->postJson(route('login'), [
        'email' => 'throttle@example.com',
        'password' => 'invalid-password',
    ]);

    $response->assertStatus(429);
});

test('can logout the authenticated user', function () {
    $user = User::Factory()->create([
        'email' => 'logically@gmail.com',
        'password' => Hash::make('Password123'),
    ]);
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->postJson(route('logout'));

    $response->assertStatus(200);

    expect($user->tokens()->count())->toBe(0);
});
