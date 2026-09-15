@extends('layouts.app')
@section('title', 'Supervisor Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#0ea5e9,#0369a1);">
      <h6>New</h6><h3>{{ $stats['new'] }}</h3><i class="bi bi-stars"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
      <h6>Pending Verify</h6><h3>{{ $stats['pending_verify'] }}</h3><i class="bi bi-shield-exclamation"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#ef4444,#b91c1c);">
      <h6>Material Approvals</h6><h3>{{ $stats['pending_approvals'] }}</h3><i class="bi bi-clipboard-check"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#047857);">
      <h6>Completed</h6><h3>{{ $stats['completed'] }}</h3><i class="bi bi-check-circle"></i>
    </div>
  </div>
</div>

@if($pendingApprovals->count() > 0)
  <div class="card mb-4">
  <div class="card-header bg-warning">Material Requests — Awaiting Endorsement to LGU</div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>Request</th><th>Item</th><th>Qty</th><th>Requested By</th><th></th></tr></thead>
      <tbody>
        @foreach($pendingApprovals as $mr)
        <tr>
          <td><a href="{{ route('requests.show', $mr->request_id) }}">{{ $mr->request->reference_no ?? '—' }}</a></td>
          <td>{{ $mr->item->item_name ?? '—' }}</td>
          <td>{{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}</td>
          <td>{{ $mr->requester->full_name ?? '—' }}</td>
          <td>
            <form method="POST" action="{{ route('material.approve', $mr->mat_req_id) }}">
              @csrf
              <button class="btn btn-sm btn-warning">Endorse to LGU</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<div class="card mb-4">
  <div class="card-header bg-warning">Diagnoses Pending Verification</div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
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
        <tr><td colspan="4" class="text-center text-muted py-3">No pending verifications.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card">
  <div class="card-header">All Recent Requests</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Ref</th><th>Location</th><th>Type</th><th>Status</th><th>Technician</th><th></th></tr></thead>
      <tbody>
        @foreach($allRequests as $r)
        <tr>
          <td class="fw-semibold">{{ $r->reference_no }}</td>
          <td>{{ $r->location }}</td>
          <td>{{ ucfirst($r->work_type) }}</td>
          <td><span class="badge bg-{{ $r->status_badge }}">{{ ucwords(str_replace('_',' ',$r->status)) }}</span></td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection