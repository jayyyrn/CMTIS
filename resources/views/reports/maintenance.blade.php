@extends('layouts.app')
@section('title', 'Maintenance Reports')
@section('content')
<form method="GET" class="card mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label small">From</label>
        <input type="date" name="from" class="form-control" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label small">To</label>
        <input type="date" name="to" class="form-control" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100">Apply Filter</button>
      </div>
    </div>
  </div>
</form>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header">By Status</div>
      <ul class="list-group list-group-flush">
        @forelse($byStatus as $s => $c)
          <li class="list-group-item d-flex justify-content-between">
            <span>{{ ucwords(str_replace('_',' ',$s)) }}</span>
            <strong>{{ $c }}</strong>
          </li>
        @empty
          <li class="list-group-item text-muted">No data.</li>
        @endforelse
      </ul>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header">By Work Type</div>
      <ul class="list-group list-group-flush">
        @forelse($byWorkType as $w => $c)
          <li class="list-group-item d-flex justify-content-between">
            <span>{{ ucfirst($w) }}</span>
            <strong>{{ $c }}</strong>
          </li>
        @empty
          <li class="list-group-item text-muted">No data.</li>
        @endforelse
      </ul>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header">By Department</div>
      <ul class="list-group list-group-flush">
        @forelse($byDept as $d => $c)
          <li class="list-group-item d-flex justify-content-between">
            <span>{{ $d }}</span>
            <strong>{{ $c }}</strong>
          </li>
        @empty
          <li class="list-group-item text-muted">No data.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">Detailed Records ({{ $requests->count() }})</div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>Ref</th><th>Teacher</th><th>Type</th><th>Location</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        @foreach($requests as $r)
        <tr>
          <td>{{ $r->reference_no }}</td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td>{{ ucfirst($r->work_type) }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->status_badge }}">{{ ucwords(str_replace('_',' ',$r->status)) }}</span></td>
          <td>{{ $r->date_reported->format('M d, Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection