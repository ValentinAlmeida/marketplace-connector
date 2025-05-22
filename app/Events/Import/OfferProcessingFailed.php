<?php

namespace App\Events\Import;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class OfferProcessingFailed
 *
 * Event dispatched when the processing of an individual offer,
 * as part of an import, encounters a failure.
 * @final
 */
final class OfferProcessingFailed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $importId The ID of the import process during which the offer processing failed.
     * @param int $originalOfferId The ID of the specific offer that failed to process.
     * @param string $failureStage A descriptor for the stage at which the failure occurred (e.g., 'details_fetching', 'validation', 'saving').
     * @param string $reason A message or code explaining the reason for the processing failure.
     */
    public function __construct(
        public readonly int $importId,
        public readonly int $originalOfferId,
        public readonly string $failureStage,
        public readonly string $reason
    ) {}
}