<?php

namespace App\Http\Requests;

use App\Models\Transaksi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transaksi = $this->route('transaksi');

        return $transaksi instanceof Transaksi
            && $this->user()?->can('update', $transaksi) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $transaksi = $this->route('transaksi');
        $gantiJenis = $this->has('jenis');
        $jenis = $this->input('jenis', $transaksi?->jenis);

        return [
            'jenis' => ['sometimes', 'required', Rule::in($this->jenisValid())],
            'nominal' => ['sometimes', 'required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'tanggal' => ['sometimes', 'required', 'date'],
            'keterangan' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'kategori_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'tantangan_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'sumber_pemasukan' => [
                Rule::requiredIf($gantiJenis && $jenis === Transaksi::JENIS_PEMASUKAN),
                'nullable',
                Rule::in([
                    Transaksi::SUMBER_UANG_SAKU,
                    Transaksi::SUMBER_PENGHASILAN_SENDIRI,
                ]),
            ],
            'akun_asal_id' => [
                Rule::requiredIf($gantiJenis && $jenis === Transaksi::JENIS_TRANSFER),
                'nullable',
                'integer',
                'min:1',
            ],
            'akun_tujuan_id' => [
                Rule::requiredIf($gantiJenis && $jenis === Transaksi::JENIS_TRANSFER),
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis transaksi wajib diisi.',
            'jenis.in' => 'Jenis transaksi tidak valid.',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.gt' => 'Nominal harus lebih besar dari nol.',
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'sumber_pemasukan.required' => 'Sumber pemasukan wajib dipilih.',
            'akun_asal_id.required' => 'Akun asal wajib dipilih untuk transfer.',
            'akun_tujuan_id.required' => 'Akun tujuan wajib dipilih untuk transfer.',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function jenisValid(): array
    {
        return [
            Transaksi::JENIS_PEMASUKAN,
            Transaksi::JENIS_PENGELUARAN,
            Transaksi::JENIS_TRANSFER,
            Transaksi::JENIS_SALDO_AWAL,
        ];
    }
}
