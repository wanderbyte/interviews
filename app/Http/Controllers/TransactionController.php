<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function materials()
    {

        $categories = Category::get();
        $materials = Material::with('category')
            ->latest()->get();

        return view('transactions.materials', compact('materials', 'categories'));
    }

    public function update(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'id'              => 'nullable|exists:materials,id',
            'category_id'     => 'required|exists:categories,id',
            'material_name'   => 'required|string'
        ]);

        if (!empty($validated['id'])) {

            $material = Material::findOrFail($validated['id']);

            $material->category_id   = $validated['category_id'];
            $material->material_name = $validated['material_name'];

            $material->save();
        } else {

            $material = Material::create([
                'category_id'       => $validated['category_id'],
                'material_name'     => $validated['material_name']
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

    public function index()
    {

        $categories = Category::orderBy('category_name')->get();

        $transactions = MaterialTransaction::with('material.category')
            ->latest()->get();

        return view('transactions.index', compact('categories', 'transactions'));
    }
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'               => 'nullable|exists:material_transactions,id',
            'material_id'      => 'required|exists:materials,id',
            'transaction_date' => 'required|date',
            'quantity'         => ['required', 'numeric', 'regex:/^-?\d+(\.\d{1,2})?$/']
        ]);

        DB::beginTransaction();

        try {
            $material = Material::lockForUpdate()->findOrFail($validated['material_id']);
            $newQty = $validated['quantity'];

            if (!empty($validated['id'])) {

                $transaction = MaterialTransaction::findOrFail($validated['id']);

                // Reverse old quantity
                $material->current_balance -= $transaction->quantity;

                // Check stock after reversal
                if ($material->current_balance + $newQty < 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient stock available'
                    ], 422);
                }

                // Apply new quantity
                $material->current_balance += $newQty;

                $transaction->update([
                    'material_id'      => $validated['material_id'],
                    'transaction_date' => $validated['transaction_date'],
                    'quantity'         => $newQty,
                ]);
            } else {

                // OUTWARD check
                if ($material->current_balance + $newQty < 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient stock available'
                    ], 422);
                }

                $material->current_balance += $newQty;

                $transaction = MaterialTransaction::create([
                    'material_id'      => $validated['material_id'],
                    'transaction_date' => $validated['transaction_date'],
                    'quantity'         => $newQty,
                ]);
            }

            $material->save();
            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => !empty($validated['id'])
                    ? 'Inward / Outward entry updated successfully'
                    : 'Inward / Outward entry created successfully',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $transaction = MaterialTransaction::find($id);

        if (!$transaction) {
            return response()->json([
                'status'  => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $material = Material::lockForUpdate()->findOrFail($transaction->material_id);

            // Reverse transaction effect on stock
            $material->current_balance -= $transaction->quantity;

            // Safety check (optional but recommended)
            if ($material->current_balance < 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot delete transaction. Stock would become negative.'
                ], 422);
            }

            $material->save();
            $transaction->delete();

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Inward / Outward entry deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete entry'
            ], 500);
        }
    }
}
