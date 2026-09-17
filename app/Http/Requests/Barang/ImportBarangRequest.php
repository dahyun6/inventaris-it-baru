<?php

namespace App\Http\Requests\Barang;

use Illuminate\Foundation\Http\FormRequest;

class ImportBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ];
    }
}
