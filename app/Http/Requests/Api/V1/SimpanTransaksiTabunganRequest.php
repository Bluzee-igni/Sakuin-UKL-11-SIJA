<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SimpanTransaksiTabunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe' => ['required', 'in:setor,tarik'],
            'jumlah' => ['required', 'numeric', 'min:100'],
            'tanggal_transaksi' => ['required', 'date'],
            'kategori_id' => ['nullable', 'exists:kategori_transaksi,id'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'sumber' => ['nullable', 'string', 'max:255'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'tipe.in' => 'Tipe transaksi harus setor atau tarik',
            'jumlah.min' => 'Jumlah transaksi minimal Rp 100',
            'kategori_id.exists' => 'Kategori transaksi tidak ditemukan',
        ];
    }
}
