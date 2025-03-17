<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics' => 'Electronic devices and gadgets',
            'Clothing' => 'Fashion clothing for men and women',
            'Home & Kitchen' => 'Products for your home and kitchen',
            'Books' => 'Books across various genres',
            'Sports & Outdoors' => 'Sports equipment and outdoor gear',
            'Beauty & Personal Care' => 'Beauty products and personal care items',
            'Toys & Games' => 'Toys and games for all ages',
        ];
        
        foreach ($categories as $name => $description) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'active' => true,
            ]);
        }
    }
}