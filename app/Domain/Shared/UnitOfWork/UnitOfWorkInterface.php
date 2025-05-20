<?php

namespace App\Domain\Shared\UnitOfWork;

use Closure;

/**
 * Interface UnitOfWorkInterface
 *
 * Defines a contract for executing operations within a transactional context.
 */
interface UnitOfWorkInterface
{
    /**
     * Executes the given callback within a transactional context.
     *
     * All changes made within the callback will be committed if no exception occurs.
     * If an exception is thrown, the transaction should be rolled back.
     *
     * @param Closure $cb The callback containing the operations to execute
     * @return mixed The return value of the callback
     */
    public function run(Closure $cb): mixed;
}
