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
        'direction',
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
    
    // Scopes
    public function scopeLiked($query)
    {
        return $query->where('direction', 'right');
    }
    
    public function scopeDisliked($query)
    {
        return $query->where('direction', 'left');
    }
}