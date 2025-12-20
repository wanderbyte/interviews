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

        if (!empty($validated['id'])) {

            $material = Material::findOrFail($validated['id']);

            $oldOpening   = $material->opening_balance;
            $oldAvailable = $material->current_balance;
            $newOpening   = $validated['opening_balance'];

            $usedQuantity = $oldOpening - $oldAvailable;

            // Prevent reducing below used quantity
            if ($newOpening < $usedQuantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Opening balance cannot be less than used quantity'
                ], 422);
            }

            // Case 1: No stock used → RESET available
            if ($oldOpening == $oldAvailable) {
                $material->current_balance = $newOpening;
            }

            // Case 2: Stock used → adjust only increment
            else {
                $difference = $newOpening - $oldOpening;

                if ($difference > 0) {
                    $material->current_balance += $difference;
                }
            }

            $material->category_id     = $validated['category_id'];
            $material->material_name   = $validated['material_name'];
            $material->opening_balance = $newOpening;

            $material->save();
        } else {

            $material = Material::create([
                'category_id'       => $validated['category_id'],
                'material_name'     => $validated['material_name'],
                'opening_balance'   => $validated['opening_balance'],
                'current_balance' => $validated['opening_balance'],
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => !empty($validated['id'])
                ? 'Material updated successfully'
                : 'Material created successfully',
            'data'    => $material
        ]);
    }

    public function destroy(Material $material)
    {
        if ($material->transactions()->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete material. Inward/Outward entries exist.'
            ], 422);
        }

        $material->delete();

        return response()->json([
            'status' => true,
            'message' => 'Material deleted successfully'
        ]);
    }

    public function delete(Material $material)
    {
        $material->delete(); // soft delete

        return response()->json([
            'status'  => true,
            'message' => 'Material deleted successfully'
        ]);
    }

    public function getByCategory($categoryId)
    {
        $materials = Material::where('category_id', $categoryId)
            ->select('id', 'material_name')
            ->orderBy('material_name')
            ->get();

        return response()->json($materials);
    }
}
