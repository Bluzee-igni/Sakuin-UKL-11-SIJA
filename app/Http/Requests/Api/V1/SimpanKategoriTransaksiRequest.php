<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SimpanKategoriTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'ikon' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'warna.regex' => 'Format warna harus valid HEX color (contoh: #FF0000)',
        ];
    }
}
