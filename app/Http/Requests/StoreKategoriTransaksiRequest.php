<?php

namespace App\Http\Requests;

use App\Models\KategoriTransaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'jenis' => [
                'required',
                Rule::in([
                    KategoriTransaksi::JENIS_PEMASUKAN,
                    KategoriTransaksi::JENIS_PENGELUARAN,
                ]),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.max' => 'Nama kategori maksimal 100 karakter.',
            'jenis.required' => 'Jenis kategori wajib dipilih.',
            'jenis.in' => 'Jenis kategori tidak valid.',
        ];
    }
}
