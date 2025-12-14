@extends('layouts.master')

@push('title')
    <title>Category Management | Interview Task</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Categories</h1>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>

        <!-- Category Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Created At</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $key => $category)
                                <tr id="row-{{ $category->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td class="cat-name">{{ $category->category_name }}</td>
                                    <td>{{ $category->created_at->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-info editBtn" data-id="{{ $category->id }}"
                                            data-name="{{ $category->category_name }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button type="button" class="btn btn-danger delete-category"
                                            data-id="{{ $category->id }}" data-name="{{ $category->category_name }}">
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

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="category_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" class="form-control">
                            <span class="text-danger error-name"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                    <h5 id="deleteCategoryText"></h5>
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

            $('.editBtn').click(function() {
                $('#category_id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('.modal-title').text('Edit Category');
                $('#categoryModal').modal('show');
            });

            $('#categoryForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/categories/save',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: $('#category_id').val(),
                        name: $('#name').val()
                    },
                    success: function(res) {
                        $('#categoryModal').modal('hide');
                        toastr.success(res.message);

                        setTimeout(() => location.reload(), 2000);
                    },
                    error: function(err) {
                        if (err.status === 422) {
                            toastr.error(err.responseJSON.message);
                        } else {
                            toastr.error('Something went wrong');
                        }
                    }
                });
            });

            let categoryId = null;
            let categoryName = null;
            $('.delete-category').on('click', function() {
                categoryId = $(this).data('id');
                categoryName = $(this).data('name');

                $('#deleteCategoryText').html(
                    `Are you sure you want to delete <strong>${categoryName}</strong>?`
                );

                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {

                let btn = $(this);

                btn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...')
                    .prop('disabled', true);

                $.ajax({
                    type: 'DELETE',
                    url: `/categories/${categoryId}`,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {

                        // Hide confirmation modal
                        $('#deleteModal').modal('hide');

                        // Show success toast
                        toastr.success(res.message ?? 'Category deleted successfully');

                        // Reload after 3 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    },
                    error: function() {
                        toastr.error('Failed to delete category');
                    },
                    complete: function() {
                        btn.html('Delete').prop('disabled', false);
                    }
                });
            });

        });
    </script>
@endpush
