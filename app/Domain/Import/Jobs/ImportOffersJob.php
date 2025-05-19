<?php

namespace App\Domain\Import\Jobs;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\Services\OfferHubSender;
use App\Domain\Import\Services\OffersFetcher;
use App\Domain\Import\Services\PaginatedOfferFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

final class ImportOffersJob implements ShouldQueue
{
    private Import $import;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $importId) {}

    public function handle(): void
    {
        $importServiceInterface = App::make(ImportServiceInterface::class);
        $this->import = $importServiceInterface->findImport($this->importId);

        $fetcher = new PaginatedOfferFetcher($this->getUrl('/offers'), $this->import);
        $offerIds = $fetcher->fetch();

        $this->import->startProcessing();

        if (empty($offerIds)) {
            $this->import->fail('Não tem ofertas disponíveis na URL');
            return;
        }

        $this->import->updateProgress(0, count($offerIds));

        $offersFetcher = new OffersFetcher($this->getUrl('/offers/'));
        $offers = $offersFetcher->fetch($offerIds);

        $hubSender = new OfferHubSender($this->getUrl('/hub/create-offer'), $this->import);
        $hubSender->send($offers);

        $importServiceInterface->updateImport($this->import);
    }

    private function getUrl(string $endpoint): string
    {
        $url = env('IMPORTER_URL', 'http://localhost') . ":3000{$endpoint}";
        Log::debug("URL gerada: {$url}");
        return $url;
    }
}
