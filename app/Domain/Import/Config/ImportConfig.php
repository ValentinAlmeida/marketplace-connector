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

    public function getOfferDetailsEndpoint(): string
    {
        return $this->importerBaseUrl . '/offers/';
    }

    public function getHubCreateEndpoint(): string
    {
        return $this->importerBaseUrl . '/hub/create-offer';
    }
}