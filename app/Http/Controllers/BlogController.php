<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::query()
            ->where('status', 'active')
            ->with(['author'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('frontend.pages.blog', compact('posts'));
    }

    public function show(int $id)
    {
        $post = Post::query()
            ->where('status', 'active')
            ->with(['author'])
            ->findOrFail($id);

        return view('frontend.pages.blog-detail', compact('post'));
    }
}
