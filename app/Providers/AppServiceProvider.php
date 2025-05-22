<?php

namespace App\Providers;

use App\Constants\Format;
use App\Http\Serializers\ImportSerializer;
use App\Repositories\ImportRepository;
use App\UseCase\Contracts\Import\ICreate;
use App\UseCase\Contracts\Import\IFetchAllOfferIds;
use App\UseCase\Contracts\Import\IFetchSingleOfferDetails;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Contracts\Import\IInitiateImportProcessing;
use App\UseCase\Contracts\Import\IProcess;
use App\UseCase\Contracts\Import\ISchedule;
use App\UseCase\Contracts\Import\ISendSingleOfferToHub;
use App\UseCase\Contracts\Import\IUpdate;
use App\UseCase\Contracts\IUnitOfWork;
use App\UseCase\Contracts\Repositories\IImportRepository;
use App\UseCase\Import\Create;
use App\UseCase\Import\FetchAllOfferIds;
use App\UseCase\Import\FetchSingleOfferDetails;
use App\UseCase\Import\ImportProcessor;
use App\UseCase\Import\InitiateImportProcessing;
use App\UseCase\Import\Process;
use App\UseCase\Import\Schedule;
use App\UseCase\Import\SendSingleOfferToHub;
use App\UseCase\Import\Update;
use App\UseCase\UnitOfWork;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ICreate::class, Create::class);
        $this->app->singleton(IInitiateImportProcessing::class, InitiateImportProcessing::class);
        $this->app->singleton(IFetchAllOfferIds::class, FetchAllOfferIds::class);
        $this->app->singleton(IFetchSingleOfferDetails::class, FetchSingleOfferDetails::class);
        $this->app->singleton(ISendSingleOfferToHub::class, SendSingleOfferToHub::class);
        $this->app->singleton(IProcess::class, Process::class);
        $this->app->singleton(ISchedule::class, Schedule::class);
        $this->app->singleton(IUpdate::class, Update::class);
        $this->app->singleton(IImportProcessor::class, ImportProcessor::class);
        $this->app->singleton(IImportRepository::class, ImportRepository::class);
        $this->app->singleton(IUnitOfWork::class, UnitOfWork::class);
        $this->app->singleton(ImportSerializer::class, fn() => new ImportSerializer());

        
        $this->app->singleton('format.constants', fn() => new class {
            public const SCHEDULE = Format::SCHEDULE;
            public const DATE = Format::DATE;
        });
    }

    public function boot(): void
    {
        //
    }
}