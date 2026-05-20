@extends('layouts.app')
@section('title', 'Medicines')
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="glass-card card">
            <div class="card-header"><h3 class="card-title">Add Medicine</h3></div>
            <form method="POST" action="{{ route('admin.medicines.store') }}" class="card-body">
                @csrf
                <div class="mb-2"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
                <div class="mb-2"><label class="form-label">Generic</label><input name="generic_name" class="form-control"></div>
                <div class="mb-2"><label class="form-label">SKU</label><input name="sku" class="form-control"></div>
                <div class="mb-2"><label class="form-label">Price</label><input name="selling_price" type="number" step="0.01" class="form-control" required></div>
                <div class="mb-2"><label class="form-label">GST %</label><input name="gst_percent" type="number" class="form-control" value="5"></div>
                <button class="btn btn-primary w-100">Add</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="glass-card card">
            <div class="card-header"><h3 class="card-title">Medicine Inventory</h3></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Name</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($medicines as $m)
                            <tr>
                                <td>{{ $m->name }}<br><small class="text-muted">{{ $m->generic_name }}</small></td>
                                <td>{{ $m->sku }}</td>
                                <td>₹{{ $m->selling_price }}</td>
                                <td>
                                    @php $stock = $m->stockQuantity(); @endphp
                                    <span class="badge bg-{{ $stock <= $m->reorder_level ? 'danger' : 'success' }}">{{ $stock }}</span>
                                </td>
                                <td>{{ $m->is_active ? 'Active' : 'Inactive' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $medicines->links() }}</div>
        </div>
    </div>
</div>
@endsection
