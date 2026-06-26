<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Toutes les routes ici sont automatiquement préfixées par "/api" et
| placées dans le groupe de middleware "api" (voir bootstrap/app.php).
| Grâce à $middleware->statefulApi(), une requête envoyée depuis un domaine
| listé dans SANCTUM_STATEFUL_DOMAINS (config/sanctum.php) est authentifiée
| par cookie de session plutôt que par token Bearer.
|
| Étape 1 : healthcheck.
| Étape 2 (authentification) : register / login / logout / me / profile.
| Étapes suivantes : projects, tasks, dashboard...
|
*/

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:6,1');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [ProfilController::class, 'update']);

    Route::get('/dashboard', [DashboardController::class, 'stats']);

    Route::apiResource('projects', ProjectController::class);

    Route::get('/tasks', [TaskController::class, 'all']);
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
});
