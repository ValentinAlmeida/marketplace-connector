<?php

namespace App\Jobs\Import;

use App\UseCase\Contracts\Import\ISendSingleOfferToHub;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SendOfferToHubJob
 *
 * A queued job responsible for sending the data of a single offer to an external system (Hub)
 * as part of an import process.
 * @final
 */
final class SendOfferToHubJob implements ShouldQueue
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
    public int $backoff = 180; // 3 minutes

    /**
     * Create a new job instance.
     *
     * @param int $importId The ID of the import process.
     * @param int $originalOfferId The original ID of the offer being sent.
     * @param mixed $offerData The data payload of the offer to be sent.
     */
    public function __construct(
        private readonly int $importId,
        private readonly int $originalOfferId,
        private mixed $offerData
    ) {}

    /**
     * Execute the job.
     *
     * Calls the use case responsible for sending the offer data to the Hub.
     * Logs an error and re-throws the exception if the use case execution fails.
     *
     * @param ISendSingleOfferToHub $useCase The use case implementation for sending a single offer.
     * @return void
     * @throws \Throwable If an error occurs during use case execution.
     */
    public function handle(ISendSingleOfferToHub $useCase): void
    {
         try {
            $useCase->execute($this->importId, $this->originalOfferId, $this->offerData);
        } catch (\Throwable $e) {
            Log::error("SendOfferToHubJob: Uncaught exception while executing ISendSingleOfferToHub for importId {$this->importId}, offerId {$this->originalOfferId}. Error: {$e->getMessage()}");
            throw $e;
        }
    }
}