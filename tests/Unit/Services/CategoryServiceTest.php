<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryService();
    }

    public function test_can_create_category(): void
    {
        $category = $this->service->create([
            'nama_kategori' => 'Laptop',
            'kode_prefix'   => 'LPT',
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'nama_kategori' => 'Laptop',
            'kode_prefix'   => 'LPT',
        ]);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create(['nama_kategori' => 'Old Name']);

        $updated = $this->service->update($category, [
            'nama_kategori' => 'New Name',
        ]);

        $this->assertEquals('New Name', $updated->fresh()->nama_kategori);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $result = $this->service->delete($category);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
