<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Campus Maintenance System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body { background: #f4f6f9; }
  .sidebar { min-height: 100vh; background: #1e293b; color: #fff; padding: 20px; }
  .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: 10px; border-radius: 6px; margin-bottom: 4px; }
  .sidebar a:hover, .sidebar a.active { background: #334155; color: #fff; }
  .brand { font-weight: 700; font-size: 1.2rem; margin-bottom: 20px; color: #38bdf8; }
  .stat-card { border-radius: 12px; color: #fff; padding: 20px; }
  .stat-card h3 { margin: 0; font-size: 2rem; }
  .badge-status { text-transform: capitalize; }
  @media (max-width: 768px) { .sidebar { min-height: auto; } }
</style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    @auth
    <div class="col-md-2 sidebar">
      <div class="brand"><i class="bi bi-tools"></i> CampusFix</div>
      <a href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
      <a href="{{ route('requests.index') }}"><i class="bi bi-clipboard-list"></i> Requests</a>
      @if(auth()->user()->role === 'teacher')
        <a href="{{ route('requests.create') }}"><i class="bi bi-plus-circle"></i> Submit Request</a>
      @endif
      @if(in_array(auth()->user()->role, ['inventory_officer','admin']))
        <a href="{{ route('inventory.index') }}"><i class="bi bi-box-seam"></i> Inventory</a>
      @endif
      <a href="{{ route('equipment.index') }}"><i class="bi bi-hdd-stack"></i> Equipment</a>
      @if(in_array(auth()->user()->role, ['admin','lead_technician','coordinator','inventory_officer']))
        <a href="{{ route('reports.maintenance') }}"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
      @endif
      @if(auth()->user()->role === 'admin')
        <a href="{{ route('users.index') }}"><i class="bi bi-people"></i> Users</a>
        <a href="{{ route('reports.audit') }}"><i class="bi bi-shield-check"></i> Audit Trail</a>
      @endif
      <hr style="border-color:#334155;">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>
    <div class="col-md-10 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
        <div class="d-flex align-items-center gap-3">
          @include('partials.notifications')
          <div>
            <span class="badge bg-primary text-capitalize">{{ str_replace('_',' ',auth()->user()->role) }}</span>
            <span class="ms-2">{{ auth()->user()->full_name }}</span>
          </div>
        </div>
      </div>
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif
      @yield('content')
    </div>
    @else
      @yield('content')
    @endauth
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>