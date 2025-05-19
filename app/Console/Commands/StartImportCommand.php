<?php

namespace App\Console\Commands;

use App\Domain\Import\Jobs\ImportOffersJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartImportCommand extends Command
{
    protected $signature = 'import:start {importId : ID da importação}';
    protected $description = 'Processa a importação imediatamente (síncrono)';

    public function handle(): int
    {
        $importId = (int) $this->argument('importId');

        throw_if(
            $importId <= 0,
            new \InvalidArgumentException("ID da importação inválido")
        );

        $this->info("Processando importação {$importId}...");

        ImportOffersJob::dispatchSync($importId);

        $this->info("Importação concluída!");
        Log::info("Importação finalizada", ['import_id' => $importId]);

        return Command::SUCCESS;
    }
}