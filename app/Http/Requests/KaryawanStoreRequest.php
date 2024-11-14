<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KaryawanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'akun' => 'required|array',
            'akun.*.name' => 'required',
            'akun.*.email' => 'required|email',
            'akun.*.password' => 'required|string|min:6',
            'karyawan' => 'required|array',
            'karyawan.*.nama' => 'required',
            'karyawan.*.no_tlp' => 'required',
            'karyawan.*.alamat' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Ada kesalahan validasi',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
