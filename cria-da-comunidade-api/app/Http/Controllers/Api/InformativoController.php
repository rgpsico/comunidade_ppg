<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Informativo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InformativoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Informativo::where('publicado', true)
            ->orderByDesc('created_at');

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        $perPage = min((int) ($request->per_page ?? 30), 100);
        return response()->json($query->paginate($perPage));
    }
}
