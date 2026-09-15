@extends('layouts.app')
@section('title', 'Maintenance Reports')
@section('content')
<form method="GET" class="row g-2 mb-3">
  <div class="col-md-3"><input type="date" name="from" class="form-control" value="{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}"></div>
  <div class="col-md-3"><input type="date" name="to" class="form-control" value="{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>
<div class="row g-3 mb-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">By Status</div>
      <ul class="list-group list-group-flush">
        @forelse($byStatus as $s => $c)
          <li class="list-group-item d-flex justify-content-between">
            <span class="text-capitalize">{{ str_replace('_',' ',$s) }}</span>
            <strong>{{ $c }}</strong>
          </li>
        @empty
          <li class="list-group-item text-muted">No data.</li>
        @endforelse
      </ul>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">By Department</div>
      <ul class="list-group list-group-flush">
        @forelse($byDept as $d => $c)
          <li class="list-group-item d-flex justify-content-between">
            <span>{{ $d }}</span><strong>{{ $c }}</strong>
          </li>
        @empty
          <li class="list-group-item text-muted">No data.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Ref</th><th>Teacher</th><th>Location</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        @foreach($requests as $r)
        <tr>
          <td>{{ $r->reference_no }}</td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td>{{ $r->location }}</td>
          <td>{{ str_replace('_',' ',$r->status) }}</td>
          <td>{{ $r->date_reported->format('M d, Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection