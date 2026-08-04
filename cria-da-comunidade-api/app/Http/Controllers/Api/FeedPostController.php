<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeedPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FeedPost::where('publicado', true)
            ->latest();

        if ($request->filled('comunidade_id')) {
            $query->where('comunidade_id', $request->comunidade_id);
        }

        $perPage = min((int) ($request->per_page ?? 8), 20);
        return response()->json($query->paginate($perPage));
    }
}
