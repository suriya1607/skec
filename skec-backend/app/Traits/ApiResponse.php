<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    protected function successList(mixed $data, mixed $meta = null, string $message = 'Success'): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        if ($meta) {
            $response['meta'] = $meta;
        }

        return response()->json($response, 200);
    }

    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function error(string $message = 'Error', string $error = 'error', int $code = 400, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'error' => $error,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, 'not_found', 404);
    }

    protected function unauthorized(string $message = 'Unauthorized', string $error = 'unauthorized'): JsonResponse
    {
        return $this->error($message, $error, 401);
    }

    protected function forbidden(string $message = 'Forbidden', string $error = 'forbidden'): JsonResponse
    {
        return $this->error($message, $error, 403);
    }

    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'validation_error',
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    protected function paginatedResponse($paginator, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'message' => $message,
        ], 200);
    }
}
