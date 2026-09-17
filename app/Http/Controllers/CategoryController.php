<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    public function index(): View
    {
        $categories = $this->categoryService->getAllWithCount();

        return view('category.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('category.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()->route('category.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->delete($category);

        return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus!');
    }
}