<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'quote',
        'summary',
        'description',
        'post_cat_id',
        'tags',
        'added_by',
        'photo',
        'status',
    ];
}


