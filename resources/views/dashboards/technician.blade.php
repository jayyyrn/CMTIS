@extends('layouts.app')
@section('title', 'Technician Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="stat-card bg-primary"><h6>Assigned</h6><h3>{{ $stats['assigned'] }}</h3></div></div>
  <div class="col-md-4"><div class="stat-card bg-warning"><h6>Ongoing</h6><h3>{{ $stats['ongoing'] }}</h3></div></div>
  <div class="col-md-4"><div class="stat-card bg-success"><h6>Completed</h6><h3>{{ $stats['completed'] }}</h3></div></div>
</div>
<div class="card">
  <div class="card-header">My Tasks</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Ref No</th><th>Location</th><th>Priority</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($myTasks as $t)
        <tr>
          <td>{{ $t->reference_no }}</td>
          <td>{{ $t->location }}</td>
          <td><span class="badge bg-secondary text-capitalize">{{ $t->priority }}</span></td>
          <td><span class="badge bg-{{ $t->status_badge }} text-capitalize">{{ str_replace('_',' ',$t->status) }}</span></td>
          <td><a href="{{ route('requests.show', $t->request_id) }}" class="btn btn-sm btn-outline-primary">Open</a></td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No tasks assigned.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection