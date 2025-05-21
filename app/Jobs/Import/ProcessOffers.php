<?php

namespace App\Jobs\Import;

use App\UseCase\Import\Process;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ImportOffersJob
 *
 * Dispatchable job responsible for handling the import of offers.
 * Executes the import logic using the ProcessImportUseCase.
 */
final class ProcessOffers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $importId The ID of the import to process.
     */
    public function __construct(private readonly int $importId) {}

    /**
     * Execute the job.
     *
     * @param ProcessImportUseCase $useCase The use case responsible for processing the import.
     * @return void
     */
    public function handle(Process $useCase): void
    {
        $useCase->execute($this->importId); 
    }
}
