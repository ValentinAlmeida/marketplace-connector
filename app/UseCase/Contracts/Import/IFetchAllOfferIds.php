<?php

namespace App\UseCase\Contracts\Import;

/**
 * Interface IFetchAllOfferIds
 *
 * Defines the contract for a use case responsible for fetching all offer IDs
 * associated with a specific import process.
 */
interface IFetchAllOfferIds
{
    /**
     * Executes the process of fetching all offer IDs for a given import.
     *
     * Implementations of this method are expected to retrieve the offer IDs
     * and typically trigger subsequent actions, such as dispatching events or jobs
     * with these IDs, as the method itself returns void.
     *
     * @param int $importId The ID of the import for which to fetch all offer IDs.
     * @return void
     */
    public function execute(int $importId): void;
}