@extends('layouts.master')

@push('title')
    <title>Material Management | Interview Task</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Materials</h1>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#materialModal">
                <i class="fas fa-plus"></i> Add Material
            </button>
        </div>

        <!-- Material Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Material Name</th>
                                <th>Opening Balance</th>
                                <th>Created At</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($materials as $key => $material)
                                <tr id="row-{{ $material->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $material->category->category_name }}</td>
                                    <td>{{ $material->material_name }}</td>
                                    <td>{{ number_format($material->opening_balance, 2) }}</td>
                                    <td>{{ $material->created_at->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-info editBtn" data-id="{{ $material->id }}"
                                            data-name="{{ $material->material_name }}"
                                            data-category="{{ $material->category_id }}"
                                            data-balance="{{ $material->opening_balance }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-danger delete-material"
                                            data-id="{{ $material->id }}" data-name="{{ $material->material_name }}">
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

    <!-- Material Modal -->
    <div class="modal fade" id="materialModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="materialForm">
                @csrf
                <input type="hidden" id="material_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Material</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select id="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-category"></span>
                        </div>

                        <div class="form-group">
                            <label>Material Name <span class="text-danger">*</span></label>
                            <input type="text" id="material_name" class="form-control">
                            <span class="text-danger error-name"></span>
                        </div>

                        <div class="form-group">
                            <label>Opening Balance <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="opening_balance" class="form-control">
                            <span class="text-danger error-balance"></span>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5 id="deleteMaterialText"></h5>
                    <p class="text-muted">This action can be reversed (soft delete).</p>
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

            $('#materialModal').on('hidden.bs.modal', function() {
                const modal = $(this);
                modal.find('form')[0].reset();
                modal.find('#material_id').val('');
                modal.find('.text-danger').text('');
                modal.find('.modal-title').text('Add Material');
            });

            $('#opening_balance').on('input', function() {
                let value = $(this).val();

                if (value.includes('.')) {
                    let parts = value.split('.');
                    if (parts[1].length > 2) {
                        $(this).val(parts[0] + '.' + parts[1].slice(0, 2));
                    }
                }
            });

            $('.editBtn').click(function() {
                $('#material_id').val($(this).data('id'));
                $('#material_name').val($(this).data('name'));
                $('#category_id').val($(this).data('category'));
                $('#opening_balance').val($(this).data('balance'));
                $('.modal-title').text('Edit Material');
                $('#materialModal').modal('show');
            });

            $('#materialForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/materials/save',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: $('#material_id').val(),
                        category_id: $('#category_id').val(),
                        material_name: $('#material_name').val(),
                        opening_balance: $('#opening_balance').val()
                    },
                    success: function(res) {
                        $('#materialModal').modal('hide');
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 2000);
                    },
                    error: function(err) {
                        toastr.error(err.responseJSON?.message || 'Validation error');
                    }
                });
            });

            let materialId = null;
            let materialName = null;

            $('.delete-material').click(function() {
                materialId = $(this).data('id');
                materialName = $(this).data('name');

                $('#deleteMaterialText').html(
                    `Are you sure you want to delete <strong>${materialName}</strong>?`
                );

                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {

                let btn = $(this);
                btn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...')
                    .prop('disabled', true);

                $.ajax({
                    type: 'DELETE',
                    url: `/materials/${materialId}`,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 3000);
                    },
                    error: function() {
                        toastr.error('Failed to delete material');
                    },
                    complete: function() {
                        btn.html('Delete').prop('disabled', false);
                    }
                });
            });

        });
    </script>
@endpush
