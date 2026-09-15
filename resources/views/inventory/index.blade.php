@extends('layouts.app')
@section('title', 'Inventory')
@section('content')
<form method="GET" class="row g-2 mb-3">
  <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Search item..." value="{{ request('search') }}"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
  <div class="col-md-4 text-end">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addItemModal">+ Add Item</button>
  </div>
</form>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Item</th><th>Category</th><th>On Hand</th><th>Threshold</th><th></th></tr></thead>
      <tbody>
        @foreach($items as $i)
        <tr class="{{ $i->isLowStock() ? 'table-warning' : '' }}">
          <td>{{ $i->item_name }}</td>
          <td>{{ $i->category ?? '—' }}</td>
          <td>{{ $i->qty_on_hand }} {{ $i->unit }}</td>
          <td>{{ $i->low_stock_threshold }}</td>
          <td>
            <form method="POST" action="{{ route('inventory.stockIn', $i->item_id) }}" class="d-flex gap-1">
              @csrf
              <input type="number" name="qty" min="1" class="form-control form-control-sm" style="width:80px;" placeholder="Qty" required>
              <button class="btn btn-sm btn-success">Stock In</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $items->links() }}</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('inventory.store') }}" class="modal-content">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Add Inventory Item</h5></div>
      <div class="modal-body">
        <div class="mb-2"><input type="text" name="item_name" class="form-control" placeholder="Item name" required></div>
        <div class="mb-2"><input type="text" name="category" class="form-control" placeholder="Category"></div>
        <div class="mb-2"><input type="text" name="unit" class="form-control" placeholder="Unit (pcs, box, etc.)" value="pcs" required></div>
        <div class="mb-2"><input type="number" name="qty_on_hand" class="form-control" placeholder="Initial stock" value="0" required></div>
        <div class="mb-2"><input type="number" name="low_stock_threshold" class="form-control" placeholder="Low stock threshold" value="5" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>
@endsection