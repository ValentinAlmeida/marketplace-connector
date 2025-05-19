<?php 

namespace App\Domain\Import\Supports;

use Illuminate\Support\Facades\Log;

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