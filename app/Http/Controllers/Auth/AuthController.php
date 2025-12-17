<?php

namespace App\Http\Controllers\Auth;

use App\Dtos\Auth\LoginDto;
use App\Dtos\Auth\RegistrationDto;
use App\Http\Controllers\ApiController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[Group('Authentication')]
class AuthController extends ApiController
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * Register a new user account
     * 
     * @response: array{status: success, data: UserResource}
     */
    public function register(RegistrationRequest $request)
    {
        $validated = RegistrationDto::fromArray($request->validated());

        $user = $this->authService->registerUser($validated);

        return $this->success(new UserResource($user), Response::HTTP_CREATED);
    }

    /**
     * Authenticate a user
     * 
     * @response: array{status: success, data: array{user: UserResource, token: <string>}}
     */
    public function login(LoginRequest $request)
    {
        $validated = LoginDto::fromArray($request->validated());

        try {
            [$user, $token] = $this->authService->authenticate($validated);

            return $this->success([
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof BadRequestException) {
                return $this->error($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }

            logger($e);
            return $this->serverError();
        }
    }
}
