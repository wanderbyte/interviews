@extends('layouts.master')

@push('title')
    <title>District Management | Masters</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Masters → Districts</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#districtModal">
                <i class="fas fa-plus"></i> Add District
            </button>
        </div>

        <!-- District Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>State</th>
                                <th>District Name</th>
                                <th>Created At</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($districts as $key => $district)
                                <tr id="row-{{ $district->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $district->state->state_name }}</td>
                                    <td>{{ $district->district_name }}</td>
                                    <td>{{ $district->created_at->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-info editBtn" data-id="{{ $district->id }}"
                                            data-name="{{ $district->district_name }}"
                                            data-state="{{ $district->state_id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-danger delete-district" data-id="{{ $district->id }}"
                                            data-name="{{ $district->district_name }}">
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

    <!-- District Modal -->
    <div class="modal fade" id="districtModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="districtForm">
                @csrf
                <input type="hidden" id="district_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add District</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>State <span class="text-danger">*</span></label>
                            <select id="state_id" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>District Name <span class="text-danger">*</span></label>
                            <input type="text" id="district_name" class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
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
                    <h5 id="deleteDistrictText"></h5>
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

            $('#districtModal').on('hidden.bs.modal', function() {
                $('#districtForm')[0].reset();
                $('#district_id').val('');
                $('.error-name').text('');
                $('.modal-title').text('Add District');
            });

            $(document).on('click', '.editBtn', function() {
                $('#district_id').val($(this).data('id'));
                $('#district_name').val($(this).data('name'));
                $('#state_id').val($(this).data('state'));

                $('.modal-title').text('Edit District');
                $('#districtModal').modal('show');
            });

            $('#districtForm').on('submit', function(e) {
                e.preventDefault();

                $('.error-name').text('');

                $.ajax({
                    url: "{{ route('masters.districts.save') }}",
                    type: "POST",
                    data: {
                        id: $('#district_id').val(),
                        state_id: $('#state_id').val(),
                        district_name: $('#district_name').val()
                    },
                    success: function(res) {
                        $('#districtModal').modal('hide');
                        toastr.success(res.message);

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('.error-name').text(xhr.responseJSON.errors.district_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });

            let deleteDistrictId = null;

            $(document).on('click', '.delete-district', function() {
                deleteDistrictId = $(this).data('id');
                $('#deleteDistrictText').html(
                    `Are you sure you want to delete <strong>${$(this).data('name')}</strong>?`
                );
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                $.ajax({
                    url: "{{ route('masters.districts.destroy', ':id') }}".replace(':id', deleteDistrictId),
                    type: "DELETE",
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);

                        $('#row-' + deleteDistrictId).fadeOut(500, function() {
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
