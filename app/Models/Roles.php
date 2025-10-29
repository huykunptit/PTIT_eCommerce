<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $fillable = [
        'role_name',
        'role_code'
    ];

    public function user()
    {
        return $this->hasMany(Product::class, 'role_id');
    }

}
