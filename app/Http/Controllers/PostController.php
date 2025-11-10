<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('admin.post.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
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
            'post_cat_id' => 'nullable|integer|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'added_by' => 'required|exists:users,id',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $validated;
        $data['tags'] = isset($validated['tags']) ? implode(',', $validated['tags']) : null;

        // Handle file upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'uploads/img/post';
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }
            $file->move(public_path($path), $filename);
            $data['photo'] = $path . '/' . $filename;
        } else {
            // Remove photo from data if no file uploaded
            unset($data['photo']);
        }

        Post::create($data);

        return redirect()->route('post.index')->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
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
            'post_cat_id' => 'nullable|integer|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'added_by' => 'required|exists:users,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $validated;
        $data['tags'] = isset($validated['tags']) ? implode(',', $validated['tags']) : null;

        // Handle file upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($post->photo && file_exists(public_path($post->photo))) {
                unlink(public_path($post->photo));
            }
            
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'uploads/img/post';
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }
            $file->move(public_path($path), $filename);
            $data['photo'] = $path . '/' . $filename;
        } else {
            // Keep existing photo if no new file uploaded
            unset($data['photo']);
        }

        $post->update($data);

        return redirect()->route('post.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('post.index')->with('success', 'Post deleted successfully!');
    }
}


