<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ReceitaController;
use App\Http\Controllers\Api\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/docs/spec', [SwaggerController::class, 'spec']);
Route::get('/docs', [SwaggerController::class, '__invoke']);

Route::post('/usuarios', [AuthController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logoff', [AuthController::class, 'logoff'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/receitas', [ReceitaController::class, 'index']);
    Route::post('/receitas', [ReceitaController::class, 'store']);
    Route::get('/receitas/{receita}', [ReceitaController::class, 'show']);
    Route::put('/receitas/{receita}', [ReceitaController::class, 'update']);
    Route::patch('/receitas/{receita}', [ReceitaController::class, 'update']);
    Route::delete('/receitas/{receita}', [ReceitaController::class, 'destroy']);
    Route::get('/categorias', [CategoriaController::class, 'index']);
});
