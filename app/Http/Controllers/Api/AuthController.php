<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UserloginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(UserloginRequest $request)
    {

        if (!$token = Auth::guard('api')->attempt($request->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        return (new UserResource(Auth::guard('api')->user()))
            ->additional(['meta' => ['token' => $token]]);
    } //end method

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        // Get the authenticated user
        $user = Auth::guard('api')->user();

        // Update the user's name and email
        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }


    public function logout(): JsonResponse
    {
        // Invalidate the token
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Berhasil Logout']);
    }
}
