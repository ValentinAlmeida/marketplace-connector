<?php

namespace App\Listeners\Import;

use App\Entities\States\Import\CompletedState;
use App\Events\Import\OffersDispatchedToHub;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Import\Support\HandlesImportFailures;
use Illuminate\Support\Carbon;

class OnOffersDispatchedToHub
{
    use HandlesImportFailures;

    public function __construct(
        private IImportProcessor $importService
    ) {}

    public function handle(OffersDispatchedToHub $event): void
    {
        $this->executeSafely($event->importId, function () use ($event) {
            $import = $this->importService->findImport($event->importId);
            $import->changeState(new CompletedState());
            $import->setCompletedAt(Carbon::now());

            $this->importService->updateImport($import);
        });
    }
}
