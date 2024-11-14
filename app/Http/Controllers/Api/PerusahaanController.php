<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerusahaanStoreRequest;
use App\Http\Resources\PerusahaanResource;
use App\Http\Resources\UserResource;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    //Membuat Perusahaan oleh [Super Admin] //
    public function store(PerusahaanStoreRequest $request)
    {

        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->assignRole('manager');


        $perusahaan = Perusahaan::create([
            'user_id' => $user->id,
            'nama' => $data['perusahaan'][0]['nama'],
            'email' => $data['perusahaan'][0]['email'],

        ]);


        return response()->json([
            'success' => true,
            'message' => 'Perusahaan Berhasil dibuat',
            'data' => [
                'Manager' => new UserResource($user),
                'perusahaan' => new PerusahaanResource($perusahaan),
            ]
        ], 200);
    }

}
