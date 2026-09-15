@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">
      <h6>Users</h6><h3>{{ $stats['users'] }}</h3><i class="bi bi-people"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#64748b,#334155);">
      <h6>Total Requests</h6><h3>{{ $stats['requests'] }}</h3><i class="bi bi-clipboard-list"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
      <h6>Open</h6><h3>{{ $stats['open'] }}</h3><i class="bi bi-hourglass-split"></i>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card" style="background: linear-gradient(135deg,#10b981,#047857);">
      <h6>Inventory Items</h6><h3>{{ $stats['inventory_items'] }}</h3><i class="bi bi-box-seam"></i>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-header">Recent Activity</div>
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>User</th><th>Action</th><th>Description</th><th>When</th></tr></thead>
      <tbody>
        @forelse($recentLogs as $log)
        <tr>
          <td>{{ $log->user->full_name ?? 'System' }}</td>
          <td><span class="badge bg-info">{{ $log->action }}</span></td>
          <td>{{ $log->description }}</td>
          <td class="text-muted small">{{ $log->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-3">No activity yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection