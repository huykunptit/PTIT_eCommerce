<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->paginate(10);
        return view('admin.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096|required_without:image_url',
            'image_url' => 'nullable|url|required_without:image',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = str()->slug($validated['title']);
        $validated['slug'] = $slug.'-'.uniqid();

        // Handle image upload or URL
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destination = public_path('/uploads/img/banner');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $imagePath = '/uploads/img/banner/' . $filename;
        } elseif (!empty($validated['image_url'])) {
            $imagePath = $validated['image_url'];
        }

        Banner::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'slug' => $validated['slug'],
            'photo' => $imagePath,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.banner.index')->with('success', 'Banner created successfully!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'image_url' => 'nullable|url',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? $banner->description,
            'status' => $validated['status'],
        ];

        if (!isset($banner->slug) || empty($banner->slug)) {
            $data['slug'] = str()->slug($validated['title']).'-'.uniqid();
        }

        // Handle image upload or URL
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destination = public_path('/uploads/img/banner');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $data['photo'] = '/uploads/img/banner/' . $filename;

            if ($banner->photo && str_starts_with($banner->photo, '/uploads/') && file_exists(public_path($banner->photo))) {
                @unlink(public_path($banner->photo));
            }
        } elseif (!empty($validated['image_url'])) {
            $data['photo'] = $validated['image_url'];
        }

        $banner->update($data);

        return redirect()->route('admin.banner.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banner.index')->with('success', 'Banner deleted successfully!');
    }
}


