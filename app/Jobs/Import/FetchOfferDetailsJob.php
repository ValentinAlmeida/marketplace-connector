<?php

namespace App\Jobs\Import;

use App\UseCase\Contracts\Import\IFetchSingleOfferDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class FetchOfferDetailsJob
 *
 * A queued job responsible for fetching the details of a single offer
 * as part of an import process.
 * @final
 */
final class FetchOfferDetailsJob implements ShouldQueue
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
     * @param int $importId The ID of the import process.
     * @param int $offerId The ID of the offer for which to fetch details.
     */
    public function __construct(
        private readonly int $importId,
        private readonly int $offerId
    ) {}

    /**
     * Execute the job.
     *
     * Calls the use case responsible for fetching single offer details.
     * Logs an error and re-throws the exception if any part of the use case execution fails.
     *
     * @param IFetchSingleOfferDetails $useCase The use case implementation for fetching offer details.
     * @return void
     * @throws \Throwable If an error occurs during use case execution.
     */
    public function handle(IFetchSingleOfferDetails $useCase): void
    {
        try {
            $useCase->execute($this->importId, $this->offerId);
        } catch (\Throwable $e) {
            Log::error("FetchOfferDetailsJob: Uncaught exception while executing IFetchSingleOfferDetails for importId {$this->importId}, offerId {$this->offerId}. Error: {$e->getMessage()}");
            throw $e;
        }
    }
}