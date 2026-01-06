<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\School;
use App\Models\Standard;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{

    public function index()
    {
        $schools = School::with(['state', 'district', 'city'])
            ->latest()->get();

        return view('schools.index', compact('schools'));
    }

    public function create()
    {
        return view('schools.create', [
            'states'    => State::all(),
            'standards' => Standard::all(),
        ]);
    }

    public function edit(School $school)
    {
        $school->load(['students', 'photos']);

        return view('schools.create', [
            'school'    => $school,
            'states'    => State::all(),
            'districts' => District::where('state_id', $school->state_id)->get(),
            'cities'    => City::where('district_id', $school->district_id)->get(),
            'standards' => Standard::all(),
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'                 => 'nullable|exists:schools,id',
            'school_name'        => 'required|string|max:255',
            'school_address'     => 'required|string',
            'state_id'           => 'required|exists:states,id',
            'district_id'        => 'required|exists:districts,id',
            'city_id'            => 'required|exists:cities,id',
            'establishment_date' => 'required|date',
            'contact_number'     => 'required|digits_between:10,15',
            'login_id'           => 'required|string|unique:schools,login_id,' . $request->id,

            // Password
            'password' => $request->id ? 'nullable|min:8' : 'required|min:8',

            // School photos
            'school_photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Students
            'students'                 => 'nullable|array',
            'students.*.id'            => 'nullable|exists:students,id',
            'students.*.student_name'  => 'required|string|max:255',
            'students.*.standard_id'   => 'required|exists:standards,id',
            'students.*.gender'        => 'required|in:male,female,other',
            'students.*.year'          => 'required|integer',
            'students.*.photo'         => 'nullable|image|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request, &$school) {

            if (!empty($validated['id'])) {

                $school = School::findOrFail($validated['id']);

                $school->update([
                    'school_name'        => $validated['school_name'],
                    'school_address'     => $validated['school_address'],
                    'state_id'           => $validated['state_id'],
                    'district_id'        => $validated['district_id'],
                    'city_id'            => $validated['city_id'],
                    'establishment_date' => $validated['establishment_date'],
                    'contact_number'     => $validated['contact_number'],
                    'login_id'           => $validated['login_id'],
                ]);

                if (!empty($validated['password'])) {
                    $school->update([
                        'password' => Hash::make($validated['password']),
                    ]);
                }
            } else {

                $school = School::create([
                    'school_name'        => $validated['school_name'],
                    'school_address'     => $validated['school_address'],
                    'state_id'           => $validated['state_id'],
                    'district_id'        => $validated['district_id'],
                    'city_id'            => $validated['city_id'],
                    'establishment_date' => $validated['establishment_date'],
                    'contact_number'     => $validated['contact_number'],
                    'login_id'           => $validated['login_id'],
                    'password'           => Hash::make($validated['password']),
                ]);
            }

            if ($request->hasFile('school_photos')) {
                foreach ($request->file('school_photos') as $photo) {
                    $path = $photo->store('schools', 'public');
                    $school->photos()->create([
                        'photo_path' => $path,
                    ]);
                }
            }

            if (!empty($validated['students'])) {

                $handledStudentIds = [];

                foreach ($validated['students'] as $student) {

                    if (
                        isset($student['photo'])
                        && $student['photo'] instanceof \Illuminate\Http\UploadedFile
                    ) {
                        $student['photo'] = $student['photo']->store('students', 'public');
                    } else {
                        unset($student['photo']); // keep existing photo
                    }

                    if (!empty($student['id'])) {

                        $school->students()
                            ->where('id', $student['id'])
                            ->update($student);

                        $handledStudentIds[] = $student['id'];
                    } else {

                        $newStudent = $school->students()->create($student);
                        $handledStudentIds[] = $newStudent->id;
                    }
                }

                $school->students()
                    ->whereNotIn('id', $handledStudentIds)
                    ->delete();
            }
        });

        return redirect()
            ->route('schools.index')
            ->with('success', 'School saved successfully');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return response()->json([
            'message' => 'School deleted successfully'
        ]);
    }

    public function exportPdf(School $school)
    {
        $school->load([
            'state',
            'district',
            'city',
            'students.standard',
            'photos'
        ]);

        $data = [
            'school_name'        => $school->school_name,
            'school_address'     => $school->school_address,
            'establishment_date' => $school->establishment_date,
            'contact_number'     => $school->contact_number,

            'state'    => $school->state->state_name ?? null,
            'district' => $school->district->district_name ?? null,
            'city'     => $school->city->city_name ?? null,

            'photos' => $school->photos->map(function ($photo) {
                $path = storage_path('app/public/' . $photo->photo_path);
                return file_exists($path) ? $path : null;
            })->filter(),

            'students' => $school->students->map(function ($student) {
                return [
                    'student_name' => $student->student_name,
                    'gender'       => ucfirst($student->gender),
                    'year'         => $student->year,
                    'standard'     => $student->standard->standard_name ?? null,

                    'photo' => $student->photo
                        ? storage_path('app/public/' . $student->photo)
                        : null,
                ];
            }),
        ];

        $pdf = Pdf::loadView('schools.pdf', compact('data'))
            ->setPaper('A4', 'portrait')
            ->setOption('enable-local-file-access', true);

        return $pdf->download('school_' . $school->id . '.pdf');
    }
}
