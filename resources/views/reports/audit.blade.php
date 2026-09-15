@extends('layouts.app')
@section('title', 'Audit Trail')
@section('content')
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>When</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
      <tbody>
        @foreach($logs as $log)
        <tr>
          <td class="small">{{ $log->created_at->format('M d, Y H:i') }}</td>
          <td>{{ $log->user->full_name ?? 'System' }}</td>
          <td><span class="badge bg-info">{{ $log->action }}</span></td>
          <td>{{ $log->description }}</td>
          <td class="small text-muted">{{ $log->ip_address }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $logs->links() }}</div>
@endsection