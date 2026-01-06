<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use App\Models\District;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $states = State::get();
        $cities = City::with('district.state')->get();

        return view('masters.cities', compact('states', 'cities'));
    }

    public function getDistricts($stateId)
    {
        return District::where('state_id', $stateId)->get();
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'          => 'nullable|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'city_name'   => 'required|string|max:255',
        ]);

        if (!empty($validated['id'])) {
            $city = City::find($validated['id']);
            $city->update($validated);
            $message = 'City updated successfully';
        } else {
            $city = City::create($validated);
            $message = 'City created successfully';
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $city
        ]);
    }

    public function destroy(City $city)
    {
        $city->delete();

        return response()->json([
            'status'  => true,
            'message' => 'City deleted successfully'
        ]);
    }
}
