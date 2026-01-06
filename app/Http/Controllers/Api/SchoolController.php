<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SchoolController extends Controller
{
    public function fetch(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $school = School::where('login_id', $request->login_id)->first();

        if (!$school || !Hash::check($request->password, $school->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid login credentials',
            ], 401);
        }

        $school->load([
            'state',
            'district',
            'city',
            'photos',
            'students.standard',
        ]);

        $response = [
            'school_name'        => $school->school_name,
            'school_address'     => $school->school_address,
            'establishment_date' => $school->establishment_date,
            'contact_number'     => $school->contact_number,

            'state'    => $school->state->state_name ?? null,
            'district' => $school->district->district_name ?? null,
            'city'     => $school->city->city_name ?? null,

            'photos' => $school->photos->pluck('photo_path'),

            'students' => $school->students->map(function ($student) {
                return [
                    'student_name' => $student->student_name,
                    'gender'       => $student->gender,
                    'year'         => $student->year,
                    'photo'        => $student->photo,
                    'standard'     => $student->standard->standard_name ?? null,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'data'   => $response,
        ]);
    }

    public function decryptPayload(Request $request)
    {
        $request->validate([
            'payload' => 'required|string',
        ]);

        try {
            $decrypted = Crypt::decryptString($request->payload);

            return response()->json([
                'status' => true,
                'data'   => json_decode($decrypted, true),
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or corrupted payload',
            ], 400);
        }
    }
}
