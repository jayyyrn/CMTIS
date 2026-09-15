@extends('layouts.app')
@section('title', 'Department Coordinator Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="stat-card bg-secondary"><h6>Total</h6><h3>{{ $stats['total'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-info"><h6>New</h6><h3>{{ $stats['new'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-warning"><h6>Ongoing</h6><h3>{{ $stats['ongoing'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-success"><h6>Completed</h6><h3>{{ $stats['completed'] }}</h3></div></div>
</div>
<div class="card">
  <div class="card-header">Department Requests</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Ref</th><th>Teacher</th><th>Location</th><th>Status</th><th>Technician</th><th></th></tr></thead>
      <tbody>
        @forelse($deptRequests as $r)
        <tr>
          <td>{{ $r->reference_no }}</td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->status_badge }} text-capitalize">{{ str_replace('_',' ',$r->status) }}</span></td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted">No department requests.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection