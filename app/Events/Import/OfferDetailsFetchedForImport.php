<?php

namespace App\Events\Import;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class OfferDetailsFetchedForImport
 *
 * Event dispatched when the details for a specific offer,
 * related to an ongoing import process, have been successfully fetched.
 * @final
 */
final class OfferDetailsFetchedForImport
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $importId The ID of the import process to which this offer belongs.
     * @param int $offerId The ID of the offer for which details were fetched.
     * @param mixed $offerData The actual data retrieved for the offer. This could be an array, object, or other data structure.
     */
    public function __construct(
        public readonly int $importId,
        public readonly int $offerId,
        public readonly mixed $offerData
    ) {}
}