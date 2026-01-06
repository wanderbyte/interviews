@extends('layouts.master')

@push('title')
    <title>{{ isset($school) ? 'Edit School' : 'Add School' }}</title>
@endpush

@section('main-content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ isset($school) ? 'Edit School' : 'Add School' }}</h5>
            </div>

            <div class="card-body">
                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('schools.save') }}" enctype="multipart/form-data" autocomplete="off"
                    id="schoolForm">
                    @csrf
                    <input type="hidden" name="id" value="{{ $school->id ?? '' }}">

                    <h6 class="text-primary mb-3">School Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>School Name <span class="text-danger">*</span></label>
                            <input type="text" name="school_name" class="form-control" value="{{ old('school_name', $school->school_name ?? '') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Contact Number <span class="text-danger">*</span></label>
                            <input type="number" name="contact_number" class="form-control" value="{{ old('contact_number', $school->contact_number ?? '') }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>School Address <span class="text-danger">*</span></label>
                            <textarea name="school_address" class="form-control" rows="2" required>{{ old('school_address', $school->school_address ?? '') }}</textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>State <span class="text-danger">*</span></label>
                            <select name="state_id" id="state" class="form-control" required>
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" @selected(old('state_id', $school->state_id ?? '') == $state->id)>
                                        {{ $state->state_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>District <span class="text-danger">*</span></label>
                            <select name="district_id" id="district" class="form-control" required>
                                <option value="">Select District</option>
                                @isset($districts)
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" @selected(old('district_id', $school->district_id ?? '') == $district->id)>
                                            {{ $district->district_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>City / Village <span class="text-danger">*</span></label>
                            <select name="city_id" id="city" class="form-control" required>
                                <option value="">Select City</option>
                                @isset($cities)
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @selected(old('city_id', $school->city_id ?? '') == $city->id)>
                                            {{ $city->city_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Establishment Date <span class="text-danger">*</span></label>
                            <input type="date" name="establishment_date" class="form-control" value="{{ old('establishment_date', $school->establishment_date ?? '') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Login ID <span class="text-danger">*</span></label>
                            <input type="text" name="login_id" class="form-control" value="{{ old('login_id', $school->login_id ?? '') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Password {{ isset($school) ? '' : '*' }}</label>
                            <input type="password" name="password" class="form-control" {{ isset($school) ? '' : 'required' }}>
                            @isset($school)
                                <small class="text-muted">Leave blank to keep existing password</small>
                            @endisset
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>School Photos</label>
                            <input type="file" name="school_photos[]" class="form-control" multiple>
                            <small class="text-muted">Multiple files allowed</small>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-primary mb-0">
                            <i class="fas fa-users"></i> Students
                        </h6>

                        <button type="button" class="btn btn-sm btn-success" id="addStudent">
                            <i class="fas fa-plus"></i> Add Student
                        </button>
                    </div>

                    <div id="studentsWrapper">

                        @php
                            $students = old('students', isset($school) ? $school->students->toArray() : [[]]);
                        @endphp

                        @foreach ($students as $index => $student)
                            <div class="card mb-3 student-row shadow-sm" data-index="{{ $index }}">
                                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                    <strong>
                                        <i class="fas fa-user-graduate text-primary"></i>
                                        Student {{ $index + 1 }}
                                    </strong>

                                    <button type="button" class="btn btn-sm btn-outline-danger removeStudent">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <div class="card-body">
                                    @if (!empty($student['id']))
                                        <input type="hidden" name="students[{{ $index }}][id]" value="{{ $student['id'] }}">
                                    @endif

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label>Student Name *</label>
                                            <input type="text" name="students[{{ $index }}][student_name]" class="form-control" value="{{ $student['student_name'] ?? '' }}" required>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label>Standard *</label>
                                            <select name="students[{{ $index }}][standard_id]" class="form-control" required>
                                                <option value="">Select</option>
                                                @foreach ($standards as $standard)
                                                    <option value="{{ $standard->id }}" @selected(($student['standard_id'] ?? '') == $standard->id)>
                                                        {{ $standard->standard_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label>Gender *</label>
                                            <div class="d-flex gap-3">
                                                @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $key => $label)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="students[{{ $index }}][gender]" value="{{ $key }}" @checked(($student['gender'] ?? '') == $key) required>
                                                        <label class="form-check-label">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-2 mb-3">
                                            <label>Year *</label>
                                            <input type="number" name="students[{{ $index }}][year]" class="form-control" value="{{ $student['year'] ?? '' }}" min="1900" max="{{ date('Y') }}" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label>Photo</label>
                                            <input type="file" name="students[{{ $index }}][photo]" class="form-control student-photo" accept="image/*">

                                            <div class="student-photo-preview mt-2">
                                                @if (!empty($student['photo']))
                                                    <img src="{{ asset('storage/' . $student['photo']) }}" class="img-thumbnail" style="max-width:90px">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <a href="{{ route('schools.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .student-row {
            border-left: 4px solid #4e73df;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let studentIndex = {{ count($students) }};

        $('#addStudent').on('click', function() {
            $('#studentsWrapper').append(`
                <div class="card mb-3 student-row shadow-sm">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <strong><i class="fas fa-user-graduate text-primary"></i> Student ${studentIndex + 1}</strong>
                        <button type="button" class="btn btn-sm btn-outline-danger removeStudent">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Student Name *</label>
                                <input type="text" name="students[${studentIndex}][student_name]" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Standard *</label>
                                <select name="students[${studentIndex}][standard_id]" class="form-control" required>
                                    <option value="">Select</option>
                                    @foreach ($standards as $standard)
                                        <option value="{{ $standard->id }}">{{ $standard->standard_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label>Gender *</label>
                                <div class="d-flex gap-3">
                                    @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $key => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                name="students[${studentIndex}][gender]" value="{{ $key }}" required>
                                            <label class="form-check-label">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Year *</label>
                                <input type="number" name="students[${studentIndex}][year]" class="form-control" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>Photo</label>
                                <input type="file" name="students[${studentIndex}][photo]" class="form-control student-photo" accept="image/*">

                                <div class="student-photo-preview mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            studentIndex++;
        });

        $(document).on('click', '.removeStudent', function() {
            $(this).closest('.student-row').slideUp(200, function() {
                $(this).remove();
            });
        });

        $(document).on('change', '.student-photo', function() {
            const previewDiv = $(this).closest('.col-md-2').find('.student-photo-preview');
            previewDiv.html('');

            const file = this.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file (JPG, PNG, GIF)');
                $(this).val('');
                return;
            }

            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                alert('Image size should be less than 2MB');
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewDiv.html(`
                <img src="${e.target.result}" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                <small class="d-block text-muted mt-1">Preview</small>
            `);
            };
            reader.readAsDataURL(file);
        });

        $('#schoolForm').on('submit', function() {
            // Add index to all new student inputs
            $('.student-row').each(function(index) {
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name && name.includes('students[')) {
                        $(this).attr('name', name.replace(/students\[\d+\]/, `students[${index}]`));
                    }
                });
            });
        });

        $('#state').on('change', function() {
            let stateId = $(this).val();
            $('#district').html('<option value="">Loading...</option>');
            $('#city').html('<option value="">Select City</option>');

            if (stateId) {
                $.get("{{ route('masters.get.districts', ':id') }}".replace(':id', stateId), function(data) {
                    let options = '<option value="">Select District</option>';
                    data.forEach(d => {
                        options += `<option value="${d.id}">${d.district_name}</option>`;
                    });
                    $('#district').html(options);
                }).fail(function() {
                    $('#district').html('<option value="">Error loading districts</option>');
                });
            }
        });

        $('#district').on('change', function() {
            let districtId = $(this).val();
            $('#city').html('<option value="">Loading...</option>');

            if (districtId) {
                $.get("{{ route('masters.get.cities', ':id') }}".replace(':id', districtId), function(data) {
                    let options = '<option value="">Select City</option>';
                    data.forEach(c => {
                        options += `<option value="${c.id}">${c.city_name}</option>`;
                    });
                    $('#city').html(options);
                }).fail(function() {
                    $('#city').html('<option value="">Error loading cities</option>');
                });
            }
        });
    </script>
@endpush
