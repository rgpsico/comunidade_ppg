<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AnalyticsController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'        => 'required|in:page_view,click',
            'screen'      => 'nullable|string|max:100',
            'entity_type' => 'nullable|in:profissional,evento,projeto,vaga',
            'entity_id'   => 'nullable|integer',
            'entity_name' => 'nullable|string|max:200',
            'session_id'  => 'nullable|string|max:64',
        ]);

        AnalyticsEvent::create([
            ...$data,
            'user_id' => $request->user()?->id,
            'ip_hash' => Hash::make($request->ip()),
            'created_at' => now(),
        ]);

        return response()->json(['ok' => true], 201);
    }
}
