<?php

namespace App\Providers;

use App\Constants\Format;
use App\Domain\Import\Repositories\EloquentImportRepository;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Http\Serializers\ImportSerializer;
use App\Domain\Import\Services\ImportService;
use App\Domain\Shared\UnitOfWork\UnitOfWork;
use App\Domain\Shared\UnitOfWork\UnitOfWorkInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImportServiceInterface::class, ImportService::class);
        $this->app->singleton(ImportRepositoryInterface::class, EloquentImportRepository::class);
        $this->app->singleton(UnitOfWorkInterface::class, UnitOfWork::class);
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