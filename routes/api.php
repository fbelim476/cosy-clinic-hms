<?php

use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\QueueApiController;
use App\Http\Controllers\Api\VisitApiController;
use App\Http\Controllers\Api\PrintApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/patients/search', [PatientApiController::class, 'search']);
    Route::get('/patients/{patient}', [PatientApiController::class, 'show']);
    Route::get('/visits/queue', [VisitApiController::class, 'queue']);
    Route::get('/visits/{visit}', [VisitApiController::class, 'show']);
    Route::get('/queue/display', [QueueApiController::class, 'display']);
    Route::get('/dashboard/stats', [QueueApiController::class, 'stats']);

    Route::prefix('print')->group(function () {
        Route::get('/templates', [PrintApiController::class, 'index']);
        Route::post('/template', [PrintApiController::class, 'store']);
        Route::put('/template/{template}', [PrintApiController::class, 'update']);
        Route::delete('/template/{template}', [PrintApiController::class, 'destroy']);
        Route::post('/template/preview', [PrintApiController::class, 'preview']);
        Route::get('/template/{template}/export', [PrintApiController::class, 'export']);
        Route::post('/template/import', [PrintApiController::class, 'import']);
    });
});
