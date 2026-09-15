@extends('layouts.app')
@section('title', 'Supervisor Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="stat-card bg-secondary"><h6>New</h6><h3>{{ $stats['new'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-primary"><h6>Assigned</h6><h3>{{ $stats['assigned'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-danger"><h6>Pending Verify</h6><h3>{{ $stats['pending_verify'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-success"><h6>Completed</h6><h3>{{ $stats['completed'] }}</h3></div></div>
</div>
<div class="card mb-4">
  <div class="card-header bg-danger text-white">Diagnoses Pending Verification</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Ref No</th><th>Technician</th><th>Findings</th><th></th></tr></thead>
      <tbody>
        @forelse($pendingVerify as $d)
        <tr>
          <td>{{ $d->request->reference_no ?? '—' }}</td>
          <td>{{ $d->technician->full_name ?? '—' }}</td>
          <td>{{ \Illuminate\Support\Str::limit($d->findings, 60) }}</td>
          <td><a href="{{ route('requests.show', $d->request_id) }}" class="btn btn-sm btn-primary">Review</a></td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted">No pending verifications.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="card">
  <div class="card-header">All Recent Requests</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Ref</th><th>Location</th><th>Status</th><th>Technician</th><th></th></tr></thead>
      <tbody>
        @foreach($allRequests as $r)
        <tr>
          <td>{{ $r->reference_no }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->status_badge }} text-capitalize">{{ str_replace('_',' ',$r->status) }}</span></td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection