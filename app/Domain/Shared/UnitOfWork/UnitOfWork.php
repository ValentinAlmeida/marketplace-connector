<?php

namespace App\Domain\Shared\UnitOfWork;

use Closure;
use Illuminate\Support\Facades\DB;

class UnitOfWork implements UnitOfWorkInterface
{
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