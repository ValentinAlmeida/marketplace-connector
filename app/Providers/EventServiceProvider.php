<?php

namespace App\Providers;

use App\Events\Import\OfferDetailsFetchedForImport;
use App\Events\Import\OfferIdsRetrievedForImport;
use App\Events\Import\OfferProcessingFailed;
use App\Events\Import\OfferSuccessfullySentToHub;
use App\Listeners\Import\DispatchOfferDetailJobs;
use App\Listeners\Import\DispatchSendToHubJob;
use App\Listeners\Import\UpdateImportProgress;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OfferIdsRetrievedForImport::class => [
            DispatchOfferDetailJobs::class,
        ],
        OfferDetailsFetchedForImport::class => [
            DispatchSendToHubJob::class,
        ],
        OfferSuccessfullySentToHub::class => [
            UpdateImportProgress::class,
        ],
        OfferProcessingFailed::class => [
            UpdateImportProgress::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}