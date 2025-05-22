<?php

namespace App\Events\Import;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class OfferIdsRetrievedForImport
 *
 * Event dispatched when a list of offer IDs has been successfully retrieved
 * as part of an import process.
 * @final
 */
final class OfferIdsRetrievedForImport
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $importId The ID of the import process for which the offer IDs were retrieved.
     * @param array $offerIds An array of offer IDs that have been retrieved.
     */
    public function __construct(
        public readonly int $importId,
        public readonly array $offerIds
    ) {}
}