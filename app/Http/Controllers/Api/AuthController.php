<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUsuarioRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function store(StoreUsuarioRequest $request): JsonResponse
    {
        $usuario = User::create($request->validated());

        return response()->json([
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'login' => $usuario->login,
            'criado_em' => $usuario->criado_em?->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'login' => $request->validated('login'),
            'password' => $request->validated('senha'),
        ];

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Login ou senha inválidos.',
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id' => $user->id,
                'nome' => $user->nome,
                'login' => $user->login,
            ],
        ]);
    }

    public function logoff(\Illuminate\Http\Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logoff realizado com sucesso.']);
    }
}
