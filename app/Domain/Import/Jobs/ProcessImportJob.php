<?php

namespace App\Domain\Import\Jobs;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Services\ImportService;
use App\Domain\Import\Services\MarketplaceService;
use App\Domain\Import\Services\HubService;
use App\Domain\Shared\ValueObjects\Identifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 180, 300];
    
    private const MAX_PAGES = 500;
    private const MAX_CONSECUTIVE_EMPTY = 3;

    public function __construct(
        private readonly Identifier $importId
    ) {}

    public function handle(
        ImportService $importService,
        MarketplaceService $marketplaceService,
        HubService $hubService
    ): void {
        try {
            $import = $this->validateImport($importService);
            $this->startProcessing($import, $importService);

            $page = 1;
            $processedItems = 0;
            $totalItems = 0;
            $consecutiveEmpty = 0;
            
            do {
                if ($page > self::MAX_PAGES) {
                    Log::warning('ProcessImportJob::handle - Limite máximo de páginas atingido', [
                        'import_id' => $this->importId->value(),
                        'current_page' => $page,
                        'max_pages' => self::MAX_PAGES
                    ]);
                    break;
                }

                $offers = $marketplaceService->getOffers($page);

                if (!empty($offers)) {
                    $consecutiveEmpty = 0;
                    $hubService->sendOffers($offers);
                    
                    $processedItems += count($offers);
                    $totalItems = max($totalItems, $processedItems);

                    $import->updateProgress($processedItems, $totalItems);
                    $importService->updateImport($import);
                    
                    $page++;
                } else {
                    $consecutiveEmpty++;

                    if ($consecutiveEmpty >= self::MAX_CONSECUTIVE_EMPTY) {
                        break;
                    }
                    
                    $page++;
                }
            } while (true);

            $this->completeProcessing($import, $importService);
            
        } catch (\Exception $e) {
            Log::error('ProcessImportJob::handle - Erro durante o processamento', [
                'import_id' => $this->importId->value(),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (isset($import)) {
                $this->handleFailure($import, $importService, $e);
            } else {
                Log::critical('ProcessImportJob::handle - Erro antes da importação ser validada', [
                    'import_id' => $this->importId->value(),
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function validateImport(ImportService $service): Import
    {
        $import = $service->findImport($this->importId->value());
        
        if (!$import) {
            Log::error('ProcessImportJob::validateImport - Importação não encontrada', ['import_id' => $this->importId->value()]);
            throw new \DomainException("Importação não encontrada: " . $this->importId->value());
        }
        
        return $import;
    }

    private function startProcessing(Import $import, ImportService $service): void
    {
        $import->startProcessing();
        $service->updateImport($import);
    }

    private function completeProcessing(Import $import, ImportService $service): void
    {
        $import->complete();
        $service->updateImport($import);
    }

    private function handleFailure(Import $import, ImportService $service, \Exception $e): void
    {
        Log::error('ProcessImportJob::handleFailure - Falha no processamento da importação', [
            'import_id' => $this->importId->value(),
            'error' => $e->getMessage(),
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);
        
        $import->fail($e->getMessage());
        $service->updateImport($import);
    }

    public function failed(\Throwable $exception): void
    {
        Log::critical('ProcessImportJob::failed - Job falhou após todas as tentativas', [
            'import_id' => $this->importId->value(),
            'error' => $exception->getMessage(),
            'exception' => get_class($exception),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}