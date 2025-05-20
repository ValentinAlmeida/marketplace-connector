<?php

namespace App\Infrastructure\Import\Services;

use App\Domain\Import\Config\ImportConfig;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Services\OfferHubSenderInterface;
use App\Domain\Import\States\CompletedState;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use RuntimeException;

class HttpOfferHubSender implements OfferHubSenderInterface
{
    public function __construct(private ImportConfig $config) {}

    public function send(array $offers, Import $import): Import
    {
        $baseUrl = $this->config->getHubCreateEndpoint();

        $total = count($offers);
        $completed = 0;

        foreach ($offers as $offer) {
            $response = Http::retry(3, 100)->post($baseUrl, [
                'title' => $offer->getProps()->title,
                'description' => $offer->getProps()->description,
                'status' => $offer->getProps()->status,
                'stock' => $offer->getProps()->stock,
            ]);

            if (!$response->successful()) {
                $import->fail('Tentativa de criar registro no hub deu falha');
                throw new RuntimeException('Erro ao enviar oferta para o hub.');
            }

            $completed++;
            $import->updateProgress($completed, $total);
        }

        $import->changeState(new CompletedState);
        $import->setCompletedAt(Carbon::now());

        return $import;
    }
}
