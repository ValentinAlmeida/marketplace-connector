<?php

namespace App\Domain\Import\ValueObjects;

final class ImportProgress
{
    public function __construct(
        private readonly int $processedItems,
        private readonly int $totalItems
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if ($this->processedItems < 0 || $this->totalItems < 0) {
            throw new \InvalidArgumentException("Valores de progresso não podem ser negativos");
        }

        if ($this->processedItems > $this->totalItems) {
            throw new \InvalidArgumentException("Itens processados não podem ser maiores que o total");
        }
    }

    public function percentage(): float
    {
        return $this->totalItems > 0 
            ? round(($this->processedItems / $this->totalItems) * 100, 2) 
            : 0;
    }

    public function toArray(): array
    {
        return [
            'processed' => $this->processedItems,
            'total' => $this->totalItems,
            'percentage' => $this->percentage()
        ];
    }
}