<?php

namespace App\Http\Controllers;

use App\Models\Standard;
use Illuminate\Http\Request;

class StandardController extends Controller
{
    public function index()
    {
        $standards = Standard::all();
        return view('masters.standards', compact('standards'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:standards,id',
            'standard_name' => 'required|string|max:100',
            'standard_description' => 'required|string',
        ]);

        if (!empty($validated['id'])) {
            // Update
            $standard = Standard::findOrFail($validated['id']);
            $standard->update([
                'standard_name' => $validated['standard_name'],
                'standard_description' => $validated['standard_description'],
            ]);

            $message = 'Standard updated successfully';
        } else {
            // Create
            $standard = Standard::create([
                'standard_name' => $validated['standard_name'],
                'standard_description' => $validated['standard_description'],
            ]);

            $message = 'Standard created successfully';
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $standard
        ]);
    }

    public function destroy(Standard $standard)
    {
        $standard->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Standard deleted successfully'
        ]);
    }
}
