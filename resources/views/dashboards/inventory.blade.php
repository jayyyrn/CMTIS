@extends('layouts.app')
@section('title', 'Inventory Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="stat-card bg-primary"><h6>Total Items</h6><h3>{{ $items->count() }}</h3></div></div>
  <div class="col-md-4"><div class="stat-card bg-warning"><h6>Pending Requests</h6><h3>{{ $pendingRequests->count() }}</h3></div></div>
  <div class="col-md-4"><div class="stat-card bg-danger"><h6>Low Stock</h6><h3>{{ $lowStock->count() }}</h3></div></div>
</div>
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between">
    <span>Pending Material Requests</span>
    <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-primary">Manage Inventory</a>
  </div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Request</th><th>Item</th><th>Qty</th><th>Requested By</th><th></th></tr></thead>
      <tbody>
        @forelse($pendingRequests as $mr)
        <tr>
          <td>{{ $mr->request->reference_no ?? '—' }}</td>
          <td>{{ $mr->item->item_name ?? '—' }}</td>
          <td>{{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}</td>
          <td>{{ $mr->requester->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $mr->request_id) }}" class="btn btn-sm btn-primary">Release</a></td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No pending requests.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="card">
  <div class="card-header">Low Stock Alerts</div>
  <ul class="list-group list-group-flush">
    @forelse($lowStock as $i)
      <li class="list-group-item d-flex justify-content-between">
        <span>{{ $i->item_name }}</span>
        <span class="badge bg-danger">{{ $i->qty_on_hand }} left</span>
      </li>
    @empty
      <li class="list-group-item text-muted">No low-stock items.</li>
    @endforelse
  </ul>
</div>
@endsection