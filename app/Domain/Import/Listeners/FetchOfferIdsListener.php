<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\ImportStarted;
use App\Domain\Import\Events\OfferIdsFetched;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\Services\PaginatedOfferFetcherInterface;

class FetchOfferIdsListener
{
    public function __construct(
        private ImportServiceInterface $importService,
        private PaginatedOfferFetcherInterface $paginatedFetcher
    ) {}

    public function handle(ImportStarted $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $offerIds = $this->paginatedFetcher->fetch();

        if (empty($offerIds)) {
            $import->fail('Não tem ofertas disponíveis na URL');
            $this->importService->updateImport($import);
            return;
        }

        event(new OfferIdsFetched($import->getIdentifier()->value(), $offerIds));
    }
}
