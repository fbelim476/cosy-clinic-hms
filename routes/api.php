<?php

use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\QueueApiController;
use App\Http\Controllers\Api\VisitApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/patients/search', [PatientApiController::class, 'search']);
    Route::get('/patients/{patient}', [PatientApiController::class, 'show']);
    Route::get('/visits/queue', [VisitApiController::class, 'queue']);
    Route::get('/visits/{visit}', [VisitApiController::class, 'show']);
    Route::get('/queue/display', [QueueApiController::class, 'display']);
    Route::get('/dashboard/stats', [QueueApiController::class, 'stats']);
});
