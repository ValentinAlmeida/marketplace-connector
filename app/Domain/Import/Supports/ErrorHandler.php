<?php

namespace App\Domain\Import\Supports;

use Illuminate\Support\Facades\Log;

class ErrorHandler
{
    public function logAndThrow(string $message, \Throwable $e, array $context = []): void
    {
        Log::error('ErrorHandler::logAndThrow - ' . $message, array_merge($context, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]));
        
        throw $e;
    }
}