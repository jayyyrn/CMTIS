@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('content')
<a href="{{ route('requests.create') }}" class="btn btn-primary mb-3">
  <i class="bi bi-plus-lg"></i> Submit New Request
</a>
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">
      <h6>My Requests</h6><h3>{{ $stats['total'] }}</h3><i class="bi bi-clipboard-list"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
      <h6>Pending</h6><h3>{{ $stats['pending'] }}</h3><i class="bi bi-hourglass-split"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#ef4444,#b91c1c);">
      <h6>Urgent</h6><h3>{{ $stats['urgent'] }}</h3><i class="bi bi-exclamation-triangle"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#047857);">
      <h6>Completed</h6><h3>{{ $stats['completed'] }}</h3><i class="bi bi-check-circle"></i>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-header">Recent Requests</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Ref No</th><th>Type</th><th>Location</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        @forelse($myRequests as $r)
        <tr>
          <td class="fw-semibold">{{ $r->reference_no }}</td>
          <td><i class="bi {{ $r->work_type_icon }} text-primary"></i> {{ ucfirst($r->work_type) }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->status_badge }}">{{ ucwords(str_replace('_',' ', $r->status)) }}</span></td>
          <td>{{ $r->date_reported->format('M d, Y') }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No requests yet. Click "Submit New Request" to begin.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection