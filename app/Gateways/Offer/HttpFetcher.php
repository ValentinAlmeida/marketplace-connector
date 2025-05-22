<?php

namespace App\Gateways\Offer;

use App\Entities\Offer;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;
use App\UseCase\Mappers\OfferDataMapper;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class HttpFetcher
 *
 * Implements the IOfferFetcher interface to retrieve offer data from multiple HTTP endpoints.
 * It fetches offer details, prices, and images, then uses a mapper to construct Offer entities.
 */
class HttpFetcher implements IOfferFetcher
{
    /**
     * HttpFetcher constructor.
     *
     * @param ImportConfig $config The configuration for import processes, including endpoint URLs and field mappings.
     * @param IHttpClient $httpClient The HTTP client used for making requests.
     * @param OfferDataMapper $offerDataMapper Mapper to convert raw fetched data into an OfferCreateDto.
     */
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient,
        private OfferDataMapper $offerDataMapper
    ) {}

    /**
     * Fetches data for a list of offer IDs and returns an array of Offer entities.
     *
     * @param array<int, int> $offerIds An array of offer IDs to fetch.
     * @return array<int, Offer> An array of Offer entities.
     * @throws RuntimeException If fetching data for any offer fails.
     */
    public function fetch(array $offerIds): array
    {
        return array_map(fn(int $offerId): Offer => $this->fetchSingleOffer($offerId), $offerIds);
    }

    /**
     * Fetches all necessary data for a single offer (details, prices, images) and creates an Offer entity.
     *
     * @param int $offerId The ID of the offer to fetch.
     * @return Offer The constructed Offer entity.
     * @throws RuntimeException If any part of the data fetching or processing fails for the offer.
     */
    private function fetchSingleOffer(int $offerId): Offer
    {
        $detailsData = $this->fetchJsonData(
            $this->config->getOfferDetailsEndpoint($offerId),
            "details for offer ID {$offerId}"
        );

        $pricesData = $this->fetchJsonData(
            $this->config->getOfferPricesEndpoint($offerId),
            "prices for offer ID {$offerId}"
        );

        $imagesData = $this->fetchJsonData(
            $this->config->getOfferImagesEndpoint($offerId),
            "images for offer ID {$offerId}"
        );
        
        $offerDto = $this->offerDataMapper->mapToDto($detailsData, $pricesData, $imagesData, $offerId);
        
        return Offer::create($offerDto);
    }

    /**
     * Fetches JSON data from a given URL and decodes it.
     *
     * @param string $url The URL to fetch data from.
     * @param string $dataTypeForErrorMessage A descriptive string for the type of data being fetched, used in error messages.
     * @return array<string|int, mixed> The decoded JSON data as an associative array.
     * @throws RuntimeException If the HTTP request fails, the response is not successful, or JSON decoding fails.
     */
    private function fetchJsonData(string $url, string $dataTypeForErrorMessage): array
    {
        $response = $this->httpClient->get($url);
        $this->ensureSuccessfulResponse($response, $dataTypeForErrorMessage);
        
        $data = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Failed to decode JSON for {$dataTypeForErrorMessage}. Error: " . json_last_error_msg());
        }
        return $data;
    }

    /**
     * Ensures that the HTTP response has a successful (200 OK) status code.
     *
     * @param ResponseInterface $response The HTTP response to check.
     * @param string $context A descriptive string for the context of the request, used in error messages.
     * @return void
     * @throws RuntimeException If the response status code is not 200.
     */
    private function ensureSuccessfulResponse(ResponseInterface $response, string $context): void
    {
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException("Failed to fetch {$context}. Status: {$response->getStatusCode()}");
        }
    }
}