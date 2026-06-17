<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('barangs')->get(); // Mengambil kategori sekalian menghitung jumlah barang di dalamnya
        return view('category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|unique:categories,nama_kategori|max:255',
            'kode_prefix'   => 'nullable|string|max:10', // <--- Tambahkan baris ini
        ]);

        Category::create($request->all());
        return redirect()->route('category.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori,' . $category->id,
            'kode_prefix'   => 'nullable|string|max:10', // <--- Tambahkan baris ini juga
        ]);

        $category->update($request->all());
        return redirect()->route('category.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus!');
    }
}