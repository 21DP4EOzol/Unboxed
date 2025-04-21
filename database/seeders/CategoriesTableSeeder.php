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
                'subcategories' => ['T-Shirts', 'Shirts', 'Pants', 'Jeans', 'Jackets']
            ],
            'Women\'s Clothing' => [
                'description' => 'Clothing items for women',
                'subcategories' => ['Dresses', 'Tops', 'Skirts', 'Pants', 'Outerwear']
            ],
            'Footwear' => [
                'description' => 'Shoes and boots for all occasions',
                'subcategories' => ['Sneakers', 'Boots', 'Sandals']
            ],
            'Accessories' => [
                'description' => 'Fashion accessories to complete your look',
                'subcategories' => ['Watches', 'Jewelry', 'Bags', 'Hats', 'Scarves', 'Belts']
            ],
            'Electronics' => [
                'description' => 'Electronic devices and gadgets',
                'subcategories' => ['Smartphones', 'Laptops', 'Headphones', 'Cameras']
            ],
            'Home & Kitchen' => [
                'description' => 'Products for your home and kitchen',
                'subcategories' => ['Cookware', 'Furniture', 'Decor', 'Bedding']
            ],
            'Beauty & Personal Care' => [
                'description' => 'Beauty products and personal care items',
                'subcategories' => ['Skincare', 'Makeup', 'Haircare', 'Fragrances']
            ],
        ];
        
        $createdCount = 0;
        
        foreach ($categories as $name => $data) {
            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $data['description'],
                'active' => true,
            ]);
            
            $createdCount++;
            
            // Create subcategories to reach a total of 15
            foreach ($data['subcategories'] as $subName) {
                if ($createdCount >= 15) {
                    break 2; // Break out of both loops once we hit 15
                }
                
                $fullName = "$subName " . Str::singular($name);
                
                Category::create([
                    'name' => $fullName,
                    'slug' => Str::slug($fullName),
                    'description' => "Subcategory of $name",
                    'active' => true,
                ]);
                
                $createdCount++;
            }
        }
        
        $this->command->info("$createdCount categories created.");
    }
}