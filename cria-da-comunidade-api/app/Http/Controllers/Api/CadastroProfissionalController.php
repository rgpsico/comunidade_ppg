<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CadastroProfissional;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Cadastro rápido de profissionais pelo link do assistente. Público (sem login), com limite de taxa e isca anti-robô. */
class CadastroProfissionalController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Isca: campo invisível que pessoas nunca preenchem. Robôs preenchem: responde "sucesso" sem gravar nada.
        if ($request->filled('apelido')) {
            return response()->json(['data' => ['nome' => 'Profissional']], 201);
        }

        $request->merge(['whatsapp' => CadastroProfissional::normalizarWhatsapp($request->input('whatsapp'))]);

        $data = $request->validate([
            'nome'                   => ['required', 'string', 'min:2', 'max:100'],
            'whatsapp'               => ['required', 'string'],
            'email'                  => ['nullable', 'email:rfc', 'max:150'],
            'categoria'              => ['required', Rule::in(CadastroProfissional::CATEGORIAS)],
            'profissao'              => ['required', 'string', 'min:2', 'max:80'],
            'descricao'              => ['nullable', 'string', 'max:1000'],
            'atende_domicilio'       => ['nullable', 'boolean'],
            'tem_site'               => ['required', 'boolean'],
            'tem_google_meu_negocio' => ['required', 'boolean'],
            'nivel_digital'          => ['required', Rule::in(array_keys(CadastroProfissional::NIVEIS))],
            'comunidade_id'          => ['nullable', 'exists:comunidades,id'],
            'origem'                 => ['nullable', 'string', 'max:60'],
        ], [
            'whatsapp.required' => 'Informe o seu WhatsApp com DDD.',
            'nome.required'     => 'Diga o seu nome.',
            'categoria.in'      => 'Escolha uma área válida.',
        ]);

        if (! $data['whatsapp']) {
            throw ValidationException::withMessages(['whatsapp' => ['Confira o WhatsApp: use o DDD e o número (ex.: 21 99999-8888).']]);
        }

        foreach (['nome', 'profissao', 'descricao', 'origem'] as $campo) {
            if (isset($data[$campo])) {
                $data[$campo] = trim(strip_tags($data[$campo])) ?: null;
            }
        }
        $data['email'] = isset($data['email']) ? mb_strtolower(trim($data['email'])) : null;
        $data['origem'] = isset($data['origem']) ? preg_replace('/[^A-Za-z0-9_.\- ]/', '', $data['origem']) : null;

        // Quem já foi aprovado não é alterado por um reenvio; os demais atualizam os dados (corrigir um erro de digitação).
        $existente = CadastroProfissional::where('whatsapp', $data['whatsapp'])->first();
        if ($existente) {
            if (! in_array($existente->status, ['aprovado'], true)) {
                $existente->update($data + ['status' => $existente->status === 'descartado' ? 'novo' : $existente->status]);
            }
        } else {
            CadastroProfissional::create($data);
        }

        // Resposta igual para novo e reenvio: não revela se aquele WhatsApp já estava na base.
        return response()->json(['data' => ['nome' => explode(' ', $data['nome'])[0]]], 201);
    }
}
