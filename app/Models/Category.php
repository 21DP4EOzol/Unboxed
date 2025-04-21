<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'active',
    ];
    
    protected $casts = [
        'active' => 'boolean',
    ];
    
    // Relationships
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }
    
    // Relationship with swipes
    public function swipes()
    {
        return $this->hasMany(Swipe::class);
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}