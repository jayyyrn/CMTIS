@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="stat-card bg-primary"><h6>Users</h6><h3>{{ $stats['users'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-secondary"><h6>Total Requests</h6><h3>{{ $stats['requests'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-warning"><h6>Open</h6><h3>{{ $stats['open'] }}</h3></div></div>
  <div class="col-md-3"><div class="stat-card bg-success"><h6>Inventory Items</h6><h3>{{ $stats['inventory_items'] }}</h3></div></div>
</div>
<div class="card">
  <div class="card-header">Recent Activity</div>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>User</th><th>Action</th><th>Description</th><th>When</th></tr></thead>
      <tbody>
        @foreach($recentLogs as $log)
        <tr>
          <td>{{ $log->user->full_name ?? 'System' }}</td>
          <td><span class="badge bg-info">{{ $log->action }}</span></td>
          <td>{{ $log->description }}</td>
          <td>{{ $log->created_at->diffForHumans() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection