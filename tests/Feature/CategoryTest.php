<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();

        //only 2 category with active products
        $categories = Category::factory(4)->create([
            'is_active' => true
        ]);

        Product::factory()->create([
            'category_id' => $categories[0]->id,
            'is_active' => true
        ]);

        Product::factory()->create([
            'category_id' => $categories[0]->id,
            'is_active' => true
        ]);

        Product::factory()->create([
            'category_id' => $categories[1]->id,
            'is_active' => false
        ]);

        Product::factory()->create([
            'category_id' => $categories[3]->id,
            'is_active' => true
        ]);
    }

    public function test_get_categories(): void
    {
        $response = $this->get('/api/categories');

        $response->assertStatus(200);

        $this->assertCount(2, $response->json()['data']);

    }

    public function test_get_categories_with_products()
    {
        $response = $this->get('/api/categories/products');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json()['data']);
    }

    public function test_get_category_by_slug()
    {
        Category::factory()->create([
            'slug' => 'test_slug',
            'is_active' => true
        ]);

        $response = $this->get('/api/categories/test_slug');

        $response->assertStatus(200)->assertJsonPath('data.slug', 'test_slug');
    }

    public function test_get_inactive_category_by_slug()
    {
        Category::factory()->create([
            'slug' => 'test_slug',
            'is_active' => false
        ]);

        $response = $this->get('/api/categories/test_slug');

        $response->assertStatus(404);
    }
}
