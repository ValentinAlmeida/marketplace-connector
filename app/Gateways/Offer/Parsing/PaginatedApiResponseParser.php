<?php

namespace App\Gateways\Offer\Parsing;

use App\Gateways\Offer\Dto\PageParseResultDto;
use App\UseCase\Import\Config\ImportConfig;
use Illuminate\Support\Facades\Log;

/**
 * Class PaginatedApiResponseParser
 *
 * Responsible for parsing a single page of data from a paginated API response.
 * It extracts the total number of pages (if available) and the list of items
 * based on configuration settings.
 */
class PaginatedApiResponseParser
{
    /**
     * PaginatedApiResponseParser constructor.
     *
     * @param ImportConfig $config The configuration object containing field mappings
     * for pagination (e.g., total pages key) and item lists.
     */
    public function __construct(private ImportConfig $config) {}

    /**
     * Parses the raw data from a single API response page.
     *
     * Extracts the total number of pages and the list of items from the page data
     * according to the field mappings defined in the ImportConfig.
     * Logs warnings if expected data (like total pages or item list) is missing or malformed.
     *
     * @param array<string|int, mixed> $pageData The raw associative array data from one page of the API response.
     * @return PageParseResultDto A DTO containing the determined total number of pages (nullable)
     * and an array of extracted items.
     */
    public function parse(array $pageData): PageParseResultDto
    {
        $configFields = $this->config->fields();
        $totalPages = null;
        
        $extractedTotalPages = data_get($pageData, $configFields['paginationTotalPages']);
        if (is_numeric($extractedTotalPages)) {
            $totalPages = (int) $extractedTotalPages;
        } else {
            Log::warning("PaginatedApiResponseParser: Could not determine total pages. Key: '{$configFields['paginationTotalPages']}'. Received data: " . json_encode($pageData));
        }

        $items = data_get($pageData, $configFields['offersList'], []);
        if (!is_array($items)) {
            Log::warning("PaginatedApiResponseParser: Expected item list is not an array or is missing. Key: '{$configFields['offersList']}'. Received data: " . json_encode($pageData));
            $items = [];
        }

        return new PageParseResultDto($totalPages, $items);
    }
}