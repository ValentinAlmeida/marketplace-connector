<?php

namespace App\Http\Serializers;

use App\Domain\Import\Entity\Import;

class ImportSerializer
{
    public function toArray(Import $import): array
    {
        return [
            'id' => $import->getIdentifier()->value(),
            'status' => $import->getStatusDescription(),
            'description' => $import->getProps()->description,
            'progress' => [
                'processed' => $import->getProps()->processedItems,
                'total' => $import->getProps()->totalItems,
                'percentage' => $this->calculatePercentage(
                    $import->getProps()->processedItems,
                    $import->getProps()->totalItems
                )
            ],
            'created_at' => $import->getProps()->createdAt->toIso8601String(),
            'scheduled_at' => $import->getProps()->scheduledAt?->toIso8601String()
        ];
    }

    private function calculatePercentage(int $processed, int $total): float
    {
        return $total > 0 ? round(($processed / $total) * 100, 2) : 0;
    }
}