@extends('layouts.app')
@section('title', 'Teacher Dashboard')
@section('content')
<a href="{{ route('requests.create') }}" class="btn btn-primary mb-3"><i class="bi bi-plus-lg"></i> SUBMIT REQUEST</a>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="stat-card bg-primary"><h6>My Requests</h6><h3>{{ $stats['total'] }}</h3></div></div>
  <div class="col-md-4"><div class="stat-card bg-warning"><h6>Pending</h6><h3>{{ $stats['pending'] }}</h3></div></div>
</div>
<div class="card">
  <div class="card-header">Recent Requests</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Ref No</th><th>Location</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        @forelse($myRequests as $r)
        <tr>
          <td>{{ $r->reference_no }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->status_badge }} badge-status">{{ str_replace('_',' ', $r->status) }}</span></td>
          <td>{{ $r->date_reported->format('M d, Y') }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No requests yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection