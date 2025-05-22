<?php

namespace App\UseCase\Contracts\Import;

/**
 * Interface IFetchSingleOfferDetails
 *
 * Defines the contract for a use case responsible for fetching the details
 * of a single, specific offer related to an import process.
 */
interface IFetchSingleOfferDetails
{
    /**
     * Executes the process of fetching details for a specific offer within an import.
     *
     * Implementations are expected to retrieve the offer details.
     * The outcome, such as the fetched data or any failure, is typically communicated
     * through events or by updating relevant entities, as this method returns void.
     *
     * @param int $importId The ID of the import process to which the offer belongs.
     * @param int $offerId The ID of the specific offer for which details are to be fetched.
     * @return void
     */
    public function execute(int $importId, int $offerId): void;
}