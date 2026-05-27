<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SimpanTargetTabunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorisasi ditangani di middleware auth
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'jumlah_target' => ['required', 'numeric', 'min:1000'],
            'rencana_harian' => ['nullable', 'numeric', 'min:0'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_target' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'prioritas' => ['nullable', 'in:rendah,sedang,tinggi'],
            'ikon' => ['nullable', 'string', 'max:50'],
            'warna' => ['nullable', 'string', 'max:20', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'jumlah_target.min' => 'Jumlah target minimal Rp 1.000',
            'tanggal_target.after_or_equal' => 'Tanggal target tidak boleh lebih awal dari tanggal mulai',
            'warna.regex' => 'Format warna harus valid HEX color (contoh: #FF0000)',
        ];
    }
}
