@extends('layouts.app')
@section('title', 'Equipment / Assets')
@section('content')
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2">
      <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Search asset, name, location..." value="{{ request('search') }}"></div>
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">All Statuses</option>
          @foreach(['working','under_repair','broken','retired'] as $s)
            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
      @if(auth()->user()->role === 'admin')
      <div class="col-md-2 text-end">
        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addEqModal">
          <i class="bi bi-plus-lg"></i> Add
        </button>
      </div>
      @endif
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Asset No</th><th>Name</th><th>Location</th><th>Department</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($equipment as $e)
        <tr>
          <td class="fw-semibold">{{ $e->asset_no }}</td>
          <td>{{ $e->name }}</td>
          <td>{{ $e->location }}</td>
          <td>{{ $e->department->dept_name ?? '—' }}</td>
          <td>
            @php
              $statusColors = ['working'=>'success','under_repair'=>'warning','broken'=>'danger','retired'=>'secondary'];
            @endphp
            <span class="badge bg-{{ $statusColors[$e->status] ?? 'secondary' }}">
              {{ ucwords(str_replace('_',' ',$e->status)) }}
            </span>
          </td>
          <td><a href="{{ route('equipment.history', $e->equipment_id) }}" class="btn btn-sm btn-outline-primary">History</a></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No equipment found.</td></tr>
        @endforelse
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
        <div class="mb-2"><label class="form-label">Asset No <span class="text-danger">*</span></label><input type="text" name="asset_no" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Location <span class="text-danger">*</span></label><input type="text" name="location" class="form-control" required></div>
        <div class="mb-2">
          <label class="form-label">Department</label>
          <select name="dept_id" class="form-select">
            <option value="">— None —</option>
            @foreach($departments as $d)
              <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label">Status</label>
          <select name="status" class="form-select" required>
            <option value="working">Working</option>
            <option value="under_repair">Under Repair</option>
            <option value="broken">Broken</option>
            <option value="retired">Retired</option>
          </select>
        </div>
        <div class="mb-2"><label class="form-label">Acquired Date</label><input type="date" name="acquired_date" class="form-control"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
@endif
@endsection