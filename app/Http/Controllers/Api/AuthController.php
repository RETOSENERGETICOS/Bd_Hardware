<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = User::with('role')->where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth', ['role' => $user->role->name])->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }
        return response()->json('Datos Incorrectos', 401);
    }

    public function logout(Request $request): JsonResponse
    {
        if($request->user()->tokens()->delete()){
            return response()->json('Sesion cerrada');
        }
        return response()->json('Error al cerrar sesion');
    }

    public function verify(Request $request): JsonResponse {
        $user = User::with('role')->find($request->user()->id);
        return response()->json($user);
    }
}
