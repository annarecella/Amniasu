<?php

use Illuminate\Support\Facades\Route;
use Modules\Mbkm\Controllers\MbkmApplicationController;
use Modules\Mbkm\Controllers\MbkmProgramController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('mbkm/program-types', [MbkmProgramController::class, 'types']);
    Route::get('mbkm/partners', [MbkmProgramController::class, 'partners']);
    Route::get('mbkm/programs', [MbkmProgramController::class, 'index']);
    Route::get('mbkm/programs/{mbkmProgram}', [MbkmProgramController::class, 'show']);
    Route::post('mbkm/programs', [MbkmProgramController::class, 'store'])->middleware('permission:mbkm.manage');
    Route::put('mbkm/programs/{mbkmProgram}', [MbkmProgramController::class, 'update'])->middleware('permission:mbkm.manage');
    Route::get('mbkm/applications', [MbkmApplicationController::class, 'index']);
    Route::post('mbkm/applications', [MbkmApplicationController::class, 'store']);
    Route::post('mbkm/applications/{mbkmApplication}/submit', [MbkmApplicationController::class, 'submit']);
    Route::post('mbkm/applications/{mbkmApplication}/review', [MbkmApplicationController::class, 'review'])->middleware('permission:mbkm.manage');
});
