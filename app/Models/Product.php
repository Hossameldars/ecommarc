<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

protected $fillable = ['name', 'catagory_id', 'description', 'image', 'price'];

public function category()
{
    return $this->belongsTo(Category::class, 'catagory_id'); 
}

    protected $casts = [
        'price' => 'decimal:2',
    ];
}