<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'image',
        'is_hidden'
       
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_hidden' => 'boolean'
    ];

   // In your Product model
protected $appends = ['image_url'];

public function getImageUrlAttribute()
{
    return $this->image ? asset("storage/{$this->image}") : null;
}
}