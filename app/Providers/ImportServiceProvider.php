<?php

namespace App\Providers;

use App\Domain\Import\Config\ImportConfig;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ImportConfig::class, function () {
            return new ImportConfig(
                env('IMPORTER_URL', 'http://localhost:3000')
            );
        });
    }
}