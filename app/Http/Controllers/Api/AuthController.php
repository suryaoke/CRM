<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserloginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(UserloginRequest $request)
    {
        // autentikasi
        if (!$token = Auth::guard('api')->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Jika berhasil
        return (new UserResource(Auth::guard('api')->user()))
            ->additional(['meta' => ['token' => $token]]);
    } //end method

    public function logout(): JsonResponse
    {
        // Invalidate the token
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
