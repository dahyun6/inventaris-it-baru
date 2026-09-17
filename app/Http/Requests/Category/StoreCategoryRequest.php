<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|unique:categories,nama_kategori|max:255',
            'kode_prefix'   => 'nullable|string|max:10',
        ];
    }
}
