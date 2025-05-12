<?php
// Written & debugged by: Tech Ngoun Leang & Ratanakvesal Thong
// Tested by: Tech Ngoun Leang
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Define relationships here if needed (e.g., a category can have many products)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

