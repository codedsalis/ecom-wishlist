<?php

namespace App\Dtos\Auth;

use App\Dtos\BaseDto;

final readonly class RegistrationDto extends BaseDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {
    }
}
