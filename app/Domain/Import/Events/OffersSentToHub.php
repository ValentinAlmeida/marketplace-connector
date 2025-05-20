<?php

namespace App\Domain\Import\Events;

/**
 * Event triggered when all offers are successfully sent to the Hub.
 */
class OffersSentToHub
{
    /**
     * The ID of the import process.
     *
     * @var int
     */
    public int $importId;

    /**
     * Create a new event instance.
     *
     * @param int $importId
     */
    public function __construct(int $importId)
    {
        $this->importId = $importId;
    }
}
