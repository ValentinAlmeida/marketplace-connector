<?php

namespace App\Gateways\Offer;

use App\Entities\Offer;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;
use App\UseCase\Mappers\OfferDataMapper;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class HttpFetcher implements IOfferFetcher
{
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient,
        private OfferDataMapper $offerDataMapper
    ) {}

    public function fetch(array $offerIds): array
    {
        return array_map(fn(int $offerId) => $this->fetchSingleOffer($offerId), $offerIds);
    }

    private function fetchSingleOffer(int $offerId): Offer
    {
        $detailsData = $this->fetchJsonData(
            $this->config->getOfferDetailsEndpoint($offerId),
            "detalhes da oferta ID {$offerId}"
        );

        $pricesData = $this->fetchJsonData(
            $this->config->getOfferPricesEndpoint($offerId),
            "preços da oferta ID {$offerId}"
        );

        $imagesData = $this->fetchJsonData(
            $this->config->getOfferImagesEndpoint($offerId),
            "imagens da oferta ID {$offerId}"
        );
        
        $offerDto = $this->offerDataMapper->mapToDto($detailsData, $pricesData, $imagesData, $offerId);
        
        return Offer::create($offerDto);
    }

    private function fetchJsonData(string $url, string $dataTypeForErrorMessage): array
    {
        $response = $this->httpClient->get($url);
        $this->ensureSuccessfulResponse($response, $dataTypeForErrorMessage);
        
        $data = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Falha ao decodificar JSON para {$dataTypeForErrorMessage}. Erro: " . json_last_error_msg());
        }
        return $data;
    }

    private function ensureSuccessfulResponse(ResponseInterface $response, string $context): void
    {
        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException("Falha ao buscar {$context}. Status: {$response->getStatusCode()}");
        }
    }
}