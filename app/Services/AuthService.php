<?php

namespace App\Services;

use App\Dtos\Auth\LoginDto;
use App\Dtos\Auth\RegistrationDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class AuthService
 *
 * Handles user authentication and registration logic.
 */
final class AuthService
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    /**
     * Register a new user.
     *
     * @param  RegistrationDto  $data
     * @return User
     */
    public function registerUser(RegistrationDto $data): User
    {
        return $this->userService->create($data);
    }

    /**
     * Authenticate a user using email and password.
     *
     * @param  LoginDto  $data
     * @return array{0: User, 1: string}  Authenticated user and access token
     *
     * @throws BadRequestException If credentials are invalid
     */
    public function authenticate(LoginDto $data): array
    {
        $user = $this->userService->fetchBy('email', $data->email);

        if (!$user) {
            throw new BadRequestException('Invalid credentials supplied');
        }

        if (Auth::attempt([
            'email' => $data->email,
            'password' => $data->password,
        ])) {
            $token = $this->generateAuthToken($user);

            return [$user, $token];
        }

        throw new BadRequestException('Invalid credentials supplied');
    }

    /**
     * Generate an authentication token for the given user.
     *
     * Ensures only a single active token exists per user
     * by revoking all previous tokens.
     *
     * @param  User  $user
     * @return string  Plain text access token
     */
    public function generateAuthToken(User $user): string
    {
        // Maintain a single session per user
        $user->tokens()->delete();

        return $user
            ->createToken("{$user->name} token")
            ->plainTextToken;
    }
}
