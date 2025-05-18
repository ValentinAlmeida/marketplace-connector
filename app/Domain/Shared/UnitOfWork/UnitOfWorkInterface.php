<?php

namespace App\Domain\Shared\UnitOfWork;

use Closure;

interface UnitOfWorkInterface
{
    public function run(Closure $cb): mixed;
}