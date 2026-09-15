@extends('layouts.app')
@section('title', 'Inventory')
@section('content')
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search item..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="category" class="form-select">
          <option value="">All Categories</option>
          @foreach(['electrical','carpentry','plumbing','fabrication','consumables','tools','other'] as $c)
            <option value="{{ $c }}" @selected(request('category') == $c)>{{ ucfirst($c) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <div class="form-check mt-2">
          <input type="checkbox" name="low_stock" value="1" class="form-check-input" id="lowStock" @checked(request('low_stock'))>
          <label for="lowStock" class="form-check-label small">Low stock only</label>
        </div>
      </div>
      <div class="col-md-1">
        <button class="btn btn-primary w-100">Filter</button>
      </div>
      <div class="col-md-2 text-end">
        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addItemModal">
          <i class="bi bi-plus-lg"></i> Add Item
        </button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Item</th><th>Category</th><th>On Hand</th><th>Threshold</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($items as $i)
        <tr class="{{ $i->isLowStock() ? 'table-warning' : '' }}">
          <td class="fw-semibold">{{ $i->item_name }}</td>
          <td><span class="badge bg-secondary">{{ ucfirst($i->category) }}</span></td>
          <td><strong>{{ $i->qty_on_hand }}</strong> <small class="text-muted">{{ $i->unit }}</small></td>
          <td>{{ $i->low_stock_threshold ?? '—' }}</td>
          <td>
            @if($i->qty_on_hand === 0)
              <span class="badge bg-danger">Out of Stock</span>
            @elseif($i->isLowStock())
              <span class="badge bg-warning text-dark">Low Stock</span>
            @else
              <span class="badge bg-success">OK</span>
            @endif
          </td>
          <td>
            <button type="button" class="btn btn-sm btn-success"
                    data-bs-toggle="modal" data-bs-target="#stockInModal{{ $i->item_id }}">
              <i class="bi bi-box-arrow-in-down"></i> Stock In
            </button>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No items yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $items->links() }}</div>

{{-- ======================= --}}
{{-- STOCK-IN MODALS (OUTSIDE TABLE — this fixes the bug) --}}
{{-- ======================= --}}
@foreach($items as $i)
  <div class="modal fade" id="stockInModal{{ $i->item_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('inventory.stockIn', $i->item_id) }}" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Stock In: {{ $i->item_name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-3">Current on hand: <strong>{{ $i->qty_on_hand }} {{ $i->unit }}</strong></p>
          <div class="mb-2">
            <label class="form-label">Quantity Received <span class="text-danger">*</span></label>
            <input type="number" name="qty" class="form-control" min="1" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Date Received <span class="text-danger">*</span></label>
            <input type="date" name="transaction_date" class="form-control" value="{{ now()->toDateString() }}" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Supplier / Source</label>
            <input type="text" name="supplier" class="form-control" placeholder="e.g., Local supplier, LGU">
          </div>
          <div class="mb-2">
            <label class="form-label">Receipt / EPR / PR Reference No.</label>
            <input type="text" name="reference_no" class="form-control" placeholder="Optional">
          </div>
          <div class="mb-2">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-success">Save Stock In</button>
        </div>
      </form>
    </div>
  </div>
@endforeach

{{-- ======================= --}}
{{-- ADD ITEM MODAL (also outside table) --}}
{{-- ======================= --}}
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('inventory.store') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Add Inventory Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Item Name <span class="text-danger">*</span></label>
          <input type="text" name="item_name" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Category <span class="text-danger">*</span></label>
          <select name="category" class="form-select" required>
            <option value="electrical">Electrical</option>
            <option value="carpentry">Carpentry</option>
            <option value="plumbing">Plumbing</option>
            <option value="fabrication">Fabrication</option>
            <option value="consumables">Consumables</option>
            <option value="tools">Tools</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label">Unit <span class="text-danger">*</span></label>
          <input type="text" name="unit" class="form-control" value="pcs" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Initial Quantity <span class="text-danger">*</span></label>
          <input type="number" name="qty_on_hand" class="form-control" value="0" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Low Stock Threshold</label>
          <input type="number" name="low_stock_threshold" class="form-control" placeholder="Leave blank if not tracked">
          <small class="text-muted">Optional. Set to trigger low-stock alerts.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Save Item</button>
      </div>
    </form>
  </div>
</div>
@endsection