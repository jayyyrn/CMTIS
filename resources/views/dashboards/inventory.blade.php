@extends('layouts.app')
@section('title', 'Inventory Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">
      <h6>Total Items</h6><h3>{{ $items->count() }}</h3><i class="bi bi-boxes"></i>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
      <h6>Pending Requests</h6><h3>{{ $pendingRequests->count() + $approvedRequests->count() }}</h3><i class="bi bi-hourglass-split"></i>
    </div>
  </div>
  <div class="col-md-4">
    <div class="stat-card" style="background: linear-gradient(135deg,#ef4444,#b91c1c);">
      <h6>Low Stock</h6><h3>{{ $lowStock->count() }}</h3><i class="bi bi-exclamation-triangle"></i>
    </div>
  </div>
</div>

@if($approvedRequests->count() > 0)
<div class="card mb-4">
  <div class="card-header bg-info text-white">Approved — Ready to Release</div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>Request</th><th>Item</th><th>Qty</th><th>Requested By</th><th></th></tr></thead>
      <tbody>
        @foreach($approvedRequests as $mr)
        <tr>
          <td>{{ $mr->request->reference_no ?? '—' }}</td>
          <td>{{ $mr->item->item_name ?? '—' }}</td>
          <td>{{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}</td>
          <td>{{ $mr->requester->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $mr->request_id) }}" class="btn btn-sm btn-primary">Release</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Pending Requests</span>
    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-primary">Manage Inventory</a>
  </div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>Request</th><th>Item</th><th>Qty</th><th>Requested By</th></tr></thead>
      <tbody>
        @forelse($pendingRequests as $mr)
        <tr>
          <td>{{ $mr->request->reference_no ?? '—' }}</td>
          <td>{{ $mr->item->item_name ?? '—' }}</td>
          <td>{{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}</td>
          <td>{{ $mr->requester->full_name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-3">No pending requests.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header bg-danger text-white">Low Stock Alerts</div>
  <ul class="list-group list-group-flush">
    @forelse($lowStock as $i)
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span><i class="bi bi-exclamation-triangle text-warning"></i> {{ $i->item_name }}</span>
        <span class="badge bg-danger">{{ $i->qty_on_hand }} {{ $i->unit }} left (threshold: {{ $i->low_stock_threshold }})</span>
      </li>
    @empty
      <li class="list-group-item text-muted text-center py-3">No low-stock items.</li>
    @endforelse
  </ul>
</div>
@endsection