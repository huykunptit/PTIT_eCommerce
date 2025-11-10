<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductReview extends Model
{
    use HasFactory;
    
    protected $fillable = ['product_id', 'user_id', 'name', 'email', 'rating', 'comment', 'status'];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
