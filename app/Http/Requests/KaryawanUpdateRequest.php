<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class KaryawanUpdateRequest extends FormRequest
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
            'akun' => 'nullable|array',
            'akun.*.name' => 'nullable|string|max:255',
            'akun.*.email' => 'nullable|email|unique:users,email,' . $this->route('id'),
            'akun.*.password' => 'nullable|string|min:6',

            'karyawan' => 'nullable|array',
            'karyawan.*.nama' => 'nullable|string|max:255',
            'karyawan.*.no_tlp' => 'nullable|string|max:15',
            'karyawan.*.alamat' => 'nullable|string|max:255',
        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
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
