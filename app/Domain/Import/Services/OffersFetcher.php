<?php
namespace App\Domain\Import\Services;

use App\Domain\Import\Config\ImportConfig;
use App\Domain\Offer\Dto\OfferCreateDto;
use App\Domain\Offer\Entity\Offer;
use App\Domain\Shared\ValueObjects\Reference;
use Illuminate\Support\Facades\Http;

class OffersFetcher
{
    public function __construct(private ImportConfig $config){}

    /**
     * @param array<int> $offerIds
     * @return array<Offer>
     */
    public function fetch(array $offerIds): array
    {
        $baseUrl = $this->config->getOfferDetailsEndpoint();

        return array_map(function (int $offerId) use ($baseUrl) {
            $response = Http::retry(3, 100)->get($baseUrl . $offerId);
            $responsePrices = Http::retry(3, 100)->get($baseUrl . $offerId . '/prices');
            $responseImages = Http::retry(3, 100)->get($baseUrl . $offerId . '/images');

            if (
                !$response->successful() ||
                !$responsePrices->successful() ||
                !$responseImages->successful()
            ) {
                return [];
            }

            $data = $response->json()['data'];
            $dataPrices = $responsePrices->json()['data'];
            $dataImages = $responseImages->json()['data'];

            $offerCreateDto = new OfferCreateDto(
                new Reference($data['id']),
                $data['title'],
                $data['description'],
                $data['status'],
                array_column($dataImages['images'], 'url'),
                $data['stock'],
                $dataPrices['price'],
            );

            return Offer::create($offerCreateDto);
        }, $offerIds);
    }
}
