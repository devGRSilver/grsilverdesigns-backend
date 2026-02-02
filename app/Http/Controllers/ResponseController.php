<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ResponseController extends Controller
{

    protected function successResponse(mixed $data = [], string $message = 'Request successful', ?string $redirect_url = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'status'   => true,
            'message'  => $message,
            'response' => $data,
        ];

        if (!is_null($redirect_url) && $redirect_url !== '') {
            $response['redirect_url'] = $redirect_url;
        }
        return response()->json($response, $statusCode);
    }


    protected function errorResponse(string $message = 'Request failed', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'status'  => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
