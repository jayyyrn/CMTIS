@extends('layouts.app')
@section('title', "History: {$equipment->name}")
@section('content')
<div class="card mb-3">
  <div class="card-body">
    <h5>{{ $equipment->name }} ({{ $equipment->asset_no }})</h5>
    <p class="text-muted mb-0">
      Location: {{ $equipment->location }} •
      Status: {{ ucfirst(str_replace('_',' ',$equipment->status)) }}
    </p>
  </div>
</div>
<div class="card">
  <div class="card-header">Maintenance History</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Ref</th><th>Reported By</th><th>Technician</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        @forelse($requests as $r)
        <tr>
          <td><a href="{{ route('requests.show', $r->request_id) }}">{{ $r->reference_no }}</a></td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><span class="badge bg-{{ $r->status_badge }} text-capitalize">{{ str_replace('_',' ',$r->status) }}</span></td>
          <td>{{ $r->date_reported->format('M d, Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No history.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection