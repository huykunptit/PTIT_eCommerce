<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    //Management Categories
    public function categories()
    {
        $categories = Category::paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.category.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destination = public_path('/uploads/img/category');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $imagePath = '/uploads/img/category/' . $filename;
        }

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'parent_category_id' => $validated['parent_category_id'] ?? null,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? $category->description,
            'parent_category_id' => $validated['parent_category_id'] ?? $category->parent_category_id,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = now()->format('YmdHis') . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destination = public_path('/uploads/img/category');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $data['image'] = '/uploads/img/category/' . $filename;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
