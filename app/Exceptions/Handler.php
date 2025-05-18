<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): Response
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    protected function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        $statusCode = $this->getStatusCode($exception);
        $response = [
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode() ?: $statusCode,
            ]
        ];

        if (config('app.debug')) {
            $response['error']['trace'] = $exception->getTrace();
        }

        return response()->json($response, $statusCode);
    }

    protected function getStatusCode(Throwable $exception): int
    {
        return match (true) {
            $exception instanceof \DomainException => 400,
            $exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
            default => 500
        };
    }
}