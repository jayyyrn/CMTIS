@extends('layouts.app')
@section('title', 'Maintenance Requests')
@section('content')
<form method="GET" class="row g-2 mb-3">
  <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}"></div>
  <div class="col-md-3">
    <select name="status" class="form-select">
      <option value="">All Statuses</option>
      @foreach(['new','assigned','inspecting','diagnosed','pending_verification','verified','repairing','repaired','closed','rejected'] as $s)
        <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Ref</th><th>Teacher</th><th>Location</th><th>Priority</th><th>Status</th><th>Technician</th><th></th></tr></thead>
      <tbody>
        @forelse($requests as $r)
        <tr>
          <td>{{ $r->reference_no }}</td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-secondary text-capitalize">{{ $r->priority }}</span></td>
          <td><span class="badge bg-{{ $r->status_badge }} text-capitalize">{{ str_replace('_',' ',$r->status) }}</span></td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>
@endsection