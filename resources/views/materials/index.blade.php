@extends('layouts.master')

@push('title')
<title>Material Management | Interview Task</title>
@endpush

@section('main-content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Material Management</h1>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#materialModal">
            <i class="fas fa-plus"></i> Add Material
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Materials</h6>
        </div>

        <div class="card-body">
            <table class="table table-bordered">
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
                    @foreach($materials as $key => $material)
                    <tr id="row-{{ $material->id }}">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $material->category->name }}</td>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->opening_balance }}</td>
                        <td>{{ $material->created_at->format('d-m-Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-info editBtn"
                                data-id="{{ $material->id }}"
                                data-name="{{ $material->name }}"
                                data-category="{{ $material->category_id }}"
                                data-balance="{{ $material->opening_balance }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button class="btn btn-sm btn-danger deleteBtn"
                                data-id="{{ $material->id }}">
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

<!-- Material Modal -->
<div class="modal fade" id="materialModal">
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
                        <label>Category *</label>
                        <select id="category_id" class="form-control">
                            <option value="">Select</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Material Name *</label>
                        <input type="text" id="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Opening Balance *</label>
                        <input type="number" step="0.01" id="opening_balance" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
$('#materialForm').submit(function(e){
    e.preventDefault();

    let id = $('#material_id').val();
    let url = id ? `/materials/${id}` : `/materials`;
    let type = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        type: type,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            category_id: $('#category_id').val(),
            name: $('#name').val(),
            opening_balance: $('#opening_balance').val()
        },
        success: () => location.reload()
    });
});

$('.editBtn').click(function(){
    $('#material_id').val($(this).data('id'));
    $('#category_id').val($(this).data('category'));
    $('#name').val($(this).data('name'));
    $('#opening_balance').val($(this).data('balance'));
    $('#materialModal').modal('show');
});

$('.deleteBtn').click(function(){
    if(!confirm('Delete this material?')) return;

    $.ajax({
        url: `/materials/${$(this).data('id')}`,
        type: 'DELETE',
        data: {_token:$('meta[name="csrf-token"]').attr('content')},
        success: () => location.reload()
    });
});
</script>
@endpush
