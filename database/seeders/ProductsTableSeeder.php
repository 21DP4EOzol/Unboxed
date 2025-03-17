<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();
        $clothing = Category::where('slug', 'clothing')->first();
        $homeKitchen = Category::where('slug', 'home-kitchen')->first();
        
        // Electronics products
        $electronicsProducts = [
            [
                'name' => 'Smartphone X1',
                'description' => 'Latest smartphone with advanced features',
                'price' => 799.99,
                'stock' => 100,
                'sku' => 'SMRT-X1',
                'featured' => true,
            ],
            [
                'name' => 'Laptop Pro',
                'description' => 'Powerful laptop for professionals',
                'price' => 1299.99,
                'stock' => 50,
                'sku' => 'LPT-PRO',
                'featured' => true,
            ],
            [
                'name' => 'Wireless Earbuds',
                'description' => 'High-quality wireless earbuds with noise cancellation',
                'price' => 129.99,
                'stock' => 200,
                'sku' => 'WLB-EAR',
                'featured' => false,
            ],
        ];
        
        $this->createProductsForCategory($electronicsProducts, $electronics);
        
        // Clothing products
        $clothingProducts = [
            [
                'name' => 'Men\'s Casual T-Shirt',
                'description' => 'Comfortable casual t-shirt for men',
                'price' => 24.99,
                'stock' => 300,
                'sku' => 'MNT-SHT',
                'featured' => false,
            ],
            [
                'name' => 'Women\'s Slim Jeans',
                'description' => 'Stylish slim jeans for women',
                'price' => 49.99,
                'stock' => 150,
                'sku' => 'WMJ-SLM',
                'featured' => true,
            ],
        ];
        
        $this->createProductsForCategory($clothingProducts, $clothing);
        
        // Home & Kitchen products
        $homeProducts = [
            [
                'name' => 'Coffee Maker',
                'description' => 'Automatic coffee maker for your kitchen',
                'price' => 89.99,
                'stock' => 75,
                'sku' => 'HMK-CFE',
                'featured' => false,
            ],
            [
                'name' => 'Blender Set',
                'description' => 'Multi-purpose blender set for various kitchen tasks',
                'price' => 69.99,
                'stock' => 100,
                'sku' => 'HMK-BLN',
                'featured' => true,
            ],
        ];
        
        $this->createProductsForCategory($homeProducts, $homeKitchen);
    }
    
    private function createProductsForCategory($products, $category)
    {
        foreach ($products as $productData) {
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'sku' => $productData['sku'],
                'images' => ['placeholder.jpg'],
                'featured' => $productData['featured'],
                'active' => true,
            ]);
            
            $product->categories()->attach($category);
        }
    }
}