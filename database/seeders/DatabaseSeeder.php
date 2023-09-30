<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Flower;
use App\Models\FlowerType;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        FlowerType::factory(5)->create();
        Flower::factory(7)->create();
        Category::factory(8)->create();
        Product::factory(50)->create();
        Image::factory(100)->create();

        $flower_product_rows = [];
        foreach (range(1, 80) as $index) {
            $flower_product_rows[] = [
                'flower_id' => rand(1, 7),
                'product_id' => rand(1, 50),
                'count' => rand(3, 11)
            ];
        }

        DB::table('flower_product')->insert($flower_product_rows);



    }
}
