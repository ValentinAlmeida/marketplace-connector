<?php

namespace App\Exceptions;

use DomainException;

class ImportException extends DomainException
{
    public static function notFoundById(int $id): self
    {
        return new static("Importação com ID {$id} não encontrada.");
    }

    public static function generic(string $message): self
    {
        return new static($message);
    }
}