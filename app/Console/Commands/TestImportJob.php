<?php

namespace App\Console\Commands;

use App\Domain\Import\Jobs\ProcessImportJob;
use App\Domain\Import\Services\ImportService;
use App\Domain\Import\Services\MarketplaceService;
use App\Domain\Shared\ValueObjects\Identifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class TestImportJob extends Command
{
    protected $signature = 'import:test 
                            {import_id : ID da importação a ser testada}
                            {--sync : Executar sincronamente (sem queue)}
                            {--fail : Simular falha na importação}
                            {--page-fail= : Número da página para simular falha}';

    protected $description = 'Testa o ProcessImportJob com diferentes cenários';

    public function handle(ImportService $importService)
    {
        try {
            $importId = $this->argument('import_id');
            $this->info("Iniciando teste para importação ID: {$importId}");

            if (!$importService->findImport($importId)) {
                $this->error("Importação não encontrada!");
                return 1;
            }

            $job = new ProcessImportJob(Identifier::create($importId));

            if ($this->option('fail')) {
                $this->simulateFailure($job);
                return 0;
            }

            $this->runJob($job);

            $this->info("Teste concluído! Verifique os logs para detalhes.");
            return 0;

        } catch (Throwable $e) {
            Log::error("Falha no comando de teste", ['error' => $e]);
            $this->error("Erro: " . $e->getMessage());
            return 1;
        }
    }

    private function runJob($job): void
    {
        if ($this->option('sync')) {
            $this->info("Executando sincronamente...");
            dispatch_sync($job);
        } else {
            $this->info("Dispachando para a fila...");
            dispatch($job)->onQueue('imports');
        }

        $this->info("Job disparado! Monitorando logs...");
        $this->call('queue:work', [
            '--queue' => 'imports',
            '--once' => true,
            '--stop-when-empty' => true
        ]);
    }

    private function simulateFailure($job): void
    {
        $this->info("Configurando simulação de falha...");

        $this->mock(MarketplaceService::class, function ($mock) {
            $failPage = $this->option('page-fail') ?? 1;
            $mock->shouldReceive('getOffers')
                ->andReturnUsing(function ($page) use ($failPage) {
                    if ($page == $failPage) {
                        throw new \Exception("Falha simulada na página {$page}");
                    }
                    return ['dados_teste' => "Página {$page}"];
                });
        });

        $this->runJob($job);
    }
}