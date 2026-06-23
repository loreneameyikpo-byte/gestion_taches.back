<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\ProfilController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok','timestamp' => now()->toDateTimeString()
    ]);
});

Route::post('/register', [AuthController::class, 'register'])->middleware('<throttle:6,1>');
Route::post('/login', [AuthController::class, 'login'])->middleware('<throttle:6,1>');
//Route::post('/profil', [ProfilController::class, 'update'])->middleware('auth');

Route::middleware('auth:sanctum')->group(function () {
//Route::post('/profil', [ProfilController::class, 'update']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::put('/profil', [ProfilController::class, 'update']);
Route::get('/me', [ProfilController::class, 'me']);
});