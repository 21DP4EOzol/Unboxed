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
            'Men\'s Clothing' => [
                'description' => 'Clothing items for men',
                'slug' => 'mens-clothing'
            ],
            'Women\'s Clothing' => [
                'description' => 'Clothing items for women',
                'slug' => 'womens-clothing'
            ],
            'Footwear' => [
                'description' => 'Shoes and boots for all occasions',
                'slug' => 'footwear'
            ],
            'Accessories' => [
                'description' => 'Fashion accessories to complete your look',
                'slug' => 'accessories'
            ],
            'Electronics' => [
                'description' => 'Electronic devices and gadgets',
                'slug' => 'electronics'
            ],
            'Home & Kitchen' => [
                'description' => 'Products for your home and kitchen',
                'slug' => 'home-kitchen'
            ],
            'Beauty & Personal Care' => [
                'description' => 'Beauty products and personal care items',
                'slug' => 'beauty-personal-care'
            ],
        ];
        
        $this->command->info('Seeding categories...');
        
        foreach ($categories as $name => $data) {
            $category = Category::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $name,
                    'description' => $data['description'],
                    'active' => true,
                ]
            );
            
            $this->command->info("Category '{$name}' processed");
        }
        
        $this->command->info('Categories seeded successfully!');
    }
}