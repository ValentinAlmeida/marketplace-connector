<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Services\ImportServiceInterface;
use App\Infrastructure\Import\Services\HttpOfferHubSender;
use App\Infrastructure\Import\Services\HttpOffersFetcher;
use App\Infrastructure\Import\Services\HttpPaginatedOfferFetcher;

class ProcessImportUseCase
{
    public function __construct(
        private ImportServiceInterface $importService,
        private HttpPaginatedOfferFetcher $paginatedFetcher,
        private HttpOffersFetcher $offersFetcher,
        private HttpOfferHubSender $hubSender
    ) {}

    public function execute(int $importId): void
    {
        $import = $this->importService->findImport($importId);

        $offerIds = $this->paginatedFetcher->fetch();
        $import->startProcessing();

        if (empty($offerIds)) {
            $import->fail('Não tem ofertas disponíveis na URL');
            return;
        }

        $import->updateProgress(0, count($offerIds));

        $offers = $this->offersFetcher->fetch($offerIds);
        $this->hubSender->send($offers, $import);

        $this->importService->updateImport($import);
    }
}
