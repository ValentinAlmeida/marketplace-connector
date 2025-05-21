<?php

namespace App\Events\Import;

/**
 * Event triggered when offers have been fully fetched for an import.
 */
class OffersRetrieved
{
    /**
     * The ID of the import process.
     *
     * @var int
     */
    public int $importId;

    /**
     * The full offer data fetched for the import.
     *
     * @var array
     */
    public array $offers;

    /**
     * Create a new event instance.
     *
     * @param int $importId
     * @param array $offers
     */
    public function __construct(int $importId, array $offers)
    {
        $this->importId = $importId;
        $this->offers = $offers;
    }
}
