<?php

namespace App\Http\Requests;

use App\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTransaksiRequest extends FormRequest
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
            'search' => ['nullable', 'string', 'max:100'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'jenis' => [
                'nullable',
                Rule::in([
                    Transaksi::JENIS_PEMASUKAN,
                    Transaksi::JENIS_PENGELUARAN,
                    Transaksi::JENIS_TRANSFER,
                    Transaksi::JENIS_SALDO_AWAL,
                ]),
            ],
            'kategori_id' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal_sampai.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
            'jenis.in' => 'Jenis transaksi tidak valid.',
        ];
    }
}
