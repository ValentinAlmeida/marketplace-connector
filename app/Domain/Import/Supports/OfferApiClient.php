<?php

namespace App\Domain\Import\Supports;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class OfferApiClient
{
    public function fetchOffers(int $page, int $perPage): array
    {
        $baseUrl = env('IMPORTER_URL', 'http://localhost');
        $endpoint = "{$baseUrl}:3000/offers";

        $response = Http::get($endpoint, [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "API request failed with status: " . $response->status()
            );
        }

        $data = $response->json();
        
        if (!isset($data['data'])) {
            Log::warning('OfferApiClient::fetchOffers - Resposta da API não contém campo "data"', [
                'response' => $data
            ]);
            return [];
        }

        return $data['data'];
    }
}