<?php

namespace App\Gateways\Offer;

use App\Entities\Import;
use App\Entities\States\Import\CompletedState;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IOfferSender;
use App\UseCase\Import\Config\ImportConfig;
use Illuminate\Support\Carbon;
use RuntimeException;

class HttpSender implements IOfferSender
{
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient
    ) {}

    public function send(array $offers, Import $import): Import
    {
        $baseUrl = $this->config->getHubCreateEndpoint();
        $total = count($offers);
        $completed = 0;

        foreach ($offers as $offer) {
            $response = $this->httpClient->post($baseUrl, [
                'title' => $offer->getProps()->title,
                'description' => $offer->getProps()->description,
                'status' => $offer->getProps()->status,
                'stock' => $offer->getProps()->stock,
            ]);
            
            if ($response->getStatusCode() !== 201) {
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
