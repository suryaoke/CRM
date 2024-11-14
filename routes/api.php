<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\PerusahaanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('profile', [AuthController::class, 'updateProfile']);
});

// Super Admin //
Route::group(['middleware' => 'api', 'role:superAdmin'], function ($router) {
    Route::post('perusahaan', [PerusahaanController::class, 'store']);
});


// Manager //
Route::group(['middleware' => 'api', 'role:manager'], function ($router) {

    Route::post('karyawan', [KaryawanController::class, 'store']);
    Route::post('karyawan/update/{id}', [KaryawanController::class, 'update']);
    Route::delete('karyawan/delete/{id}', [KaryawanController::class, 'delete']);
});

// Manager dan karyawan//
Route::group(['middleware' => 'api', 'role:manager,karyawan'], function ($router) {

    Route::get('karyawan/all', [KaryawanController::class, 'KaryawanAll']);
    Route::get('karyawan/{id}', [KaryawanController::class, 'KaryawanDetail']);
});
