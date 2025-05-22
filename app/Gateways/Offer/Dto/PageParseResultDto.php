<?php

namespace App\Gateways\Offer\Dto;

/**
 * Class PageParseResultDto
 *
 * Data Transfer Object representing the result of parsing a single page
 * from a paginated API response. It holds the total number of pages (if available)
 * and the items extracted from the current page.
 * @final
 */
final class PageParseResultDto
{
    /**
     * PageParseResultDto constructor.
     *
     * @param int|null $totalPages The total number of pages in the paginated response,
     * or null if not determinable from the current page's data.
     * @param array<int, mixed> $items An array of items extracted from the current page of the response.
     * The structure of each item depends on the source API.
     */
    public function __construct(
        public readonly ?int $totalPages,
        public readonly array $items
    ) {}
}