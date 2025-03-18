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
        // Get or create categories
        $mensClothing = $this->getOrCreateCategory('Men\'s Clothing', 'mens-clothing', 'Clothing items for men');
        $womensClothing = $this->getOrCreateCategory('Women\'s Clothing', 'womens-clothing', 'Clothing items for women');
        $footwear = $this->getOrCreateCategory('Footwear', 'footwear', 'Shoes and boots');
        $accessories = $this->getOrCreateCategory('Accessories', 'accessories', 'Fashion accessories');
        
        // Men's Products
        $mensProducts = [
            [
                'name' => 'Classic Fit Denim Jeans',
                'description' => 'Comfortable classic fit denim jeans with a timeless look. Made from high-quality denim that will last for years.',
                'price' => 59.99,
                'stock' => 100,
                'sku' => 'MEN-JEAN-001',
                'featured' => true,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Premium Cotton T-Shirt',
                'description' => 'Soft and breathable premium cotton t-shirt thats perfect for everyday wear. Available in multiple colors.',
                'price' => 24.99,
                'stock' => 200,
                'sku' => 'MEN-TSHIRT-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Slim Fit Oxford Shirt',
                'description' => 'Classic Oxford shirt with a modern slim fit. Perfect for both casual and semi-formal occasions.',
                'price' => 45.99,
                'stock' => 75,
                'sku' => 'MEN-SHIRT-001',
                'featured' => true,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Winter Wool Sweater',
                'description' => 'Warm and cozy wool sweater perfect for the cold winter months. Features ribbed cuffs and hem.',
                'price' => 69.99,
                'stock' => 50,
                'sku' => 'MEN-SWTR-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
        ];
        
        // Women's Products
        $womensProducts = [
            [
                'name' => 'High-Waisted Skinny Jeans',
                'description' => 'Flattering high-waisted skinny jeans that elongate your legs. Made with stretch denim for maximum comfort.',
                'price' => 65.99,
                'stock' => 85,
                'sku' => 'WMN-JEAN-001',
                'featured' => true,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Floral Summer Dress',
                'description' => 'Light and breezy floral summer dress. Perfect for warm days and outdoor events.',
                'price' => 49.99,
                'stock' => 60,
                'sku' => 'WMN-DRESS-001',
                'featured' => true,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'V-Neck Blouse',
                'description' => 'Elegant V-neck blouse made from silky fabric. Can be dressed up or down for any occasion.',
                'price' => 39.99,
                'stock' => 120,
                'sku' => 'WMN-BLOUSE-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Knit Cardigan',
                'description' => 'Cozy knit cardigan with pockets. Versatile layer for transitional weather.',
                'price' => 55.99,
                'stock' => 40,
                'sku' => 'WMN-CARD-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
        ];
        
        // Footwear Products
        $footwearProducts = [
            [
                'name' => 'Canvas Sneakers',
                'description' => 'Classic canvas sneakers that go with everything. Comfortable for all-day wear.',
                'price' => 45.99,
                'stock' => 150,
                'sku' => 'FTW-SNKR-001',
                'featured' => true,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Leather Dress Shoes',
                'description' => 'Premium leather dress shoes with a polished finish. Perfect for formal occasions.',
                'price' => 129.99,
                'stock' => 30,
                'sku' => 'FTW-DRESS-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Hiking Boots',
                'description' => 'Durable hiking boots with excellent traction. Waterproof and ready for adventure.',
                'price' => 89.99,
                'stock' => 45,
                'sku' => 'FTW-BOOT-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
        ];
        
        // Accessories Products
        $accessoriesProducts = [
            [
                'name' => 'Leather Wallet',
                'description' => 'Slim leather wallet with multiple card slots and a bill compartment. Made from genuine leather.',
                'price' => 35.99,
                'stock' => 100,
                'sku' => 'ACC-WALLET-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Silk Scarf',
                'description' => 'Elegant silk scarf with a beautiful pattern. Adds a touch of sophistication to any outfit.',
                'price' => 29.99,
                'stock' => 80,
                'sku' => 'ACC-SCARF-001',
                'featured' => false,
                'images' => ['placeholder.jpg'],
            ],
            [
                'name' => 'Aviator Sunglasses',
                'description' => 'Classic aviator sunglasses with UV protection. Timeless style for any face shape.',
                'price' => 75.99,
                'stock' => 60,
                'sku' => 'ACC-SNGLS-001',
                'featured' => true,
                'images' => ['placeholder.jpg'],
            ],
        ];
        
        // Create products and attach categories
        $this->createProductsForCategory($mensProducts, $mensClothing);
        $this->createProductsForCategory($womensProducts, $womensClothing);
        $this->createProductsForCategory($footwearProducts, $footwear);
        $this->createProductsForCategory($accessoriesProducts, $accessories);
        
        $this->command->info('Products seeded successfully!');
    }
    
    /**
     * Get or create a category
     */
    private function getOrCreateCategory($name, $slug, $description)
    {
        $category = Category::where('slug', $slug)->first();
        
        if (!$category) {
            $category = Category::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'active' => true,
            ]);
            
            $this->command->info("Created category: {$name}");
        }
        
        return $category;
    }
    
    /**
     * Create products for a category
     */
    private function createProductsForCategory($products, $category)
    {
        $this->command->info("Creating products for category: {$category->name}");
        
        foreach ($products as $productData) {
            // Check if product already exists
            $existingProduct = Product::where('sku', $productData['sku'])->first();
            
            if (!$existingProduct) {
                $product = Product::create([
                    'name' => $productData['name'],
                    'slug' => Str::slug($productData['name']),
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'sku' => $productData['sku'],
                    'images' => $productData['images'],
                    'featured' => $productData['featured'],
                    'active' => true,
                ]);
                
                $product->categories()->attach($category);
                
                $this->command->info("  - Created product: {$productData['name']}");
            } else {
                $this->command->info("  - Product already exists: {$productData['name']}");
            }
        }
    }
}