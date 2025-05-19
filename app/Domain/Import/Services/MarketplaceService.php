<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Supports\ErrorHandler;
use App\Domain\Import\Supports\OfferApiClient;

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