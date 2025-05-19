<?php
namespace App\Domain\Import\Services;

use App\Domain\Offer\Dto\OfferCreateDto;
use App\Domain\Offer\Entity\Offer;
use App\Domain\Shared\ValueObjects\Reference;
use Illuminate\Support\Facades\Http;

class OffersFetcher
{
    protected string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array<int> $offerIds
     * @return array<Offer>
     */
    public function fetch(array $offerIds): array
    {
        return array_map(function (int $offerId) {
            $response = Http::retry(3, 100)->get($this->baseUrl . $offerId);
            $responsePrices = Http::retry(3, 100)->get($this->baseUrl . $offerId . '/prices');
            $responseImages = Http::retry(3, 100)->get($this->baseUrl . $offerId . '/images');

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
