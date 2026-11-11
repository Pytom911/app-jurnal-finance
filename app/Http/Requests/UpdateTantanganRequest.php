<?php

namespace App\Http\Requests;

use App\Models\Tantangan;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTantanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        $tantangan = $this->route('tantangan');

        return $tantangan instanceof Tantangan
            && $this->user()?->can('update', $tantangan) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'required', 'string', 'max:150'],
            'target' => ['sometimes', 'required', 'numeric', 'gt:0', 'max:9999999999999.99'],
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
