<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KaryawanStoreRequest;
use App\Http\Requests\KaryawanUpdateRequest;
use App\Http\Resources\KaryawanResource;
use App\Http\Resources\UserResource;
use App\Models\Karyawan;
use App\Models\Perusahaan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    // membuat karyawan oleh [manager] //
    public function store(KaryawanStoreRequest $request)
    {

        $data = $request->validated();
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ada',
            ], 401);
        }

        // Find the associated company for the authenticated user
        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }

        $user = User::create([
            'name' => $data['akun'][0]['name'],
            'email' => $data['akun'][0]['email'],
            'password' => bcrypt($data['akun'][0]['password']),
        ]);
        $user->assignRole('karyawan');


        $karyawan = Karyawan::create([
            'user_id' => $user->id,
            'nama' => $data['karyawan'][0]['nama'],
            'no_tlp' => $data['karyawan'][0]['no_tlp'],
            'alamat' => $data['karyawan'][0]['alamat'],
            'perusahaan_id' => $perusahaan->id,
        ]);


        return response()->json([
            'success' => true,
            'message' => 'karyawan Berhasil dibuat',
            'data' => [
                'akun' => new UserResource($user),
                'karyawan' => new KaryawanResource($karyawan),

            ]
        ], 200);
    } // end method

    // Mengupdate karyawan oleh [manager] //

    public function update(KaryawanUpdateRequest $request, $id)
    {

        $data = $request->validated();

        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ada',
            ], 401);
        }

        $perusahaan = Perusahaan::where(
            'user_id',
            $user->id
        )->first();
        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }

        $karyawan = Karyawan::where('id', $id)->where('perusahaan_id', $perusahaan->id)->first();
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }


        $user = $karyawan->user;
        if ($data['akun']) {
            $user->name = $data['akun'][0]['name'] ?? $user->name;
            $user->email = $data['akun'][0]['email'] ?? $user->email;
            if (isset($data['akun'][0]['password'])) {
                $user->password = bcrypt($data['akun'][0]['password']);
            }

            $user->save();
        }

        $karyawan->nama = $data['karyawan'][0]['nama'] ?? $karyawan->nama;
        $karyawan->no_tlp = $data['karyawan'][0]['no_tlp'] ?? $karyawan->no_tlp;
        $karyawan->alamat = $data['karyawan'][0]['alamat'] ?? $karyawan->alamat;
        $karyawan->save();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil diperbarui',
            'data' => [
                'akun' => new UserResource($user),
                'karyawan' => new KaryawanResource($karyawan),
            ]
        ], 200);
    } // end method

    // Delete karyawan oleh [manager] //
    public function delete($id)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ada',
            ], 401);
        }


        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }


        $karyawan = Karyawan::find($id);
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        // Delete the associated User record
        $user = User::find($karyawan->user_id);
        if ($user) {
            $user->delete();
        }


        $karyawan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dihapus',
        ], 200);
    }

    // Melihat semua karyawan oleh [manager, karyawan] .
    public function KaryawanAll()
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ada',
            ], 401);
        }


        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }

        $karyawan = Karyawan::where('perusahaan_id', $perusahaan->id)->get();
        if ($karyawan->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada karyawan ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => KaryawanResource::collection($karyawan),
        ], 200);
    } // end method


    // Melihat detail karyawan oleh [manager, karyawan]
    public function KaryawanDetail($id)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ada',
            ], 401);
        }


        $perusahaan = Perusahaan::where('user_id', $user->id)->first();
        if (!$perusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan tidak ditemukan',
            ], 404);
        }


        $karyawan = Karyawan::where('perusahaan_id', $perusahaan->id)->where('id', $id)->first();
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new KaryawanResource($karyawan),
        ], 200);
    } // end method



}
