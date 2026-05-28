<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComunidadeController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\ProfissionalController;
use App\Http\Controllers\Api\ProjetoController;
use App\Http\Controllers\Api\VagaController;
use Illuminate\Support\Facades\Route;

// Auth — públicas
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Rotas públicas de leitura
Route::get('comunidades',          [ComunidadeController::class, 'index']);
Route::get('comunidades/{comunidade}', [ComunidadeController::class, 'show']);

Route::get('profissionais',                  [ProfissionalController::class, 'index']);
Route::get('profissionais/{profissional}',   [ProfissionalController::class, 'show']);

Route::get('eventos',              [EventoController::class, 'index']);
Route::get('eventos/{evento}',     [EventoController::class, 'show']);

Route::get('projetos',             [ProjetoController::class, 'index']);
Route::get('projetos/{projeto}',   [ProjetoController::class, 'show']);

Route::get('vagas',                [VagaController::class, 'index']);
Route::get('vagas/{vaga}',         [VagaController::class, 'show']);

// Rotas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Profissionais
    Route::post('profissionais',                   [ProfissionalController::class, 'store']);
    Route::put('profissionais/{profissional}',      [ProfissionalController::class, 'update']);
    Route::delete('profissionais/{profissional}',   [ProfissionalController::class, 'destroy']);

    // Eventos
    Route::post('eventos',                    [EventoController::class, 'store']);
    Route::post('eventos/{evento}/rsvp',      [EventoController::class, 'rsvp']);
    Route::delete('eventos/{evento}',         [EventoController::class, 'destroy']);

    // Projetos
    Route::post('projetos',                       [ProjetoController::class, 'store']);
    Route::post('projetos/{projeto}/apoiar',       [ProjetoController::class, 'apoiar']);
    Route::delete('projetos/{projeto}',            [ProjetoController::class, 'destroy']);

    // Vagas
    Route::post('vagas',                          [VagaController::class, 'store']);
    Route::put('vagas/{vaga}',                    [VagaController::class, 'update']);
    Route::post('vagas/{vaga}/candidatar',        [VagaController::class, 'candidatar']);
    Route::delete('vagas/{vaga}',                 [VagaController::class, 'destroy']);
});
