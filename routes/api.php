<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'Great!']);
});

Route::prefix('/imports')->group(function(){
    Route::post('/', [ImportController::class, 'store']);
});