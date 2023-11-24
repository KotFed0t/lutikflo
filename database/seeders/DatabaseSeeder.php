<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\DeliveryPriceSetting;
use App\Models\Flower;
use App\Models\FlowerType;
use App\Models\Image;
use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;

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

        if (env('app_env') === 'production') {
            MoonshineUserRole::query()->create(['name' => 'Worker']);

            DeliveryPriceSetting::query()->create([
                'fix_price_distance_km' => 5,
                'fix_price' => 400,
                'price_per_one_km' => 50,
            ]);
        }

        if (env('app_env') === 'local') {
            FlowerType::factory(5)->create();
            Flower::factory(7)->create();
            Category::factory(8)->create();
            Product::factory(30)->create();
            Image::factory(16)->create();
            Package::factory(3)->create();

            $flower_product_rows = [];
            foreach (range(1, 30) as $index) {
                $flower_product_rows[] = [
                    'flower_id' => rand(1, 7),
                    'product_id' => rand(1, 30),
                    'count' => rand(3, 11),
                    'is_changeable_flower_count' => fake()->boolean()
                ];
            }
            DB::table('flower_product')->insert($flower_product_rows);

            $package_product_rows = [];
            foreach (range(1, 15) as $index) {
                $package_product_rows[] = [
                    'package_id' => rand(1, 3),
                    'product_id' => rand(1, 30),
                ];
            }
            DB::table('package_product')->insert($package_product_rows);

            DeliveryPriceSetting::query()->create([
                'fix_price_distance_km' => 5,
                'fix_price' => 400,
                'price_per_one_km' => 50,
            ]);

            User::query()->create(['phone' => '79137098882']);
        }

    }
}
