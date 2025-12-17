<?php

namespace App\Services;

use App\Dtos\Auth\LoginDto;
use App\Dtos\Auth\RegistrationDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class AuthService
{
    public function registerUser(RegistrationDto $data)
    {
        $user = User::query()
            ->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => password_hash($data->password, PASSWORD_BCRYPT),
            ]);

        return $user;
    }

    /**
     * @throws BadRequestException
     */
    public function authenticate(LoginDto $data): array
    {
        $user = User::query()
            ->where('email', $data->email)
            ->first();

        if (!$user) {
            throw new BadRequestException('Invalid credentials supplied');
        }

        if (Auth::attempt(['email' => $data->email, 'password' => $data->password])) {
            $token = $this->generateAuthToken($user);

            return [$user, $token];
        }

        throw new BadRequestException('Invalid credentials supplied');
    }

    public function generateAuthToken(User $user): string
    {
        $user->tokens()->delete();
        $token = $user->createToken("$user->name token")->plainTextToken;
        return $token;
    }
}
