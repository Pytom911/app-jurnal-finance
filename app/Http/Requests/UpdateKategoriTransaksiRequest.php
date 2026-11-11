<?php

namespace App\Http\Requests;

use App\Models\KategoriTransaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kategori = $this->route('kategori');

        return $kategori instanceof KategoriTransaksi
            && $this->user()?->can('update', $kategori) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'required', 'string', 'max:100'],
            'jenis' => [
                'sometimes',
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
