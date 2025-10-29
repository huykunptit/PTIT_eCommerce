<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('admin.post.index', compact('posts'));
    }

    public function create()
    {
        $categories = []; // Adjust if you have post categories table
        $tags = [];
        $users = User::select('id','name')->get();
        return view('admin.post.create', compact('categories','tags','users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'quote' => 'nullable|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'post_cat_id' => 'nullable|integer',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'added_by' => 'required|exists:users,id',
            'photo' => 'nullable|string|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $validated;
        $data['tags'] = isset($validated['tags']) ? implode(',', $validated['tags']) : null;

        Post::create($data);

        return redirect()->route('post.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = [];
        $tags = [];
        $users = User::select('id','name')->get();
        return view('admin.post.edit', compact('post','categories','tags','users'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'quote' => 'nullable|string',
            'summary' => 'required|string',
            'description' => 'nullable|string',
            'post_cat_id' => 'nullable|integer',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'added_by' => 'required|exists:users,id',
            'photo' => 'nullable|string|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $validated;
        $data['tags'] = isset($validated['tags']) ? implode(',', $validated['tags']) : null;

        $post->update($data);

        return redirect()->route('post.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('post.index')->with('success', 'Post deleted successfully!');
    }
}


