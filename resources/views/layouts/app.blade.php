<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>CampusFix — Campus Maintenance System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root {
    --sidebar-bg: #0f172a;
    --sidebar-hover: #1e293b;
    --brand: #3b82f6;
    --brand-dark: #1d4ed8;
  }
  body {
    background: #f1f5f9;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }
  .sidebar {
    min-height: 100vh;
    background: var(--sidebar-bg);
    color: #fff;
    padding: 24px 16px;
    position: sticky;
    top: 0;
  }
  .brand {
    font-weight: 800;
    font-size: 1.35rem;
    margin-bottom: 28px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .brand i { color: var(--brand); font-size: 1.5rem; }
  .sidebar a {
    color: #94a3b8;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 14px;
    border-radius: 8px;
    margin-bottom: 4px;
    font-size: 0.92rem;
    transition: all 0.15s;
  }
  .sidebar a:hover { background: var(--sidebar-hover); color: #fff; }
  .sidebar a.active { background: var(--brand); color: #fff; }
  .sidebar a i { width: 18px; text-align: center; }
  .topbar {
    background: #fff;
    padding: 14px 24px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
  }
  .stat-card {
    border-radius: 14px;
    color: #fff;
    padding: 22px;
    position: relative;
    overflow: hidden;
    height: 100%;
  }
  .stat-card h6 {
    opacity: 0.85;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }
  .stat-card h3 { margin: 0; font-size: 2.1rem; font-weight: 700; }
  .stat-card i {
    position: absolute;
    right: 18px;
    bottom: 14px;
    font-size: 3.5rem;
    opacity: 0.15;
  }
  .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }
  .card-header {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    padding: 14px 20px;
    border-radius: 12px 12px 0 0 !important;
  }
  .badge { font-weight: 500; padding: 5px 10px; }
  .btn { border-radius: 8px; font-weight: 500; }
  .table th {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 600;
  }
  @media (max-width: 768px) {
    .sidebar { min-height: auto; position: relative; }
  }
</style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    @auth
    <div class="col-md-2 sidebar">
      <div class="brand"><i class="bi bi-tools"></i> CampusFix</div>
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
      <a href="{{ route('requests.index') }}" class="{{ request()->routeIs('requests.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard-list"></i> Requests
      </a>
      @if(auth()->user()->role === 'teacher')
        <a href="{{ route('requests.create') }}"><i class="bi bi-plus-circle"></i> Submit Request</a>
      @endif
      @if(in_array(auth()->user()->role, ['inventory_officer','admin']))
        <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
          <i class="bi bi-box-seam"></i> Inventory
        </a>
      @endif
      <a href="{{ route('equipment.index') }}" class="{{ request()->routeIs('equipment.*') ? 'active' : '' }}">
        <i class="bi bi-hdd-stack"></i> Equipment
      </a>
      @if(in_array(auth()->user()->role, ['admin','lead_technician','coordinator','inventory_officer']))
        <a href="{{ route('reports.maintenance') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
          <i class="bi bi-file-earmark-bar-graph"></i> Reports
        </a>
      @endif
      @if(auth()->user()->role === 'admin')
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
          <i class="bi bi-people"></i> Users
        </a>
        <a href="{{ route('reports.audit') }}"><i class="bi bi-shield-check"></i> Audit Trail</a>
      @endif
      <hr style="border-color:#1e293b; margin: 18px 0;">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-sm btn-outline-light w-100">
          <i class="bi bi-box-arrow-right"></i> Logout
        </button>
      </form>
    </div>
    <div class="col-md-10 p-4">
      <div class="topbar d-flex justify-content-between align-items-center">
        <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
        <div class="d-flex align-items-center gap-3">
          @include('partials.notifications')
          <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                 style="width: 36px; height: 36px; font-weight: 600; font-size: 0.85rem;">
              {{ auth()->user()->initials }}
            </div>
            <div class="d-none d-md-block">
              <div style="font-size: 0.85rem; font-weight: 600;">{{ auth()->user()->full_name }}</div>
              <div style="font-size: 0.72rem; color: #64748b;">{{ ucwords(str_replace('_',' ',auth()->user()->role)) }}</div>
            </div>
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
          <i class="bi bi-check-circle"></i> {{ session('success') }}
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
          <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
          <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
          <i class="bi bi-x-circle"></i> <strong>Please fix the following:</strong>
          <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
          <button class="btn-close" data-bs-dismiss="alert"></button>
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