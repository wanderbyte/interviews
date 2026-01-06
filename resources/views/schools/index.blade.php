@extends('layouts.master')

@push('title')
    <title>School Management</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Schools</h1>

            <a href="{{ route('schools.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add School
            </a>
        </div>

        <!-- School Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>School Name</th>
                                <th>State</th>
                                <th>District</th>
                                <th>City</th>
                                <th>Contact Number</th>
                                <th>Established On</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schools as $key => $school)
                                <tr id="row-{{ $school->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $school->school_name }}</td>
                                    <td>{{ $school->state->state_name }}</td>
                                    <td>{{ $school->district->district_name }}</td>
                                    <td>{{ $school->city->city_name }}</td>
                                    <td>{{ $school->contact_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($school->establishment_date)->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('schools.edit', $school->id) }}" class="btn btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="{{ route('schools.pdf', $school->id) }}" class="btn btn-secondary">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        <button class="btn btn-danger delete-school" data-id="{{ $school->id }}"
                                            data-name="{{ $school->school_name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5 id="deleteSchoolText"></h5>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let deleteSchoolId = null;

            $(document).on('click', '.delete-school', function() {
                deleteSchoolId = $(this).data('id');
                $('#deleteSchoolText').html(
                    `Are you sure you want to delete <strong>${$(this).data('name')}</strong>?`
                );
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                $.ajax({
                    url: "{{ route('schools.destroy', ':id') }}".replace(':id', deleteSchoolId),
                    type: "DELETE",
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);

                        $('#row-' + deleteSchoolId).fadeOut(500, function() {
                            $(this).remove();
                        });
                    },
                    error: function() {
                        toastr.error('Delete failed!');
                    }
                });
            });

        });
    </script>
@endpush
