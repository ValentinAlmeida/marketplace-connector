<?php

namespace App\Domain\Import\Jobs;

use App\Domain\Import\UseCases\ProcessImportUseCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ImportOffersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $importId) {}

    public function handle(ProcessImportUseCase $useCase): void
    {
        $useCase->execute($this->importId);
    }
}