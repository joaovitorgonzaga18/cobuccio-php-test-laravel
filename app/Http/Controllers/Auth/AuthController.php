<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {    
    public function login(Request $request): JsonResponse {
        $request->validate([
            'email' => 'required|email|max:100',
            'password' => 'required|max:100',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->buildResponse(['success' => false, 'message' => 'Invalid Credentials'], 401);
        }

        $token = $user->createToken('auth_token');

        return $this->buildResponse([
            'success' => true,
            'message' => 'Logged in',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return $this->buildResponse(['success' => true, 'message' => 'Logged out'], 200);
    }
}
