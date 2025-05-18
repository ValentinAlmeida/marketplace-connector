<?php

namespace App\Domain\Import\Enums;

enum ImportStatus: string
{
    case PENDING = 'import.status.pending';
    case PROCESSING = 'import.status.processing';
    case COMPLETED = 'import.status.completed';
    case FAILED = 'import.status.failed';
    case CANCELLED = 'import.status.cancelled';

    public function withMeta(): array
    {
        return match ($this) {
            self::PENDING => [
                'description' => 'Pendente',
            ],
            self::PROCESSING => [
                'description' => 'Processando',
            ],
            self::COMPLETED => [
                'description' => 'Concluída',
            ],
            self::FAILED => [
                'description' => 'Falhou',
            ],
            self::CANCELLED => [
                'description' => 'Cancelada',
            ],
        };
    }
}