<?php

namespace App\Domain\Import\Services;

/**
 * Interface for fetching paginated offer IDs from an external source.
 */
interface PaginatedOfferFetcherInterface
{
    /**
     * Fetch all available offer IDs from a paginated API or data source.
     *
     * @return array An array of offer IDs.
     */
    public function fetch(): array;
}
