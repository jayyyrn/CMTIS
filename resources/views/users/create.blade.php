@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('users.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Username <span class="text-danger">*</span></label>
              <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select name="role" class="form-select" required id="roleSelect">
                <option value="teacher">Teacher</option>
                <option value="coordinator">Department Coordinator</option>
                <option value="technician">Technician</option>
                <option value="lead_technician">Lead Technician / Supervisor</option>
                <option value="inventory_officer">Inventory Officer</option>
                <option value="admin">System Administrator</option>
                <option value="head">Head / Department Head (Approver)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <select name="dept_id" class="form-select">
                <option value="">— None —</option>
                @foreach($departments as $d)
                  <option value="{{ $d->dept_id }}" @selected(old('dept_id') == $d->dept_id)>{{ $d->dept_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6" id="specField" style="display:none;">
              <label class="form-label">Specialization</label>
              <select name="specialization" class="form-select">
                <option value="general">General</option>
                <option value="electrical">Electrical</option>
                <option value="aircon">Air Conditioning</option>
                <option value="carpentry">Carpentry</option>
                <option value="fabrication">Fabrication / Welding</option>
                <option value="plumbing">Plumbing</option>
              </select>
            </div>
          </div>
          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Create User</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('roleSelect').addEventListener('change', function() {
  const spec = document.getElementById('specField');
  spec.style.display = ['technician','lead_technician'].includes(this.value) ? 'block' : 'none';
});
</script>
@endpush
@endsection