<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MaterialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index()
    {

        $categories = Category::orderBy('category_name')->get();

        $transactions = MaterialTransaction::with('material.category')
            ->latest()->get();

        return view('transactions.index', compact('categories', 'transactions'));
    }

    public function save(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'id'               => 'nullable|exists:material_transactions,id',
            'material_id'      => 'required|exists:materials,id',
            'transaction_date' => 'required|date',
            'quantity'         => ['required', 'numeric', 'regex:/^-?\d+(\.\d{1,2})?$/']
        ]);

        // UPDATE (if required later)
        if (!empty($validated['id'])) {
            $transaction = MaterialTransaction::find($validated['id']);
            $transaction->material_id      = $validated['material_id'];
            $transaction->transaction_date = $validated['transaction_date'];
            $transaction->quantity         = $validated['quantity'];
            $transaction->save();
        }
        // CREATE
        else {
            $transaction = MaterialTransaction::create([
                'material_id'      => $validated['material_id'],
                'transaction_date' => $validated['transaction_date'],
                'quantity'         => $validated['quantity']
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => isset($validated['id'])
                ? 'Inward / Outward entry updated successfully'
                : 'Inward / Outward entry created successfully',
            'data'    => $transaction
        ]);
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

        $transaction->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Inward / Outward entry deleted successfully'
        ]);
    }
}
