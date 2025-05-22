<?php

namespace App\Jobs\Import;

use App\UseCase\Contracts\Import\IInitiateImportProcessing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessImportJob
 *
 * A queued job responsible for initiating the main processing logic for a given import.
 * This job typically orchestrates the various steps involved in processing an import.
 * @final
 */
final class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     *
     * @param int $importId The ID of the import to be processed.
     */
    public function __construct(private readonly int $importId) {}

    /**
     * Execute the job.
     *
     * Calls the use case responsible for initiating the import processing.
     * Logs a critical error and re-throws the exception if the use case execution fails.
     *
     * @param IInitiateImportProcessing $useCase The use case implementation for initiating import processing.
     * @return void
     * @throws \Throwable If a critical error occurs during use case execution.
     */
    public function handle(IInitiateImportProcessing $useCase): void
    {
        try {
            $useCase->execute($this->importId);
        } catch (\Throwable $e) {
            Log::critical("ProcessImportJob: Critical unrecoverable failure while executing InitiateImportProcessingUseCase for importId {$this->importId}: {$e->getMessage()}");
            throw $e;
        }
    }
}