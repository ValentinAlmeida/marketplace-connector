<?php

namespace App\Domain\Import\Events;

/**
 * Event triggered when offer IDs have been fetched for an import.
 */
class OfferIdsFetched
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
