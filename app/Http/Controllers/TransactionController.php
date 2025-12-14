<?php

namespace App\Http\Controllers;

use App\Models\MaterialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_id'      => 'required|exists:materials,id',
            'transaction_date' => 'required|date',
            'quantity'         => 'required|numeric|regex:/^-?\d+(\.\d{1,2})?$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction = MaterialTransaction::create(
            $request->only('material_id', 'transaction_date', 'quantity')
        );

        return response()->json([
            'status' => true,
            'message' => 'Transaction saved successfully',
            'data' => $transaction
        ]);
    }
}
