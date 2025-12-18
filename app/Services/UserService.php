<?php

namespace App\Services;

use App\Dtos\Auth\RegistrationDto;
use App\Models\User;

/**
 * Class UserService
 *
 * Handles user persistence and retrieval logic.
 */
final class UserService
{
    /**
     * Create a new user from registration data.
     *
     * @param  RegistrationDto  $data
     * @return User
     */
    public function create(RegistrationDto $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);
    }

    /**
     * Fetch a user by a given column and value.
     *
     * @param string $column
     * @param mixed $data  Value to match
     * @return User|null
     */
    public function fetchBy(string $column, mixed $value): ?User
    {
        return User::query()
            ->where($column, $value)
            ->first();
    }
}
