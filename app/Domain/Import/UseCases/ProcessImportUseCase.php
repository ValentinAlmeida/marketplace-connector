<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Config\ImportConfig;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\Services\OfferHubSender;
use App\Domain\Import\Services\OffersFetcher;
use App\Domain\Import\Services\PaginatedOfferFetcher;

class ProcessImportUseCase
{
    private Import $import;

    public function __construct(
        private ImportServiceInterface $importService,
        private PaginatedOfferFetcher $paginatedFetcher,
        private OffersFetcher $offersFetcher,
        private OfferHubSender $hubSender
    ) {}

    public function execute(int $importId): void
    {
        $this->import = $this->importService->findImport($importId);
        
        $offerIds = $this->paginatedFetcher->fetch();
        
        $this->import->startProcessing();
        
        if (empty($offerIds)) {
            $this->import->fail('Não tem ofertas disponíveis na URL');
            return;
        }

        $this->import->updateProgress(0, count($offerIds));

        $offers = $this->offersFetcher->fetch($offerIds);
        $this->import = $this->hubSender->send($offers, $this->import);

        $this->importService->updateImport($this->import);
    }
}