<?php

namespace App\UseCase\Contracts\Gateways;

/**
 * Interface for fetching paginated offer IDs from an external source.
 */
interface IPaginatedOfferFetcher
{
    /**
     * Fetch all available offer IDs from a paginated API or data source.
     *
     * @return array An array of offer IDs.
     */
    public function fetch(): array;
}
