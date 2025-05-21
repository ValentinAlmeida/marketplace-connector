<?php

namespace App\Gateways\Offer;

use App\Entities\Offer;
use App\Entities\ValueObjects\Reference;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Import\Config\ImportConfig;
use App\UseCase\Offer\Dto\OfferCreateDto;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpFetcher implements IOfferFetcher
{
    public function __construct(private ImportConfig $config) {}

    /**
     * @param array<int> $offerIds
     * @return array<Offer>
     */
    public function fetch(array $offerIds): array
    {
        return array_map(function (int $offerId) {
            $response = Http::retry(3, 100)->get($this->config->getOfferDetailsEndpoint($offerId));
            $responsePrices = Http::retry(3, 100)->get($this->config->getOfferPricesEndpoint($offerId));
            $responseImages = Http::retry(3, 100)->get($this->config->getOfferImagesEndpoint($offerId));

            if (
                !$response->successful() ||
                !$responsePrices->successful() ||
                !$responseImages->successful()
            ) {
                throw new RuntimeException("Falha ao buscar dados da oferta ID {$offerId}");
            }

            $data = data_get($response->json(), $this->config->fields()['offerData']);
            $dataPrices = $responsePrices->json()['data'];
            $dataImages = $responseImages->json()['data'];

            $images = array_column($dataImages[$this->config->fields()['imagesList']], 'url');
            $price = $dataPrices[$this->config->fields()['price']];

            $offerCreateDto = new OfferCreateDto(
                new Reference($data['id']),
                $data['title'],
                $data['description'],
                $data['status'],
                $images,
                $data['stock'],
                $price,
            );

            return Offer::create($offerCreateDto);
        }, $offerIds);
    }
}
