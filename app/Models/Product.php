<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if it's named 'products' by default)
    protected $table = 'products';

    // Define the fields that are mass assignable
    protected $fillable = [
        'name', 
        'description', 
        'price', 
        'category_id', 
        'stock_quantity', 
        'reorder_level',
        'photo_path'  
    ];

    // You can also define any relationships here if necessary (e.g., belongsTo, hasMany, etc.)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

