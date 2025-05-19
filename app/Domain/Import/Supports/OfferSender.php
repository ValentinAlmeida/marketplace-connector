<?php 

namespace App\Domain\Import\Supports;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OfferSender
{
    private OfferValidator $validator;

    public function __construct(OfferValidator $validator)
    {
        $this->validator = $validator;
    }

    public function sendAll(array $offers): array
    {
        $processed = 0;
        $invalid = 0;

        foreach ($offers as $index => $offer) {
            if (!$this->validator->validate($offer)) {
                Log::warning('OfferSender::sendAll - Oferta inválida - pulando', ['index' => $index]);
                $invalid++;
                continue;
            }

            if ($this->send($offer, $index)) {
                $processed++;
            } else {
                $invalid++;
            }
        }

        return [
            'processed' => $processed,
            'invalid' => $invalid
        ];
    }

    public function send(array $offer, int $index): bool
    {
        try {
            $baseUrl = env('IMPORTER_URL', 'http://localhost');
            $endpoint = "{$baseUrl}:3000/hub/create-offer";

            $response = Http::timeout(30)->post($endpoint, [
                'title' => $offer['title'],
                'description' => $offer['description'],
                'status' => $offer['status'],
                'stock' => (int)$offer['stock'],
            ]);

            if (!$response->successful()) {
                Log::error('OfferSender::send - Falha no envio da oferta', [
                    'index' => $index,
                    'status_code' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('OfferSender::send - Erro ao enviar oferta', [
                'index' => $index,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}