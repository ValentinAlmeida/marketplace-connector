<?php

namespace App\UseCase\Mappers;

use App\Entities\ValueObjects\Reference;
use App\UseCase\Import\Config\ImportConfig;
use App\UseCase\Offer\Dto\OfferCreateDto;
use RuntimeException;

class OfferDataMapper
{
    public function __construct(private ImportConfig $config) {}

    public function mapToDto(array $rawDetails, array $rawPrices, array $rawImages, int $originalOfferId): OfferCreateDto
    {
        $configFields = $this->config->fields();

        $details = data_get($rawDetails, $configFields['offerData']);
        if (!$details) {
            throw new RuntimeException("Dados de detalhes essenciais ausentes para oferta ID {$originalOfferId} na chave '{$configFields['offerData']}'. Resposta: " . json_encode($rawDetails));
        }

        $prices = $rawPrices['data'] ?? null;
        if (!$prices) {
            throw new RuntimeException("Dados de preços essenciais ausentes para oferta ID {$originalOfferId} na chave 'data'. Resposta: " . json_encode($rawPrices));
        }
        
        $imagesList = $rawImages['data'][$configFields['imagesList']] ?? null;
        if (!is_array($imagesList)) {
            throw new RuntimeException("Lista de imagens ausente ou em formato incorreto para oferta ID {$originalOfferId} na chave 'data.{$configFields['imagesList']}'. Resposta: " . json_encode($rawImages));
        }

        $images = array_column($imagesList, 'url');
        $price = $prices[$configFields['price']] ?? null;

        if (!isset($details['id'], $details['title'], $details['description'], $details['status'], $details['stock']) || $price === null) {
            throw new RuntimeException("Um ou mais campos essenciais (id, title, description, status, stock, price) não encontrados nos dados mapeados para oferta ID {$originalOfferId}");
        }

        return new OfferCreateDto(
            new Reference((string)$details['id']),
            (string)$details['title'],
            (string)$details['description'],
            (string)$details['status'],
            $images,
            (int)$details['stock'],
            (float)$price
        );
    }
}