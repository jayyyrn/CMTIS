@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height:100vh;">
  <div class="card p-4 shadow" style="width: 380px;">
    <div class="text-center mb-3">
      <h3 class="text-primary"><i class="bi bi-tools"></i> CampusFix</h3>
      <small class="text-muted">Campus Maintenance Tracking System</small>
    </div>
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">Remember me</label>
      </div>
      <button class="btn btn-primary w-100">LOG IN</button>
    </form>
  </div>
</div>
@endsection