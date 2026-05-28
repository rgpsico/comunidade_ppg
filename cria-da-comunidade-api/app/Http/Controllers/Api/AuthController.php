<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|string|min:8|confirmed',
            'handle'       => 'nullable|string|max:50|unique:users',
            'whatsapp'     => 'nullable|string|max:20',
            'comunidade_id'=> 'nullable|exists:comunidades,id',
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole('usuario');

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user->load('comunidade', 'roles'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        if (!$user->ativo) {
            return response()->json(['message' => 'Conta desativada.'], 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user->load('comunidade', 'roles'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('comunidade', 'roles'));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => 'sometimes|required|string|max:100',
            'handle'   => 'sometimes|nullable|string|max:50|unique:users,handle,' . $user->id,
            'whatsapp' => 'sometimes|nullable|string|max:20',
            'bio'      => 'sometimes|nullable|string|max:500',
        ]);

        $user->update($data);

        return response()->json($user->fresh()->load('comunidade', 'roles'));
    }
}
