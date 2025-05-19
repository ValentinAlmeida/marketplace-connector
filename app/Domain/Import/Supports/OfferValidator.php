<?php 

namespace App\Domain\Import\Supports;

use Illuminate\Support\Facades\Log;

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