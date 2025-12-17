<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function success(mixed $data, ?int $status = 200, ?array $headers = [])
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], $status, $headers);
    }

    public function ok(string $message, ?array $headers = [])
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], Response::HTTP_OK, $headers);
    }

    public function notFound(mixed $message, array $headers = [])
    {
        return response()->json([
            'status' => 'failed',
            'message' => $message,
        ], Response::HTTP_NOT_FOUND, $headers);
    }

    public function error(mixed $message, ?int $status, array $headers = [])
    {
        return response()->json([
            'status' => 'failed',
            'error' => [
                'message' => $message,
            ],
        ], $status, $headers);
    }

    public function serverError()
    {
        return response()->json([
            'status' => 'failed',
            'message' => 'Something went wrong, please try again',
        ], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
