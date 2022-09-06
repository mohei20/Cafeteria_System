<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'price',
        'quantity',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}
