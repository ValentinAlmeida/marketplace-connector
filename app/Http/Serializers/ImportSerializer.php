<?php

namespace App\Http\Serializers;

use App\Entities\Import;

/**
 * Class ImportSerializer
 *
 * Responsible for transforming Import entities into array representations.
 */
class ImportSerializer
{
    /**
     * Convert an Import entity into an array.
     *
     * @param Import $import The import entity to be serialized
     * @return array The serialized representation of the import
     */
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

    /**
     * Calculate the progress percentage based on processed and total items.
     *
     * @param int $processed Number of processed items
     * @param int $total Total number of items
     * @return float Progress percentage (0-100)
     */
    private function calculatePercentage(int $processed, int $total): float
    {
        return $total > 0 ? round(($processed / $total) * 100, 2) : 0;
    }
}
