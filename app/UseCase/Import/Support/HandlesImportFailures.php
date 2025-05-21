<?php

namespace App\UseCase\Import\Support;

use App\UseCase\Contracts\Import\IImportProcessor;
use Closure;
use Throwable;

trait HandlesImportFailures
{
    /**
     * Execute a given callback and mark import as failed on exception.
     *
     * @param int $importId
     * @param Closure $callback
     * @return void
     */
    protected function executeSafely(int $importId, Closure $callback): void
    {
        try {
            $callback();
        } catch (Throwable $e) {
            $importService = app(IImportProcessor::class);
            $import = $importService->findImport($importId);
            $import->fail($e->getMessage());
            $importService->updateImport($import);
        }
    }
}
