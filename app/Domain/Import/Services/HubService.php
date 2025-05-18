<?php

namespace App\Domain\Import\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HubService
{
    private OfferNormalizer $normalizer;
    private OfferValidator $validator;
    private OfferSender $sender;

    public function __construct(
        OfferNormalizer $normalizer,
        OfferValidator $validator,
        OfferSender $sender
    ) {
        $this->normalizer = $normalizer;
        $this->validator = $validator;
        $this->sender = $sender;
    }

    public function sendOffers($offers): array
    {
        $normalizedOffers = $this->normalizer->normalize($offers);
        
        if ($normalizedOffers === null) {
            Log::error('HubService::sendOffers - Falha na normalização das ofertas', ['input_structure' => $this->getStructureInfo($offers)]);
            return ['error' => 'Não foi possível normalizar a estrutura de ofertas'];
        }
        
        $result = $this->sender->sendAll($normalizedOffers);

        return [
            'total' => count($normalizedOffers),
            'processed' => $result['processed'],
            'invalid' => $result['invalid']
        ];
    }

    private function getStructureInfo($input): array
    {
        if (!is_array($input)) {
            return ['type' => gettype($input)];
        }

        return [
            'type' => 'array',
            'keys' => array_keys($input),
            'first_item_type' => isset($input[0]) ? gettype($input[0]) : null,
            'count' => count($input)
        ];
    }
}

class OfferNormalizer
{
    public function normalize($input): ?array
    {
        if ($this->isValidOfferFormat($input)) {
            return $input;
        }

        if (is_array($input) && isset($input['offers'])) {
            return $this->normalizeOfferList($input['offers']);
        }

        if (is_array($input) && array_keys($input) === range(0, count($input) - 1)) {
            return $this->normalizeOfferList($input);
        }

        Log::warning('OfferNormalizer::normalize - Estrutura de ofertas não reconhecida', ['input_type' => gettype($input)]);
        return null;
    }

    private function normalizeOfferList(array $items): array
    {
        $normalized = [];
        
        foreach ($items as $index => $item) {
            if (is_string($item)) {
                $normalized[] = $this->createDefaultOffer($item);
            } elseif (is_array($item)) {
                $normalized[] = $this->mergeWithDefaults($item);
            } else {
                Log::warning('OfferNormalizer::normalizeOfferList - Tipo de oferta não suportado', [
                    'index' => $index,
                    'type' => gettype($item)
                ]);
            }
        }
        
        return $normalized;
    }

    private function createDefaultOffer(string $item): array
    {
        return [
            'title' => 'Produto ' . $item,
            'description' => 'Descrição para ' . $item,
            'status' => 'active',
            'stock' => 0
        ];
    }

    private function mergeWithDefaults(array $item): array
    {
        return array_merge([
            'title' => '',
            'description' => '',
            'status' => 'active',
            'stock' => 0
        ], $item);
    }

    private function isValidOfferFormat($input): bool
    {
        if (!is_array($input)) {
            return false;
        }

        foreach ($input as $index => $item) {
            if (!is_array($item) || 
                !isset($item['title'], $item['description'], $item['status'], $item['stock'])) {
                return false;
            }
        }

        return true;
    }
}

class OfferValidator
{
    public function validate(array $offer): bool
    {
        $required = ['title', 'description', 'status', 'stock'];
        $missing = [];
        
        foreach ($required as $field) {
            if (!isset($offer[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            Log::warning('OfferValidator::validate - Oferta inválida: campos obrigatórios faltando', [
                'missing_fields' => $missing,
                'offer_keys' => array_keys($offer)
            ]);
            return false;
        }

        return true;
    }
}

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
            $response = Http::timeout(30)
                ->post('http://localhost:3000/hub/create-offer', [
                    'title' => $offer['title'],
                    'description' => $offer['description'],
                    'status' => $offer['status'],
                    'stock' => (int)$offer['stock']
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