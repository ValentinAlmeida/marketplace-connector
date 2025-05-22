<?php

namespace App\UseCase\Contracts\Gateways;

/**
 * Interface IOfferFetcher
 *
 * Defines the contract for services responsible for fetching detailed information
 * for a given list of offer IDs.
 */
interface IOfferFetcher
{
    /**
     * Fetches detailed information for a specified list of offer IDs.
     *
     * Implementations should retrieve the data for each offer ID and typically
     * return an array of structured offer data, often as domain entities.
     *
     * @param array<int, int|string> $offerIds An array of offer IDs for which to fetch details.
     * @return array<int, \App\Entities\Offer> An array of Offer entities,
     * corresponding to the provided offer IDs.
     */
    public function fetch(array $offerIds): array;
}