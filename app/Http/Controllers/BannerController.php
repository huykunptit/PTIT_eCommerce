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
            'photo' => 'nullable|string|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $slug = str()->slug($validated['title']);
        $validated['slug'] = $slug.'-'.uniqid();

        Banner::create($validated);

        return redirect()->route('banner.index')->with('success', 'Banner created successfully!');
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
            'photo' => 'nullable|string|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        if (!isset($banner->slug) || empty($banner->slug)) {
            $validated['slug'] = str()->slug($validated['title']).'-'.uniqid();
        }

        $banner->update($validated);

        return redirect()->route('banner.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('banner.index')->with('success', 'Banner deleted successfully!');
    }
}


