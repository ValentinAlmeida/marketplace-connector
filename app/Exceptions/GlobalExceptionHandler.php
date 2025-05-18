<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Illuminate\Foundation\Configuration\Exceptions;

class GlobalExceptionHandler
{
    public function register(Exceptions $exceptions): void
    {
        $exceptions->reportable(function (Throwable $e) {
        });

        $exceptions->renderable(function (Throwable $e, Request $request) {
            return $this->render($request, $e);
        });
    }

    public function render(Request $request, Throwable $e): Response|JsonResponse
    {
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException('Resource not found', $e);
        }

        if ($e instanceof HttpException) {
            return $this->renderHttpException($e);
        }

        return $this->prepareJsonResponse($request, $e);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $e->errors(),
        ], $e->status);
    }

    protected function unauthenticated(Request $request, AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }

    protected function renderHttpException(HttpException $e): JsonResponse
    {
        return response()->json([
            'message' => $e->getMessage(),
        ], $e->getStatusCode());
    }

    protected function prepareJsonResponse(Request $request, Throwable $e): JsonResponse
    {
        $status = $this->determineStatusCode($e);

        return response()->json([
            'message' => $e->getMessage() ?: 'Server Error',
            'exception' => config('app.debug') ? [
                'class' => get_class($e),
                'trace' => $e->getTrace(),
            ] : null,
        ], $status);
    }

    protected function determineStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpException) {
            return $e->getStatusCode();
        }

        return match (true) {
            $e instanceof AuthenticationException => 401,
            $e instanceof ModelNotFoundException => 404,
            default => 500
        };
    }
}