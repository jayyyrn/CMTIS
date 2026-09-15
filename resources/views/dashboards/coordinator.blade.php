@extends('layouts.app')
@section('title', 'Coordinator Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#64748b,#334155);">
      <h6>Total</h6><h3>{{ $stats['total'] }}</h3><i class="bi bi-collection"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#0ea5e9,#0369a1);">
      <h6>New</h6><h3>{{ $stats['new'] }}</h3><i class="bi bi-stars"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
      <h6>Ongoing</h6><h3>{{ $stats['ongoing'] }}</h3><i class="bi bi-arrow-repeat"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#047857);">
      <h6>Completed</h6><h3>{{ $stats['completed'] }}</h3><i class="bi bi-check-circle"></i>
    </div>
  </div>
</div>
@if($stats['waiting'] > 0)
  <div class="alert alert-dark">
    <i class="bi bi-box-seam"></i> <strong>{{ $stats['waiting'] }}</strong> request(s) waiting for materials.
  </div>
@endif
<div class="card">
  <div class="card-header">Department Requests</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Ref</th><th>Teacher</th><th>Type</th><th>Location</th><th>Status</th><th>Technician</th><th></th></tr></thead>
      <tbody>
        @forelse($deptRequests as $r)
        <tr>
          <td class="fw-semibold">{{ $r->reference_no }}</td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td><i class="bi {{ $r->work_type_icon }} text-primary"></i> {{ ucfirst($r->work_type) }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->status_badge }}">{{ ucwords(str_replace('_',' ',$r->status)) }}</span></td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No department requests.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection