@extends('layouts.master')

@push('title')
    <title>Inward / Outward Entry | Interview Task</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Inward / Outward Entries</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#transactionModal">
                <i class="fas fa-plus"></i> Add Entry
            </button>
        </div>

        <!-- Transaction Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Material</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $key => $txn)
                                <tr id="row-{{ $txn->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $txn->material->category->category_name }}</td>
                                    <td>{{ $txn->material->material_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($txn->transaction_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="{{ $txn->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($txn->quantity, 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        <button class="btn btn-info editBtn" data-id="{{ $txn->id }}"
                                            data-category="{{ $txn->material->category_id }}"
                                            data-material="{{ $txn->material_id }}"
                                            data-date="{{ $txn->transaction_date }}" data-quantity="{{ $txn->quantity }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger delete-entry" data-id="{{ $txn->id }}">
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

    <!-- Transaction Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="transactionForm">
                @csrf
                <input type="hidden" id="transaction_id" name="id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Inward / Outward</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Material Category <span class="text-danger">*</span></label>
                            <select id="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Material <span class="text-danger">*</span></label>
                            <select id="material_id" class="form-control">
                                <option value="">Select Material</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" id="transaction_date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Quantity (+ / -) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" id="quantity" class="form-control"
                                oninput="this.value=this.value.match(/^-?\d*(\.\d{0,2})?$/)?.[0]||this.value">
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
                    <h5>Are you sure you want to delete this entry?</h5>
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

            /* ----------------------------------------------------------
             |  Reset Modal on Close
             ---------------------------------------------------------- */
            $('#transactionModal').on('hidden.bs.modal', function() {
                $('#transactionForm')[0].reset();
                $('#transaction_id').val('');
                $('#material_id').html('<option value="">Select Material</option>');
                $('.modal-title').text('Add Inward / Outward');
            });

            /* ----------------------------------------------------------
             |  Category → Material Dependent Dropdown
             ---------------------------------------------------------- */
            $('#category_id').on('change', function() {

                let categoryId = $(this).val();
                let materialDropdown = $('#material_id');
                materialDropdown.html('<option value="">Select Material</option>');

                if (!categoryId) {
                    return;
                }

                materialDropdown.html('<option>Loading...</option>');

                $.ajax({
                    url: `/materials/by-category/${categoryId}`,
                    type: 'GET',
                    success: function(data) {

                        let options = '<option value="">Select Material</option>';

                        if (data.length === 0) {
                            options = '<option value="">No material found</option>';
                        } else {
                            data.forEach(function(item) {
                                options +=
                                    `<option value="${item.id}">${item.material_name}</option>`;
                            });
                        }

                        materialDropdown.html(options);
                    },
                    error: function() {
                        toastr.error('Failed to load materials');
                        materialDropdown.html('<option value="">Select Material</option>');
                    }
                });
            });

            /* ----------------------------------------------------------
             |  Save Inward / Outward Entry
             ---------------------------------------------------------- */
            $('#transactionForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/material-transactions/save',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: $('#transaction_id').val(),
                        material_id: $('#material_id').val(),
                        transaction_date: $('#transaction_date').val(),
                        quantity: $('#quantity').val()
                    },
                    success: function(res) {
                        $('#transactionModal').modal('hide');
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 2000);
                    },
                    error: function(err) {
                        if (err.status === 422) {
                            toastr.error('Please fill all required fields correctly');
                        } else {
                            toastr.error('Something went wrong');
                        }
                    }
                });
            });

            /* ----------------------------------------------------------
            |  Edit Inward / Outward Entry
            ---------------------------------------------------------- */
            $('.editBtn').on('click', function() {

                let transactionId = $(this).data('id');
                let categoryId = $(this).data('category');
                let materialId = $(this).data('material');
                let date = $(this).data('date');
                let quantity = $(this).data('quantity');

                // Set hidden ID
                $('#transaction_id').val(transactionId);

                // Set date & quantity
                $('#transaction_date').val(date);
                $('#quantity').val(quantity);

                // Set category
                $('#category_id').val(categoryId).trigger('change');

                // Load materials and select correct one
                setTimeout(function() {
                    $('#material_id').val(materialId);
                }, 300);

                // Update modal title
                $('.modal-title').text('Edit Inward / Outward');

                // Show modal
                $('#transactionModal').modal('show');
            });


            /* ----------------------------------------------------------
             |  Delete Entry
             ---------------------------------------------------------- */
            let deleteId = null;

            $('.delete-entry').on('click', function() {
                deleteId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {

                let btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

                $.ajax({
                    url: `/material-transactions/${deleteId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);
                        setTimeout(() => location.reload(), 2000);
                    },
                    error: function() {
                        toastr.error('Failed to delete entry');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Delete');
                    }
                });
            });

        });
    </script>
@endpush
