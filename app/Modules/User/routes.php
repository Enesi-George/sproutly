<?php
use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\ProfileController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ProfileController::class, 'me']);
    Route::post('/me', [ProfileController::class, 'update']);
});
