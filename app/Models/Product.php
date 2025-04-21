<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sku',
        'images',
        'available_sizes',
        'available_colors',
        'specifications',
        'featured',
        'active',
    ];
    
    protected $casts = [
        'images' => 'array',
        'available_sizes' => 'array',
        'available_colors' => 'array',
        'specifications' => 'array',
        'featured' => 'boolean',
        'active' => 'boolean',
        'price' => 'decimal:2',
    ];
    
    // Relationships
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
    
    public function swipes()
    {
        return $this->hasMany(Swipe::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
    
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
