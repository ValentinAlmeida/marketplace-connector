<?php

namespace App\Infrastructure\Import\Services;

use App\Domain\Import\Config\ImportConfig;
use App\Domain\Import\Services\OffersFetcherInterface;
use App\Domain\Offer\Dto\OfferCreateDto;
use App\Domain\Offer\Entity\Offer;
use App\Domain\Shared\ValueObjects\Reference;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpOffersFetcher implements OffersFetcherInterface
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
