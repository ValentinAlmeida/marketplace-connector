<?php

namespace App\Jobs\Import;

use App\UseCase\Contracts\Import\IFetchAllOfferIds;
use App\UseCase\Import\Support\HandlesImportFailures;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class FetchOfferIdsJob
 *
 * A queued job responsible for fetching all offer IDs related to a specific import process.
 * It utilizes the HandlesImportFailures trait to manage exceptions during execution.
 * @final
 */
final class FetchOfferIdsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HandlesImportFailures;

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
    public int $backoff = 120; // 2 minutes

    /**
     * Create a new job instance.
     *
     * @param int $importId The ID of the import for which offer IDs are to be fetched.
     */
    public function __construct(private readonly int $importId) {}

    /**
     * Execute the job.
     *
     * Calls the use case to fetch all offer IDs for the given import ID.
     * The execution is wrapped in a try-catch block provided by the HandlesImportFailures trait.
     *
     * @param IFetchAllOfferIds $useCase The use case implementation for fetching all offer IDs.
     * @return void
     */
    public function handle(IFetchAllOfferIds $useCase): void
    {
        $this->executeSafely($this->importId, function () use ($useCase) {
            $useCase->execute($this->importId);
        });
    }
}