<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        $materials = Material::with('category')->get();

        return view('materials.index', compact('materials', 'categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'     => 'required|exists:categories,id',
            'name'            => 'required|alpha_num',
            'opening_balance' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $material = Material::create($request->only(
            'category_id',
            'name',
            'opening_balance'
        ));

        return response()->json([
            'status' => true,
            'message' => 'Material created successfully',
            'data' => $material
        ]);
    }

    public function update(Request $request, Material $material)
    {
        $validator = Validator::make($request->all(), [
            'category_id'     => 'required|exists:categories,id',
            'name'            => 'required|alpha_num',
            'opening_balance' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $material->update($request->only(
            'category_id',
            'name',
            'opening_balance'
        ));

        return response()->json([
            'status' => true,
            'message' => 'Material updated successfully'
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
