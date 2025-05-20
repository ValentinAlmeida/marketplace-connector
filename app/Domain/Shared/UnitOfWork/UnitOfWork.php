<?php

namespace App\Domain\Shared\UnitOfWork;

use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Class UnitOfWork
 *
 * Handles execution of code blocks within a database transaction using Laravel's DB facade.
 */
class UnitOfWork implements UnitOfWorkInterface
{
    /**
     * Executes the given callback within a database transaction.
     *
     * Begins a transaction before executing the callback.
     * Commits the transaction if execution is successful.
     * Rolls back the transaction if an exception is thrown.
     *
     * @param Closure $cb The callback to execute within the transaction
     * @return mixed The result returned by the callback
     *
     * @throws \Throwable If an error occurs during execution
     */
    public function run(Closure $cb): mixed
    {
        DB::beginTransaction();
        try {
            $result = $cb();
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
