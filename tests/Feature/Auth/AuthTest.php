<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Authentication API', function () {

    it('registers a new user', function () {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    });

    it('fails registration with missing fields', function () {
        $payload = [
            'name' => 'John Doe',
            // email missing
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertUnprocessable();
    });

    it('logs in a registered user and returns token', function () {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                    'token',
                ],
            ]);

        expect($response->json('data.user.email'))->toBe($user->email);
        expect($response->json('data.token'))->not->toBeNull();
    });

    it('fails login with wrong credentials', function () {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response
            ->assertStatus(400)
            ->assertJson([
                'status' => 'failed',
                'error' => [
                    'message' => 'Invalid credentials supplied',
                ],
            ]);
    });

    it('fails login with non-existent email', function () {
        $payload = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response
            ->assertStatus(400)
            ->assertJson([
                'status' => 'failed',
                'error' => [
                    'message' => 'Invalid credentials supplied',
                ],
            ]);
    });
});
