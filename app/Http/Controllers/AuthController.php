<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\SigninAuthRequest;
use App\Http\Requests\Auth\SignupAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Validator;

class AuthController extends Controller
{
    // Método de registro
    public function signup(SignupAuthRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => 'Bearer ' . $token
        ]);
    }

    // Método de login
    public function signin(SigninAuthRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retorna os dados do usuário e o token
        $user = User::where('email', $request->email)->first();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => 'Bearer ' . $token
        ]);
    }
}
