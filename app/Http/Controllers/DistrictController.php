<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\State;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display listing of districts
     */
    public function index()
    {
        $states = State::get();
        $districts = District::with('state')->get();

        return view('masters.districts', compact('states', 'districts'));
    }

    /**
     * Store or update district
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'nullable|exists:districts,id',
            'state_id'      => 'required|exists:states,id',
            'district_name' => 'required|string|max:255',
        ]);

        if (!empty($validated['id'])) {

            $district = District::find($validated['id']);
            $district->state_id = $validated['state_id'];
            $district->district_name = $validated['district_name'];
            $district->save();

            $message = 'District updated successfully';
        } else {

            $district = District::create([
                'state_id'      => $validated['state_id'],
                'district_name' => $validated['district_name'],
            ]);

            $message = 'District created successfully';
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $district
        ]);
    }

    /**
     * Delete district
     */
    public function destroy(District $district)
    {
        $district->delete();

        return response()->json([
            'status'  => true,
            'message' => 'District deleted successfully'
        ]);
    }
}
