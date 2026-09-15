@extends('layouts.app')
@section('title', 'Technician Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">
      <h6>Assigned</h6><h3>{{ $stats['assigned'] }}</h3><i class="bi bi-person-check"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
      <h6>Ongoing</h6><h3>{{ $stats['ongoing'] }}</h3><i class="bi bi-wrench"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#0f172a,#334155);">
      <h6>Waiting Materials</h6><h3>{{ $stats['waiting'] }}</h3><i class="bi bi-box-seam"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#047857);">
      <h6>Completed</h6><h3>{{ $stats['completed'] }}</h3><i class="bi bi-check-circle"></i>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-header">My Tasks</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Ref No</th><th>Location</th><th>Type</th><th>Priority</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($myTasks as $t)
        <tr>
          <td class="fw-semibold">{{ $t->reference_no }}</td>
          <td>{{ $t->location }}</td>
          <td><i class="bi {{ $t->work_type_icon }} text-primary"></i> {{ ucfirst($t->work_type) }}</td>
          <td><span class="badge bg-{{ $t->priority_badge }}">{{ ucfirst($t->priority) }}</span></td>
          <td><span class="badge bg-{{ $t->status_badge }}">{{ ucwords(str_replace('_',' ',$t->status)) }}</span></td>
          <td><a href="{{ route('requests.show', $t->request_id) }}" class="btn btn-sm btn-primary">Open</a></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No tasks assigned.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection