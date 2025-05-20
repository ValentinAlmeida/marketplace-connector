<?php

namespace App\Domain\Import\Config;

class ImportConfig
{
    public function __construct(
        private string $importerBaseUrl
    ) {}

    public function getOffersEndpoint(): string
    {
        return $this->importerBaseUrl . '/offers';
    }

    public function getOfferDetailsEndpoint(int $id): string
    {
        return "{$this->importerBaseUrl}/offers/{$id}";
    }

    public function getOfferPricesEndpoint(int $id): string
    {
        return "{$this->importerBaseUrl}/offers/{$id}/prices";
    }

    public function getOfferImagesEndpoint(int $id): string
    {
        return "{$this->importerBaseUrl}/offers/{$id}/images";
    }

    public function getHubCreateEndpoint(): string
    {
        return $this->importerBaseUrl . '/hub/create-offer';
    }

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
