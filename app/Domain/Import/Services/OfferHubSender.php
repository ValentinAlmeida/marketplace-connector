<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\States\CompletedState;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class OfferHubSender
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly Import $import
    ) {}

    /**
     * Envia ofertas para o hub, atualiza progresso e estado da importação
     *
     * @param Offer[] $offers
     * @return void
     */
    public function send(array $offers): void
    {
        $total = count($offers);
        $completed = 0;

        foreach ($offers as $offer) {
            $response = Http::retry(3, 100)->post($this->baseUrl, [
                'title' => $offer->getProps()->title,
                'description' => $offer->getProps()->description,
                'status' => $offer->getProps()->status,
                'stock' => $offer->getProps()->stock,
            ]);

            if (!$response->successful()) {
                $this->import->fail('Tentativa de criar registro no hub deu falha');
                return;
            }

            $completed++;
            $this->import->updateProgress($completed, $total);
        }

        $this->import->changeState(new CompletedState);
        $this->import->setCompletedAt(Carbon::now());
    }
}
