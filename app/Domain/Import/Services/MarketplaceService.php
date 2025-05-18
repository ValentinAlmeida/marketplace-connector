<?php

namespace App\Domain\Import\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarketplaceService
{
    private const PER_PAGE = 100;
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MS = 100;

    private OfferApiClient $apiClient;
    private ErrorHandler $errorHandler;

    public function __construct()
    {
        $this->apiClient = new OfferApiClient();
        $this->errorHandler = new ErrorHandler();
    }

    public function getOffers(int $page)
    {
        try {
            return $this->fetchOffersWithRetry($page);
        } catch (\Exception $e) {
            $this->errorHandler->logAndThrow(
                'MarketplaceService::getOffers - Falha ao buscar ofertas',
                $e,
                ['page' => $page]
            );
        }
    }

    private function fetchOffersWithRetry(int $page): array
    {
        $retryCount = 0;
        
        do {
            try {
                return $this->apiClient->fetchOffers($page, self::PER_PAGE);
            } catch (\Exception $e) {
                $retryCount++;
                
                if ($retryCount >= self::MAX_RETRIES) {
                    throw $e;
                }
                
                usleep(self::RETRY_DELAY_MS * 1000);
            }
        } while ($retryCount < self::MAX_RETRIES);

        return [];
    }
}

class OfferApiClient
{
    public function fetchOffers(int $page, int $perPage): array
    {
        $response = Http::retry(3, 100)
            ->get('http://localhost:3000/offers', [
                'page' => $page,
                'per_page' => $perPage
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

class ErrorHandler
{
    public function logAndThrow(string $message, \Throwable $e, array $context = []): void
    {
        Log::error('ErrorHandler::logAndThrow - ' . $message, array_merge($context, [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]));
        
        throw $e;
    }
}