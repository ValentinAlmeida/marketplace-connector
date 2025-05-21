<?php

namespace App\Gateways\Offer;

use App\Entities\Offer;
use App\Entities\ValueObjects\Reference;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;
use App\UseCase\Offer\Dto\OfferCreateDto;
use RuntimeException;

class HttpFetcher implements IOfferFetcher
{
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient
    ) {}

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
                throw new RuntimeException("Falha ao buscar dados da oferta ID {$offerId}");
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
