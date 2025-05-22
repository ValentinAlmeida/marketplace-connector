<?php

namespace App\Gateways\Offer;

use App\Entities\Offer;
use App\Entities\ValueObjects\Reference;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;
use App\UseCase\Offer\Dto\OfferCreateDto;
use RuntimeException;

/**
 * Class HttpFetcher
 *
 * Implements the IOfferFetcher interface to retrieve offer data from multiple HTTP endpoints.
 * It fetches offer details, prices, and images, then constructs Offer entities.
 */
class HttpFetcher implements IOfferFetcher
{
    /**
     * HttpFetcher constructor.
     *
     * @param ImportConfig $config The configuration for import processes, containing endpoint URLs and field mappings.
     * @param IHttpClient $httpClient The HTTP client used for making requests.
     */
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient
    ) {}

    /**
     * Fetches data for a list of offer IDs and returns an array of Offer entities.
     *
     * For each offer ID, it makes separate HTTP requests to fetch general details,
     * prices, and images. It then aggregates this data to create an Offer entity.
     *
     * @param array<int, int> $offerIds An array of offer IDs to fetch.
     * @return array<int, Offer> An array of Offer entities.
     * @throws RuntimeException If any HTTP request fails or if essential data is missing from the responses.
     */
    public function fetch(array $offerIds): array
    {
        return array_map(function (int $offerId) {
            $response = $this->httpClient->get($this->config->getOfferDetailsEndpoint($offerId));
            $responsePrices = $this->httpClient->get($this->config->getOfferPricesEndpoint($offerId));
            $responseImages = $this->httpClient->get($this->config->getOfferImagesEndpoint($offerId));

            if (
                $response->getStatusCode() !== 200 ||
                $responsePrices->getStatusCode() !== 200 ||
                $responseImages->getStatusCode() !== 200
            ) {
                throw new RuntimeException("Failed to fetch data for offer ID {$offerId}");
            }

            $data = data_get(json_decode($response->getBody(), true), $this->config->fields()['offerData']);
            $dataPrices = json_decode($responsePrices->getBody(), true)['data'];
            $dataImages = json_decode($responseImages->getBody(), true)['data'];

            $images = array_column($dataImages[$this->config->fields()['imagesList']], 'url');
            $price = $dataPrices[$this->config->fields()['price']];

            return Offer::create(new OfferCreateDto(
                new Reference($data['id']),
                $data['title'],
                $data['description'],
                $data['status'],
                $images,
                $data['stock'],
                $price,
            ));
        }, $offerIds);
    }
}