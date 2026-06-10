<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\Vaga;

class ShareController extends Controller
{
    public function loja(Loja $loja): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($loja->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $image = $loja->logo_url
            ?? asset('images/og-default.png');

        return view('share.loja', [
            'loja'        => $loja,
            'ogTitle'     => "{$loja->nome} — {$loja->categoria}",
            'ogDesc'      => $desc ?: "{$loja->categoria} · {$loja->endereco}",
            'ogImage'     => $image,
            'redirectUrl' => config('app.frontend_url') . "/?loja={$loja->id}",
        ]);
    }

    public function vaga(Vaga $vaga): \Illuminate\Contracts\View\View
    {
        $desc = strip_tags($vaga->descricao ?? '');
        $desc = mb_strlen($desc) > 160
            ? mb_substr($desc, 0, 157) . '...'
            : $desc;

        $image = $vaga->logo_imagem_url
            ?? asset('images/og-default.png');

        return view('share.vaga', [
            'vaga'        => $vaga,
            'ogTitle'     => "{$vaga->titulo} — {$vaga->empresa}",
            'ogDesc'      => $desc ?: "{$vaga->tipo} · {$vaga->local} · {$vaga->salario}",
            'ogImage'     => $image,
            'redirectUrl' => config('app.frontend_url') . "/?vaga={$vaga->id}",
        ]);
    }
}
