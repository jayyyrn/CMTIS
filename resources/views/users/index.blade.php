@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <form method="GET" class="row g-2 flex-grow-1 me-3">
    <div class="col-md-5"><input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}"></div>
    <div class="col-md-3">
      <select name="role" class="form-select">
        <option value="">All Roles</option>
        @foreach(['teacher','coordinator','technician','lead_technician','inventory_officer','admin'] as $r)
          <option value="{{ $r }}" @selected(request('role') == $r)>{{ ucwords(str_replace('_',' ',$r)) }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
  </form>
  <a href="{{ route('users.create') }}" class="btn btn-success align-self-start"><i class="bi bi-plus-lg"></i> Add User</a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table mb-0 align-middle">
      <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @foreach($users as $u)
        <tr>
          <td class="fw-semibold">{{ $u->full_name }}</td>
          <td>{{ $u->username }}</td>
          <td class="small">{{ $u->email }}</td>
          <td><span class="badge bg-primary">{{ ucwords(str_replace('_',' ',$u->role)) }}</span></td>
          <td>{{ $u->department->dept_name ?? '—' }}</td>
          <td>
            <span class="badge bg-{{ $u->status === 'active' ? 'success' : 'secondary' }}">
              {{ ucfirst($u->status) }}
            </span>
          </td>
          <td>
            @if($u->user_id !== auth()->id())
              <form method="POST" action="{{ route('users.toggle', $u->user_id) }}">
                @csrf
                <button class="btn btn-sm btn-outline-warning">Toggle</button>
              </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection