<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        $states = State::get();
        return view('masters.states', compact('states'));
    }

    public function save(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'id' => 'nullable|exists:states,id',
            'state_name' => 'required|string',
            'state_code' => 'nullable|string|max:10'
        ]);

        if (!empty($validated['id'])) {
            $state = State::find($validated['id']);
            $state->state_name = $validated['state_name'];
            $state->state_code = $validated['state_code'] ?? null;
            $state->save();
        } else {
            $state = State::create([
                'country_id' => 105,
                'state_name' => $validated['state_name'],
                'state_code' => $validated['state_code'] ?? null,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => isset($validated['id'])
                ? 'State updated successfully'
                : 'State created successfully',
            'data'    => $state
        ]);
    }

    public function destroy(State $state)
    {
        $state->delete();

        return response()->json([
            'status'  => true,
            'message' => 'State deleted successfully'
        ]);
    }
}
