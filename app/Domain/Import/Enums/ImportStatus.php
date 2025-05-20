<?php

namespace App\Domain\Import\Enums;

/**
 * Enum representing the status of an import process.
 */
enum ImportStatus: string
{
    /** The import is scheduled and has not started yet. */
    case PENDING = 'import.status.pending';

    /** The import is currently in progress. */
    case PROCESSING = 'import.status.processing';

    /** The import completed successfully. */
    case COMPLETED = 'import.status.completed';

    /** The import failed during processing. */
    case FAILED = 'import.status.failed';

    /** The import was cancelled before completion. */
    case CANCELLED = 'import.status.cancelled';

    /**
     * Returns an array of metadata associated with the status.
     *
     * @return array{description: string}
     */
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

    /**
     * Checks if the status represents a failed import.
     *
     * @return bool True if status is FAILED, false otherwise.
     */
    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }
}
