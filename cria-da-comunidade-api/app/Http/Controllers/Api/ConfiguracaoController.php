<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfiguracaoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $comunidadeId = $request->query('comunidade_id');

        $config = $comunidadeId
            ? Configuracao::where('comunidade_id', $comunidadeId)->first()
                ?? Configuracao::whereNull('comunidade_id')->first()
            : Configuracao::whereNull('comunidade_id')->first();

        $defaults = Configuracao::defaults();

        if (! $config) {
            return response()->json($defaults);
        }

        return response()->json(array_merge($defaults, array_filter(
            $config->only(array_keys($defaults)),
            fn ($v) => $v !== null
        )));
    }
}
