<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->context(fn () => [
            'url' => request()->method() . ': ' . request()->getPathInfo(),
            'body' => preg_replace(
                '/("password"| "password_confirmation"| "pin")\s*:\s*"([^"]+)"/i',
                '$1"********"',
                request()->getContent()
            ),
        ]);

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Unauthenticated. Please login to proceed',
                ], Response::HTTP_UNAUTHORIZED);
            }

            if ($e instanceof AuthorizationException || $e instanceof AccessDeniedHttpException) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ], Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Requested resource is not found',
                ], Response::HTTP_NOT_FOUND);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => $e->validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($e instanceof ThrottleRequestsException) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }

            if ($e instanceof TokenMismatchException) {
                return response()->json([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ], Response::HTTP_FORBIDDEN);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'The specified Method is not allowed for this endpoint',
                ], Response::HTTP_METHOD_NOT_ALLOWED);
            }

            logger($e);
            return response()->json([
                'status' => 'failed',
                'message' => 'Something went wrong, please try again',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        });
    })->create();
