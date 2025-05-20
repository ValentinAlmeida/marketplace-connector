<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Config\ImportConfig;
use Illuminate\Support\Facades\Http;

class PaginatedOfferFetcher
{
    public function __construct(private ImportConfig $config){}

    public function fetch(): array
    {
        $baseUrl = $this->config->getOffersEndpoint();

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
                $lastPage = $data['pagination']['total_pages'];
            }

            $offerIds = array_merge($offerIds, $data['data']['offers']);
            $page++;
        }

        return $offerIds;
    }
}
