<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $categoryId = $category instanceof \App\Models\Category ? $category->id : $category;

        return [
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $categoryId,
            'kode_prefix'   => 'nullable|string|max:10',
        ];
    }
}
