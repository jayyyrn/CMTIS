@extends('layouts.app')
@section('title', 'Equipment / Assets')
@section('content')
<form method="GET" class="row g-2 mb-3">
  <div class="col-md-6"><input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
  @if(auth()->user()->role === 'admin')
  <div class="col-md-4 text-end"><button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEqModal">+ Add Equipment</button></div>
  @endif
</form>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Asset No</th><th>Name</th><th>Location</th><th>Department</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @foreach($equipment as $e)
        <tr>
          <td>{{ $e->asset_no }}</td>
          <td>{{ $e->name }}</td>
          <td>{{ $e->location }}</td>
          <td>{{ $e->department->dept_name ?? '—' }}</td>
          <td><span class="badge bg-secondary text-capitalize">{{ str_replace('_',' ',$e->status) }}</span></td>
          <td><a href="{{ route('equipment.history', $e->equipment_id) }}" class="btn btn-sm btn-outline-primary">History</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $equipment->links() }}</div>

@if(auth()->user()->role === 'admin')
<div class="modal fade" id="addEqModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('equipment.store') }}" class="modal-content">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Add Equipment</h5></div>
      <div class="modal-body">
        <div class="mb-2"><input type="text" name="asset_no" class="form-control" placeholder="Asset No" required></div>
        <div class="mb-2"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
        <div class="mb-2"><input type="text" name="location" class="form-control" placeholder="Location" required></div>
        <div class="mb-2">
          <select name="status" class="form-select" required>
            <option value="working">Working</option>
            <option value="under_repair">Under Repair</option>
            <option value="broken">Broken</option>
            <option value="retired">Retired</option>
          </select>
        </div>
        <div class="mb-2"><input type="date" name="acquired_date" class="form-control"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Save</button></div>
    </form>
  </div>
</div>
@endif
@endsection