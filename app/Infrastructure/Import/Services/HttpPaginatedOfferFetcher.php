<?php

namespace App\Infrastructure\Import\Services;

use App\Domain\Import\Config\ImportConfig;
use App\Domain\Import\Services\PaginatedOfferFetcherInterface;
use Illuminate\Support\Facades\Http;

class HttpPaginatedOfferFetcher implements PaginatedOfferFetcherInterface
{
    public function __construct(private ImportConfig $config) {}

    public function fetch(): array
    {
        $baseUrl = $this->config->getOffersEndpoint();
        $fields = $this->config->fields();

        $page = 1;
        $lastPage = null;
        $offerIds = [];

        while (is_null($lastPage) || $page <= $lastPage) {
            $response = Http::retry(3, 100)->get($baseUrl, ['page' => $page]);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();

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
