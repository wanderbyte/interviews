<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{

    public function index()
    {
        $categories = Category::get();
        $materials = Material::with('category')->get();

        return view('materials.index', compact('materials', 'categories'));
    }

    public function save(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'id'              => 'nullable|exists:materials,id',
            'category_id'     => 'required|exists:categories,id',
            'material_name'   => 'required|string',
            'opening_balance' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/']
        ]);

        // UPDATE
        if (!empty($validated['id'])) {
            $material = Material::find($validated['id']);
            $material->category_id     = $validated['category_id'];
            $material->material_name   = $validated['material_name'];
            $material->opening_balance = $validated['opening_balance'];
            $material->save();
        }
        // CREATE
        else {
            $material = Material::create([
                'category_id'     => $validated['category_id'],
                'material_name'   => $validated['material_name'],
                'opening_balance' => $validated['opening_balance']
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => isset($validated['id'])
                ? 'Material updated successfully'
                : 'Material created successfully',
            'data'    => $material
        ]);
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return response()->json([
            'status' => true,
            'message' => 'Material deleted successfully'
        ]);
    }
}
