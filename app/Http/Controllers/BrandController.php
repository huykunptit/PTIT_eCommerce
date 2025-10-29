<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        $brands = Brand::orderByDesc('id')->paginate(10);
        return view('admin.brand.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brand.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $slugBase = Str::slug($validated['title']);
        $slug = $slugBase;
        $counter = 1;
        while (Brand::where('slug', $slug)->exists()) {
            $slug = $slugBase.'-'.$counter++;
        }

        Brand::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully!');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brand.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'title' => $validated['title'],
            'status' => $validated['status'],
        ];

        if ($brand->title !== $validated['title']) {
            $slugBase = Str::slug($validated['title']);
            $slug = $slugBase;
            $counter = 1;
            while (Brand::where('slug', $slug)->where('id', '!=', $brand->id)->exists()) {
                $slug = $slugBase.'-'.$counter++;
            }
            $data['slug'] = $slug;
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully!');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully!');
    }
}


