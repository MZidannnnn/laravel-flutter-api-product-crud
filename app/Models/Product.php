<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    use Hasfactory;

    protected $fillable = [
        'image',
        'title',
        'description',
        'price',
        'stock',
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/products/' . $value),
        );
    }
}
