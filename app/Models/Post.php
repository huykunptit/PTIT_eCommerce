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

    /**
     * Relationship với Category
     */
    public function cat_info()
    {
        return $this->belongsTo(Category::class, 'post_cat_id');
    }

    /**
     * Relationship với User (author)
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Active top-level comments for this post
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->where('status', 'active')->whereNull('parent_id');
    }

    /**
     * All comments for this post (including inactive/children)
     */
    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }
}


