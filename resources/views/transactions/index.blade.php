@extends('layouts.master')

@push('title')
<title>Material Inward / Outward</title>
@endpush

@section('main-content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Material Inward / Outward</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="transactionForm">
                @csrf

                <div class="form-group">
                    <label>Material *</label>
                    <select id="material_id" class="form-control">
                        <option value="">Select</option>
                        @foreach($materials as $material)
                            <option value="{{ $material->id }}">
                                {{ $material->name }} ({{ $material->category->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" id="transaction_date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Quantity (+ / -) *</label>
                    <input type="number" step="0.01" id="quantity" class="form-control">
                </div>

                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$('#transactionForm').submit(function(e){
    e.preventDefault();

    $.ajax({
        url: '/material-transactions',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            material_id: $('#material_id').val(),
            transaction_date: $('#transaction_date').val(),
            quantity: $('#quantity').val()
        },
        success: () => {
            alert('Transaction saved');
            $('#transactionForm')[0].reset();
        }
    });
});
</script>
@endpush
