<?php

namespace App\Events\Import;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class OfferSuccessfullySentToHub
 *
 * Event dispatched when an individual offer, as part of an import process,
 * has been successfully sent to the designated external system (Hub).
 * @final
 */
final class OfferSuccessfullySentToHub
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $importId The ID of the import process to which this offer belongs.
     * @param int $originalOfferId The ID of the offer that was successfully sent to the Hub.
     */
    public function __construct(
        public readonly int $importId,
        public readonly int $originalOfferId
    ) {}
}