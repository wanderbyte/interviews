@extends('layouts.master')

@push('title')
    <title>Category Management | Interview Task</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Category Management</h1>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoryModal">
                <i class="fas fa-plus"></i> Add Category
            </button>
        </div>

        <!-- Category Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Categories</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="categoryTable">
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
                                    <td class="cat-name">{{ $category->name }}</td>
                                    <td>{{ $category->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info editBtn" data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $category->id }}">
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

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // ADD / UPDATE CATEGORY
            $('#categoryForm').submit(function(e) {
                e.preventDefault();

                let id = $('#category_id').val();
                let url = id ? `/categories/${id}` : `/categories`;
                let type = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    type: type,
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        name: $('#name').val()
                    },
                    success: function(res) {
                        location.reload();
                    },
                    error: function(err) {
                        $('.error-name').text(err.responseJSON.errors.name[0]);
                    }
                });
            });

            // EDIT
            $('.editBtn').click(function() {
                $('#category_id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('.modal-title').text('Edit Category');
                $('#categoryModal').modal('show');
            });

            // DELETE
            $('.deleteBtn').click(function() {
                if (!confirm('Are you sure?')) return;

                let id = $(this).data('id');

                $.ajax({
                    url: `/categories/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        $('#row-' + id).remove();
                    }
                });
            });

        });
    </script>
@endpush
