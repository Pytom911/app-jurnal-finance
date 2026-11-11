<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTantanganRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:150'],
            'target' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama tantangan wajib diisi.',
            'nama.max' => 'Nama tantangan maksimal 150 karakter.',
            'target.required' => 'Target tantangan wajib diisi.',
            'target.numeric' => 'Target harus berupa angka.',
            'target.gt' => 'Target harus lebih besar dari nol.',
        ];
    }
}
