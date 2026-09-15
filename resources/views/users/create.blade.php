@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('users.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="col-md-6">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required>
            <option value="teacher">Teacher</option>
            <option value="coordinator">Department Maintenance Coordinator</option>
            <option value="technician">Technician</option>
            <option value="lead_technician">Lead Technician / Supervisor</option>
            <option value="inventory_officer">Inventory Officer</option>
            <option value="admin">System Administrator</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Department</label>
          <select name="dept_id" class="form-select">
            <option value="">— None —</option>
            @foreach($departments as $d)
              <option value="{{ $d->dept_id }}">{{ $d->dept_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <button class="btn btn-primary mt-3">Create User</button>
    </form>
  </div>
</div>
@endsection