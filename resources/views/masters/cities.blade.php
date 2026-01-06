@extends('layouts.master')

@push('title')
    <title>City / Village Management | Masters</title>
@endpush

@section('main-content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-flex justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Masters → Cities / Villages</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#cityModal">
                <i class="fas fa-plus"></i> Add City / Village
            </button>
        </div>

        <!-- Table -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>State</th>
                                <th>District</th>
                                <th>City / Village</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $key => $city)
                                <tr id="row-{{ $city->id }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $city->district->state->state_name }}</td>
                                    <td>{{ $city->district->district_name }}</td>
                                    <td>{{ $city->city_name }}</td>
                                    <td>
                                        <!-- Edit -->
                                        <button class="btn btn-info edit-city" data-id="{{ $city->id }}"
                                            data-city="{{ $city->city_name }}" data-state="{{ $city->district->state->id }}"
                                            data-district="{{ $city->district->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete -->
                                        <button class="btn btn-danger delete-city" data-id="{{ $city->id }}"
                                            data-name="{{ $city->city_name }}">
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

    <!-- City Modal -->
    <div class="modal fade" id="cityModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="cityForm">
                @csrf
                <input type="hidden" id="city_id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add City / Village</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <!-- State -->
                        <div class="form-group">
                            <label>State <span class="text-danger">*</span></label>
                            <select id="state_id" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- District -->
                        <div class="form-group">
                            <label>District <span class="text-danger">*</span></label>
                            <select id="district_id" class="form-control">
                                <option value="">Select District</option>
                            </select>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label>City / Village Name <span class="text-danger">*</span></label>
                            <input type="text" id="city_name" class="form-control">
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
                    <h5 id="deleteCityText"></h5>
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

            function loadDistricts(stateId, selectedDistrict = null) {
                $('#district_id').html('<option>Loading...</option>');

                $.ajax({
                    url: "{{ route('masters.get.districts', ':id') }}".replace(':id', stateId),
                    type: "GET",
                    success: function(data) {
                        let options = '<option value="">Select District</option>';

                        data.forEach(d => {
                            options += `
                            <option value="${d.id}" ${selectedDistrict == d.id ? 'selected' : ''}>
                                ${d.district_name}
                            </option>`;
                        });

                        $('#district_id').html(options);
                    },
                    error: function() {
                        toastr.error('Failed to load districts');
                    }
                });
            }

            // State change
            $(document).on('change', '#state_id', function() {
                let stateId = $(this).val();

                if (stateId) {
                    loadDistricts(stateId);
                } else {
                    $('#district_id').html('<option value="">Select District</option>');
                }
            });

            // Edit City / Village
            $(document).on('click', '.edit-city', function() {
                $('#city_id').val($(this).data('id'));
                $('#city_name').val($(this).data('city'));
                $('#state_id').val($(this).data('state'));

                loadDistricts($(this).data('state'), $(this).data('district'));

                $('.modal-title').text('Edit City / Village');
                $('#cityModal').modal('show');
            });

            // Save City / Village
            $('#cityForm').on('submit', function(e) {
                e.preventDefault();

                $('.error-name').text('');

                $.ajax({
                    url: "{{ route('masters.cities.save') }}",
                    type: "POST",
                    data: {
                        id: $('#city_id').val(),
                        district_id: $('#district_id').val(),
                        city_name: $('#city_name').val()
                    },
                    success: function(res) {
                        $('#cityModal').modal('hide');
                        toastr.success(res.message);

                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('.error-name').text(xhr.responseJSON.errors.city_name[0]);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });

            // Reset modal
            $('#cityModal').on('hidden.bs.modal', function() {
                $('#cityForm')[0].reset();
                $('#city_id').val('');
                $('#district_id').html('<option value="">Select District</option>');
                $('.error-name').text('');
                $('.modal-title').text('Add City / Village');
            });

            let deleteCityId = null;

            // Open delete modal
            $(document).on('click', '.delete-city', function() {
                deleteCityId = $(this).data('id');

                $('#deleteCityText').html(
                    `Are you sure you want to delete <strong>${$(this).data('name')}</strong>?`
                );

                $('#deleteModal').modal('show');
            });

            // Confirm delete
            $('#confirmDelete').on('click', function() {
                if (!deleteCityId) return;

                $.ajax({
                    url: "{{ route('masters.cities.destroy', ':id') }}".replace(':id',
                        deleteCityId),
                    type: "DELETE",
                    success: function(res) {
                        $('#deleteModal').modal('hide');
                        toastr.success(res.message);

                        $('#row-' + deleteCityId).fadeOut(500, function() {
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
