<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExternalDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// ============================================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================================

// A. Login & Register endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ============================================================
// PROTECTED ROUTES (Authentication Required via Sanctum)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // Auth profile & logout
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // B. CRUD User endpoints
    Route::apiResource('users', UserController::class);

    // C. Search by NAMA - e.g., GET /api/external-data/search/nama?nama=Turner Mia
    Route::get('/external-data/search/nama', [ExternalDataController::class, 'searchByNama']);

    // D. Search by NIM - e.g., GET /api/external-data/search/nim?nim=9352078461
    Route::get('/external-data/search/nim', [ExternalDataController::class, 'searchByNim']);

    // E. Search by YMD - e.g., GET /api/external-data/search/ymd?ymd=20230405
    Route::get('/external-data/search/ymd', [ExternalDataController::class, 'searchByYmd']);

    // Get all external data
    Route::get('/external-data', [ExternalDataController::class, 'index']);
});
