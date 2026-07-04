<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShareController;

Route::get('/', function () {
    return view('welcome');
});

// ── Share pages (Open Graph para redes sociais) ───────────────────────────────
Route::get('/share/evento/{evento}', [ShareController::class, 'evento'])->name('share.evento');
Route::get('/share/vaga/{vaga}', [ShareController::class, 'vaga'])->name('share.vaga');
Route::get('/share/loja/{loja}', [ShareController::class, 'loja'])->name('share.loja');
