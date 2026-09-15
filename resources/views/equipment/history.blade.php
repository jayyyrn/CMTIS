@extends('layouts.app')
@section('title', "History: {$equipment->name}")
@section('content')
<div class="card mb-3">
  <div class="card-body">
    <h5 class="mb-1">{{ $equipment->name }} <span class="text-muted">({{ $equipment->asset_no }})</span></h5>
    <p class="text-muted mb-0 small">
      <i class="bi bi-geo-alt"></i> {{ $equipment->location }} &middot;
      <i class="bi bi-tag"></i> {{ ucwords(str_replace('_',' ',$equipment->status)) }}
      @if($equipment->department) &middot; {{ $equipment->department->dept_name }} @endif
    </p>
  </div>
</div>

<div class="card">
  <div class="card-header">Maintenance History ({{ $requests->count() }} record{{ $requests->count() === 1 ? '' : 's' }})</div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>Ref</th><th>Reported By</th><th>Technician</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        @forelse($requests as $r)
        <tr>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="fw-semibold">{{ $r->reference_no }}</a></td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><i class="bi {{ $r->work_type_icon }} text-primary"></i> {{ ucfirst($r->work_type) }}</td>
          <td><span class="badge bg-{{ $r->status_badge }}">{{ ucwords(str_replace('_',' ',$r->status)) }}</span></td>
          <td>{{ $r->date_reported->format('M d, Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No maintenance history for this equipment.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection