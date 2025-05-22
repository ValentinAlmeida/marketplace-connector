<?php

namespace App\Gateways\Offer;

use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IPaginatedOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;

/**
 * Class PaginatedHttpFetcher
 *
 * Implements the IPaginatedOfferFetcher interface to retrieve all offer IDs
 * by iterating through paginated responses from an HTTP API.
 */
class PaginatedHttpFetcher implements IPaginatedOfferFetcher
{
    /**
     * PaginatedHttpFetcher constructor.
     *
     * @param ImportConfig $config The configuration for import processes, including the offers endpoint and field mappings.
     * @param IHttpClient $httpClient The HTTP client used for making requests.
     */
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient
    ) {}

    /**
     * Fetches all offer IDs from a paginated API endpoint.
     *
     * It iterates through pages of results until all offer IDs are collected.
     * If an HTTP request for a page fails (non-200 status), it returns any IDs collected up to that point.
     *
     * @return array<int, int|string> An array of all retrieved offer IDs. The IDs can be integers or strings.
     */
    public function fetch(): array
    {
        $baseUrl = $this->config->getOffersEndpoint();
        $fields = $this->config->fields();

        $page = 1;
        $lastPage = null;
        $offerIds = [];

        while (is_null($lastPage) || $page <= $lastPage) {
            $response = $this->httpClient->get($baseUrl, ['page' => $page]);

            if ($response->getStatusCode() !== 200) {
                return $offerIds; 
            }

            $data = json_decode($response->getBody(), true);

            if (is_null($lastPage)) {
                $lastPage = data_get($data, $fields['paginationTotalPages']);
            }

            $ids = data_get($data, $fields['offersList'], []);
            $offerIds = array_merge($offerIds, $ids);

            $page++;
        }

        return $offerIds;
    }
}