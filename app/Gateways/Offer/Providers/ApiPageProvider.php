<?php

namespace App\Gateways\Offer\Providers;

use App\Gateways\Offer\Parsing\PaginatedApiResponseParser;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Import\Config\ImportConfig;
use Generator;

/**
 * Class ApiPageProvider
 *
 * Provides an iterable way to fetch batches of items (typically offer IDs)
 * from a paginated API. It handles the logic of fetching successive pages,
 * parsing them, and yielding item batches until all pages are processed or an issue occurs.
 */
class ApiPageProvider
{
    private string $baseUrl;

    /**
     * ApiPageProvider constructor.
     *
     * @param ImportConfig $config The configuration object, used to get the base API endpoint.
     * @param IHttpClient $httpClient The HTTP client for making API requests.
     * @param PaginatedApiResponseParser $responseParser The parser for API page responses.
     */
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient,
        private PaginatedApiResponseParser $responseParser
    ) {
        $this->baseUrl = $this->config->getOffersEndpoint();
    }

    /**
     * Gets all item batches by fetching and parsing pages from the API.
     *
     * This method is a generator that yields an array of items for each successfully
     * fetched and parsed page. It continues until all pages are processed,
     * the total number of pages is unknown after the first page and no items are returned,
     * or an error occurs during page fetching.
     *
     * @return Generator<int, array<int, int|string>> Yields arrays of items (typically offer IDs).
     */
    public function getAllItemBatches(): Generator
    {
        $currentPage = 1;
        $totalPages = null;

        do {
            $pageRawData = $this->fetchPageRawData($this->baseUrl, $currentPage);
            if (is_null($pageRawData)) {
                break;
            }

            $parseResult = $this->responseParser->parse($pageRawData);

            if (is_null($totalPages)) {
                $totalPages = $parseResult->totalPages;
                if ((is_null($totalPages) && $currentPage > 1 && empty($parseResult->items)) || $totalPages === 0) {
                    break;
                }
            }
            
            if (!empty($parseResult->items)) {
                yield $parseResult->items;
            } elseif (empty($parseResult->items) && is_null($totalPages) && $currentPage > 1) {
                break;
            }

            if (!is_null($totalPages) && $currentPage >= $totalPages) {
                break; 
            }

            $currentPage++;

        } while (true);
    }

    /**
     * Fetches the raw data for a single page from the API.
     *
     * @param string $baseUrl The base URL of the API endpoint.
     * @param int $pageNumber The page number to fetch.
     * @return array<string|int, mixed>|null The decoded JSON data as an associative array if successful, otherwise null.
     */
    private function fetchPageRawData(string $baseUrl, int $pageNumber): ?array
    {
        $response = $this->httpClient->get($baseUrl, ['page' => $pageNumber]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $bodyContents = $response->getBody()->getContents();
        $data = json_decode($bodyContents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $data;
    }
}