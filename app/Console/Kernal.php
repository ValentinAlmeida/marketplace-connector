<?php

namespace App\Console;

use App\Console\Commands\TestImportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        $this->app->singleton('command.import.test', function ($app) {
            return new TestImportJob(
                $app->make(\App\Domain\Import\Services\ImportService::class)
            );
        });
        
        $this->commands([
            'command.import.test',
            TestImportJob::class,
        ]);

        require base_path('routes/console.php');
    }

    protected function getArtisanTimeout()
    {
        return 3600;
    }
}