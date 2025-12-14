<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::get();
        return view('categories.index', compact('categories'));
    }

    public function save(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'name' => 'required|string',
            'id'   => 'nullable|exists:categories,id'
        ]);

        if (!empty($validated['id'])) {
            $category = Category::find($validated['id']);
            $category->category_name = $validated['name'];
            $category->save();
        } else {
            $category = Category::create([
                'category_name' => $validated['name']
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => isset($validated['id'])
                ? 'Category updated successfully'
                : 'Category created successfully',
            'data'    => $category
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
