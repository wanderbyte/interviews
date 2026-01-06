@extends('layouts.master')

@push('title')
    <title>Standard Management | Masters</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Masters → Standards</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#standardModal">
                <i class="fas fa-plus"></i> Add Standard
            </button>
        </div>

        <!-- Standards Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Standard Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($standards as $key => $standard)
                                <tr id="row-{{ $standard->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="standard-name">{{ $standard->standard_name }}</td>
                                    <td>{{ Str::limit($standard->standard_description, 50) }}</td>
                                    <td>{{ $standard->created_at->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-info editBtn" data-id="{{ $standard->id }}"
                                            data-name="{{ $standard->standard_name }}"
                                            data-description="{{ $standard->standard_description }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-danger delete-standard" data-id="{{ $standard->id }}"
                                            data-name="{{ $standard->standard_name }}">
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

    <!-- Standard Modal -->
    <div class="modal fade" id="standardModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="standardForm">
                @csrf
                <input type="hidden" id="standard_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Standard</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Standard Name <span class="text-danger">*</span></label>
                            <input type="text" id="standard_name" class="form-control">
                            <span class="text-danger error-name"></span>
                        </div>

                        <div class="form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea id="standard_description" class="form-control" rows="3"></textarea>
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
                    <h5 id="deleteStandardText"></h5>
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

            $('#standardModal').on('hidden.bs.modal', function() {
                $('#standardForm')[0].reset();
                $('#standard_id').val('');
                $('.error-name').text('');
                $('.modal-title').text('Add Standard');
            });

            $(document).on('click', '.editBtn', function() {
                $('#standard_id').val($(this).data('id'));
                $('#standard_name').val($(this).data('name'));
                $('#standard_description').val($(this).data('description'));

                $('.modal-title').text('Edit Standard');
                $('#standardModal').modal('show');
            });

            $('#standardForm').on('submit', function(e) {
                e.preventDefault();

                $('.error-name').text('');

                $.ajax({
                    url: "{{ route('masters.standards.save') }}",
                    type: "POST",
                    data: {
                        id: $('#standard_id').val(),
                        standard_name: $('#standard_name').val(),
                        standard_description: $('#standard_description').val()
                    },
                    success: function(res) {
                        $('#standardModal').modal('hide');
                        toastr.success(res.message);

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('.error-name').text(xhr.responseJSON.errors.standard_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });

            let deleteStandardId = null;

            $(document).on('click', '.delete-standard', function() {
                deleteStandardId = $(this).data('id');

                $('#deleteStandardText').html(
                    `Are you sure you want to delete <strong>${$(this).data('name')}</strong>?`
                );

                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                if (!deleteStandardId) return;

                $.ajax({
                    url: "{{ route('masters.standards.destroy', ':id') }}"
                        .replace(':id', deleteStandardId),
                    type: "DELETE",
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);

                        $('#row-' + deleteStandardId).fadeOut(500, function() {
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
