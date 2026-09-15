@extends('layouts.app')
@section('title', 'Maintenance Requests')
@section('content')
<form method="GET" class="card mb-3">
  <div class="card-body">
    <div class="row g-2">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control" placeholder="Search ref, location..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">All Statuses</option>
          @foreach(['new','assigned','inspecting','diagnosed','waiting_for_materials','pending_verification','verified','repairing','repaired','closed','rejected'] as $s)
            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <select name="work_type" class="form-select">
          <option value="">All Types</option>
          @foreach(['electrical','aircon','carpentry','fabrication','plumbing','general','other'] as $w)
            <option value="{{ $w }}" @selected(request('work_type') == $w)>{{ ucfirst($w) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-1">
        <select name="priority" class="form-select">
          <option value="">Priority</option>
          @foreach(['low','medium','high','urgent'] as $p)
            <option value="{{ $p }}" @selected(request('priority') == $p)>{{ ucfirst($p) }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-1"><input type="date" name="from" class="form-control" value="{{ request('from') }}"></div>
      <div class="col-md-1"><input type="date" name="to" class="form-control" value="{{ request('to') }}"></div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
      </div>
    </div>
  </div>
</form>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0 align-middle">
      <thead><tr><th>Ref</th><th>Teacher</th><th>Type</th><th>Location</th><th>Priority</th><th>Status</th><th>Technician</th><th></th></tr></thead>
      <tbody>
        @forelse($requests as $r)
        <tr>
          <td class="fw-semibold">{{ $r->reference_no }}</td>
          <td>{{ $r->teacher->full_name ?? '—' }}</td>
          <td><i class="bi {{ $r->work_type_icon }} text-primary"></i> {{ ucfirst($r->work_type) }}</td>
          <td>{{ $r->location }}</td>
          <td><span class="badge bg-{{ $r->priority_badge }}">{{ ucfirst($r->priority) }}</span></td>
          <td><span class="badge bg-{{ $r->status_badge }}">{{ ucwords(str_replace('_',' ',$r->status)) }}</span></td>
          <td>{{ $r->technician->full_name ?? '—' }}</td>
          <td><a href="{{ route('requests.show', $r->request_id) }}" class="btn btn-sm btn-outline-primary">View</a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">No requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>
@endsection