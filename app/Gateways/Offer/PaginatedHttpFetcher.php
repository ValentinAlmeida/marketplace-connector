<?php

namespace App\Gateways\Offer;

use App\Gateways\Offer\Factories\ApiPageProviderFactory;
use App\UseCase\Contracts\Gateways\IPaginatedOfferFetcher;

/**
 * Class PaginatedHttpFetcher
 *
 * Implements the IPaginatedOfferFetcher interface to retrieve all offer IDs.
 * It utilizes an ApiPageProvider, created by a factory, to iterate through
 * batches of items (offer IDs) from a paginated source.
 */
class PaginatedHttpFetcher implements IPaginatedOfferFetcher
{
    /**
     * PaginatedHttpFetcher constructor.
     *
     * @param ApiPageProviderFactory $apiPageProviderFactory Factory used to create an instance of an API page provider.
     */
    public function __construct(
        private ApiPageProviderFactory $apiPageProviderFactory
    ) {}

    /**
     * Fetches all offer IDs by iterating through batches provided by an API page provider.
     *
     * It creates a page provider using the factory and then iterates over all item batches,
     * merging them into a single array of offer IDs.
     *
     * @return array<int, int|string> An array of all retrieved offer IDs. The IDs can be integers or strings.
     */
    public function fetch(): array
    {
        $allOfferIds = [];
        $pageProvider = $this->apiPageProviderFactory->create();

        foreach ($pageProvider->getAllItemBatches() as $itemBatch) {
            if (!empty($itemBatch) && is_array($itemBatch)) {
                $allOfferIds = array_merge($allOfferIds, $itemBatch);
            }
        }
        return $allOfferIds;
    }
}