<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories with barangs count.
     */
    public function getAllWithCount(): Collection
    {
        return Category::withCount('barangs')->get();
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        $cat = Category::create($data);

        ActivityLogService::log('Kategori', 'CREATE', $cat->nama_kategori, "Menambahkan kategori master baru: {$cat->nama_kategori}", $cat->id);

        return $cat;
    }

    /**
     * Update an existing category.
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        ActivityLogService::log('Kategori', 'UPDATE', $category->nama_kategori, "Memperbarui kategori: {$category->nama_kategori}", $category->id);

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): bool
    {
        $nama = $category->nama_kategori;
        $id = $category->id;
        $res = (bool) $category->delete();

        if ($res) {
            ActivityLogService::log('Kategori', 'DELETE', $nama, "Menghapus kategori: {$nama}", $id);
        }

        return $res;
    }
}
