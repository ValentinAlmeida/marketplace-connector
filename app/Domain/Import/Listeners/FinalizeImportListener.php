<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\OffersSentToHub;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\States\CompletedState;
use Illuminate\Support\Carbon;

class FinalizeImportListener
{
    public function __construct(
        private ImportServiceInterface $importService
    ) {}

    public function handle(OffersSentToHub $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $import->changeState(new CompletedState());
        $import->setCompletedAt(Carbon::now());

        $this->importService->updateImport($import);
    }
}
