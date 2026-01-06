@extends('layouts.master')

@push('title')
    <title>State Management | Masters</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Masters → States</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#stateModal">
                <i class="fas fa-plus"></i> Add State
            </button>
        </div>

        <!-- State Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>State Name</th>
                                <th>State Code</th>
                                <th>Created At</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($states as $key => $state)
                                <tr id="row-{{ $state->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="state-name">{{ $state->state_name }}</td>
                                    <td>{{ $state->state_code }}</td>
                                    <td>{{ $state->created_at->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-info editBtn" data-id="{{ $state->id }}"
                                            data-name="{{ $state->state_name }}" data-code="{{ $state->state_code }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-danger delete-state" data-id="{{ $state->id }}"
                                            data-name="{{ $state->state_name }}">
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

    <!-- State Modal -->
    <div class="modal fade" id="stateModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="stateForm">
                @csrf
                <input type="hidden" id="state_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add State</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>State Name <span class="text-danger">*</span></label>
                            <input type="text" id="state_name" class="form-control">
                            <span class="text-danger error-name"></span>
                        </div>

                        <div class="form-group">
                            <label>State Code</label>
                            <input type="text" id="state_code" class="form-control">
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
                    <h5 id="deleteStateText"></h5>
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

            $('#stateModal').on('hidden.bs.modal', function() {
                $('#stateForm')[0].reset();
                $('#state_id').val('');
                $('.error-name').text('');
                $('.modal-title').text('Add State');
            });

            $(document).on('click', '.editBtn', function() {
                $('#state_id').val($(this).data('id'));
                $('#state_name').val($(this).data('name'));
                $('#state_code').val($(this).data('code'));

                $('.modal-title').text('Edit State');
                $('#stateModal').modal('show');
            });

            $('#stateForm').on('submit', function(e) {
                e.preventDefault();

                $('.error-name').text('');

                $.ajax({
                    url: "{{ route('masters.states.save') }}",
                    type: "POST",
                    data: {
                        id: $('#state_id').val(),
                        state_name: $('#state_name').val(),
                        state_code: $('#state_code').val()
                    },
                    success: function(res) {
                        $('#stateModal').modal('hide');
                        toastr.success(res.message);

                        // Optional: reload only table
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('.error-name').text(xhr.responseJSON.errors.state_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });

            let deleteStateId = null;

            $(document).on('click', '.delete-state', function() {
                deleteStateId = $(this).data('id');
                $('#deleteStateText').html(
                    `Are you sure you want to delete <strong>${$(this).data('name')}</strong>?`
                );
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                $.ajax({
                    url: "{{ route('masters.states.destroy', ':id') }}".replace(':id', deleteStateId),
                    type: "DELETE",
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);

                        $('#row-' + deleteStateId).fadeOut(500, function() {
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
