@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<a href="{{ route('users.create') }}" class="btn btn-primary mb-3">+ Add User</a>
<div class="card">
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Department</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @foreach($users as $u)
        <tr>
          <td>{{ $u->full_name }}</td>
          <td>{{ $u->username }}</td>
          <td><span class="badge bg-primary text-capitalize">{{ str_replace('_',' ',$u->role) }}</span></td>
          <td>{{ $u->department->dept_name ?? '—' }}</td>
          <td><span class="badge bg-{{ $u->status==='active'?'success':'secondary' }}">{{ $u->status }}</span></td>
          <td>
            <form method="POST" action="{{ route('users.toggle', $u->user_id) }}">
              @csrf
              <button class="btn btn-sm btn-outline-warning">Toggle</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection