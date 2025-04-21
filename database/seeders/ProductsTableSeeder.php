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
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run the category seeder first.');
            return;
        }
        
        // Define available sizes for clothing
        $clothingSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        
        // Define available colors
        $colors = ['Red', 'Blue', 'Green', 'Black', 'White', 'Yellow', 'Purple', 'Pink', 'Gray', 'Orange', 'Brown'];
        
        // Define materials by category type
        $materials = [
            'Clothing' => ['Cotton', 'Polyester', 'Wool', 'Linen', 'Silk', 'Denim', 'Leather'],
            'Footwear' => ['Leather', 'Canvas', 'Rubber', 'Synthetic', 'Suede'],
            'Electronics' => ['Plastic', 'Metal', 'Glass'],
            'Accessories' => ['Metal', 'Leather', 'Fabric', 'Plastic', 'Wood'],
            'Home' => ['Wood', 'Metal', 'Glass', 'Ceramic', 'Plastic', 'Cotton']
        ];
        
        for ($i = 1; $i <= 100; $i++) {
            // Randomly select 1-3 categories for this product
            $selectedCategories = $categories->random(rand(1, 3));
            
            // Determine product type from the first category for consistent specifications
            $categoryName = $selectedCategories->first()->name;
            $productType = 'Other';
            
            if (Str::contains($categoryName, ['Clothing', 'T-Shirt', 'Shirt', 'Pants', 'Dress', 'Top', 'Skirt', 'Jacket', 'Outerwear'])) {
                $productType = 'Clothing';
            } elseif (Str::contains($categoryName, ['Footwear', 'Shoes', 'Boots', 'Sneakers', 'Sandals'])) {
                $productType = 'Footwear';
            } elseif (Str::contains($categoryName, ['Electronics', 'Smartphone', 'Laptop', 'Headphones', 'Camera'])) {
                $productType = 'Electronics';
            } elseif (Str::contains($categoryName, ['Accessories', 'Watch', 'Jewelry', 'Bag', 'Hat', 'Scarf', 'Belt'])) {
                $productType = 'Accessories';
            } elseif (Str::contains($categoryName, ['Home', 'Kitchen', 'Cookware', 'Furniture', 'Decor', 'Bedding'])) {
                $productType = 'Home';
            }
            
            // Generate sizes based on product type
            $availableSizes = null;
            if ($productType === 'Clothing' || $productType === 'Footwear') {
                $sizeCount = rand(3, 6);
                $availableSizes = array_slice($clothingSizes, 0, $sizeCount);
            }
            
            // Generate colors
            $colorCount = rand(3, 6);
            $availableColors = array_slice($colors, 0, $colorCount);
            
            // Generate specifications
            $specs = [];
            
            // Always add material
            if (isset($materials[$productType])) {
                $materialOptions = $materials[$productType];
                $specs['Material'] = $materialOptions[array_rand($materialOptions)];
            }
            
            // Add specifications based on product type
            if ($productType === 'Clothing') {
                $specs['Fit'] = ['Regular', 'Slim', 'Relaxed', 'Oversized'][array_rand(['Regular', 'Slim', 'Relaxed', 'Oversized'])];
                $specs['Care'] = ['Machine wash', 'Hand wash', 'Dry clean only'][array_rand(['Machine wash', 'Hand wash', 'Dry clean only'])];
            } elseif ($productType === 'Electronics') {
                $specs['Power'] = rand(100, 500) . 'W';
                $specs['Warranty'] = rand(1, 3) . ' year(s)';
                $specs['Connectivity'] = ['Bluetooth', 'WiFi', 'USB-C', 'Lightning'][array_rand(['Bluetooth', 'WiFi', 'USB-C', 'Lightning'])];
            } elseif ($productType === 'Footwear') {
                $specs['Sole'] = ['Rubber', 'Leather', 'Synthetic'][array_rand(['Rubber', 'Leather', 'Synthetic'])];
                $specs['Closure'] = ['Laces', 'Velcro', 'Slip-on', 'Buckle', 'Zipper'][array_rand(['Laces', 'Velcro', 'Slip-on', 'Buckle', 'Zipper'])];
            }
            
            // Common specs
            $specs['Country of Origin'] = ['USA', 'China', 'India', 'Vietnam', 'Italy', 'Bangladesh'][array_rand(['USA', 'China', 'India', 'Vietnam', 'Italy', 'Bangladesh'])];
            
            // Create the product
            $productName = "Product $i - " . Str::title($productType);
            $product = Product::create([
                'name' => $productName,
                'slug' => Str::slug($productName),
                'description' => "This is a high-quality $productType product with premium materials and exceptional craftsmanship. Perfect for everyday use and special occasions.",
                'price' => rand(999, 19999) / 100, // Random price between $9.99 and $199.99
                'stock' => rand(5, 100),
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'images' => null, // We don't have actual images
                'available_sizes' => $availableSizes,
                'available_colors' => $availableColors,
                'specifications' => $specs,
                'featured' => rand(0, 10) < 2, // 20% chance of being featured
                'active' => true,
            ]);
            
            // Attach categories
            $product->categories()->attach($selectedCategories->pluck('id')->toArray());
        }
        
        $this->command->info('100 products created and linked to categories.');
    }
}