<?php

namespace App\Console\Commands;

use App\Jobs\Import\ProcessImportJob;
use App\Models\Import as ImportModel;
use App\Entities\Enums\ImportStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class StartImportCommand
 *
 * Initiates a specified import and monitors its progress through the queue,
 * awaiting its completion or timeout.
 */
class StartImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:start 
        {importId : The ID of the import to be processed}  // To ensure the next info line doesn\'t overwrite the slash
        {--timeout=300 : Maximum time in seconds to wait for completion (default: 5 minutes)}
        {--poll=5 : Interval in seconds between status checks (default: 5 seconds)}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiates an import and monitors its progress through the queue.';

    /**
     * Execute the console command.
     *
     * Dispatches the import job and monitors its status until completion,
     * timeout, or failure.
     *
     * @return int Returns Command::SUCCESS or Command::FAILURE.
     */
    public function handle(): int
    {
        $importId = (int) $this->argument('importId');
        $timeoutSeconds = (int) $this->option('timeout');
        $pollIntervalSeconds = (int) $this->option('poll');

        if ($importId <= 0) {
            $this->error("Invalid import ID: {$importId}");
            return Command::FAILURE;
        }

        $initialImport = ImportModel::find($importId);
        if (!$initialImport) {
            $this->error("Import with ID {$importId} not found.");
            return Command::FAILURE;
        }
        
        $initialStatus = ImportStatus::tryFrom($initialImport->{ImportModel::STATUS});
        if ($initialStatus && $initialStatus->isFinal()) {
             $this->warn("Import {$importId} is already in a final state ({$initialStatus->value}). No job will be dispatched.");
             $this->displayImportDetails($initialImport);
             return Command::SUCCESS;
        }
        if ($initialStatus === ImportStatus::PROCESSING) {
             $this->warn("Import {$importId} is already processing. Monitoring existing status...");
        } else {
            $this->info("Dispatching ProcessImportJob for import {$importId} on queue 'imports_control'...");
            ProcessImportJob::dispatch($importId)->onQueue('imports_control');
        }

        $this->info("Monitoring progress of import {$importId} (Timeout: {$timeoutSeconds}s, Poll: {$pollIntervalSeconds}s)...");
        $this->line("Ensure your queue workers (`php artisan queue:work --queue=imports_control,imports_ids,imports_details,imports_send,default`) are running in another terminal.");

        $startTime = Carbon::now();
        $progressBar = null;

        while (Carbon::now()->diffInSeconds($startTime) < $timeoutSeconds) {
            $import = ImportModel::find($importId);

            if (!$import) {
                $this->error("Import {$importId} disappeared from the database during monitoring.");
                return Command::FAILURE;
            }

            $currentStatus = ImportStatus::tryFrom($import->{ImportModel::STATUS});
            $totalItems = $import->{ImportModel::TOTAL_ITEMS} ?? 0;
            $processedItems = $import->{ImportModel::PROCESSED_ITEMS} ?? 0;
            $failedItems = $import->{ImportModel::FAILED_ITEMS} ?? 0;
            
            $itemsAttempted = $processedItems + $failedItems;

            if ($totalItems > 0 && $progressBar === null) {
                $progressBar = $this->output->createProgressBar($totalItems);
                $progressBar->start();
            }
            
            if ($progressBar) {
                $progressBar->setProgress(min($itemsAttempted, $totalItems));
            }
            
            $this->newLine(); 
            $this->info(" Status: {$import->{ImportModel::STATUS}} | Total: {$totalItems} | Processed: {$processedItems} | Failed: {$failedItems}");


            if ($currentStatus && $currentStatus->isFinal()) {
                if ($progressBar) $progressBar->finish();
                $this->newLine(); 
                if ($currentStatus === ImportStatus::COMPLETED) {
                    $this->info("Import {$importId} completed successfully!");
                } elseif ($currentStatus === ImportStatus::FAILED) {
                    $this->error("Import {$importId} failed!");
                } else {
                    $this->warn("Import {$importId} finished with status: {$currentStatus->value}.");
                }
                $this->displayImportDetails($import);
                Log::info("Monitoring of import {$importId} finished by command.", ['import_id' => $importId, 'status' => $currentStatus->value]);
                return Command::SUCCESS;
            }

            sleep($pollIntervalSeconds);
        }
        
        if ($progressBar) $progressBar->finish();
        $this->newLine(); 
        $this->error("Timeout ({$timeoutSeconds}s) reached while waiting for import {$importId}. Check logs and status manually.");
        $this->displayImportDetails(ImportModel::find($importId)); 
        return Command::FAILURE;
    }

    /**
     * Displays the details of an import in the console.
     *
     * @param ImportModel|null $import The ImportModel instance or null if not found.
     * @return void
     */
    private function displayImportDetails(?ImportModel $import): void
    {
        if (!$import) {
            $this->warn("Could not load import details for display.");
            return;
        }
        $this->line("Import Details:");
        $this->line("  ID: " . $import->id);
        $this->line("  Status: " . $import->{ImportModel::STATUS});
        $this->line("  Description: " . $import->{ImportModel::DESCRIPTION});
        $this->line("  Total Items: " . $import->{ImportModel::TOTAL_ITEMS});
        $this->line("  Processed: " . $import->{ImportModel::PROCESSED_ITEMS});
        $this->line("  Failed: " . $import->{ImportModel::FAILED_ITEMS});
        $this->line("  Main Error: " . $import->{ImportModel::ERROR});
        $this->line("  Metadata (error_details): " . json_encode($import->{ImportModel::METADATA}['error_details'] ?? []));
        $this->line("  Started At: " . $import->{ImportModel::STARTED_AT});
        $this->line("  Completed At: " . $import->{ImportModel::COMPLETED_AT});
    }
}