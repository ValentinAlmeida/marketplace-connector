<?php

namespace App\UseCase\Import\Config;

/**
 * Class ImportConfig
 *
 * Provides configuration and endpoints for interacting with the external importer API.
 */
class ImportConfig
{
    /**
     * @param string $importerBaseUrl Base URL of the importer service.
     */
    public function __construct(
        private string $importerBaseUrl
    ) {}

    /**
     * Get the endpoint for listing offers.
     *
     * @return string
     */
    public function getOffersEndpoint(): string
    {
        return $this->importerBaseUrl . '/offers';
    }

    /**
     * Get the endpoint for retrieving details of a specific offer.
     *
     * @param int $id Offer ID
     * @return string
     */
    public function getOfferDetailsEndpoint(int $id): string
    {
        return "{$this->importerBaseUrl}/offers/{$id}";
    }

    /**
     * Get the endpoint for retrieving prices of a specific offer.
     *
     * @param int $id Offer ID
     * @return string
     */
    public function getOfferPricesEndpoint(int $id): string
    {
        return "{$this->importerBaseUrl}/offers/{$id}/prices";
    }

    /**
     * Get the endpoint for retrieving images of a specific offer.
     *
     * @param int $id Offer ID
     * @return string
     */
    public function getOfferImagesEndpoint(int $id): string
    {
        return "{$this->importerBaseUrl}/offers/{$id}/images";
    }

    /**
     * Get the endpoint for creating an offer in the hub.
     *
     * @return string
     */
    public function getHubCreateEndpoint(): string
    {
        return $this->importerBaseUrl . '/hub/create-offer';
    }

    /**
     * Get field mappings used to parse the importer's response.
     *
     * @return array<string, string> Associative array mapping internal keys to response paths.
     */
    public function fields(): array
    {
        return [
            'paginationTotalPages' => 'pagination.total_pages',
            'offersList' => 'data.offers',
            'offerData' => 'data',
            'imagesList' => 'images',
            'price' => 'price',
        ];
    }
}
