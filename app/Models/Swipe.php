<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Swipe extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'product_id',
        'category_id',
        'direction',
        'type',
    ];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    // Scopes
    public function scopeLiked($query)
    {
        return $query->where('direction', 'right');
    }
    
    public function scopeDisliked($query)
    {
        return $query->where('direction', 'left');
    }
    
    public function scopeProductSwipes($query)
    {
        return $query->whereNotNull('product_id')->whereNull('category_id');
    }
    
    public function scopeCategorySwipes($query)
    {
        return $query->whereNotNull('category_id')->whereNull('product_id');
    }
}