<?php

namespace App\Gateways\Offer;

use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IPaginatedOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;

class PaginatedHttpFetcher implements IPaginatedOfferFetcher
{
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient
    ) {}

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
                return [];
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
