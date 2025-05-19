<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Supports\OfferNormalizer;
use App\Domain\Import\Supports\OfferSender;
use App\Domain\Import\Supports\OfferValidator;
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