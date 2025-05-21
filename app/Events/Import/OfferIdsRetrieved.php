<?php

namespace App\Events\Import;

/**
 * Event triggered when offer IDs have been fetched for an import.
 */
class OfferIdsRetrieved
{
    /**
     * The ID of the import process.
     *
     * @var int
     */
    public int $importId;

    /**
     * The list of fetched offer IDs.
     *
     * @var array
     */
    public array $offerIds;

    /**
     * Create a new event instance.
     *
     * @param int $importId
     * @param array $offerIds
     */
    public function __construct(int $importId, array $offerIds)
    {
        $this->importId = $importId;
        $this->offerIds = $offerIds;
    }
}
