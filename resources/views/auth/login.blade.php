@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height:100vh; background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);">
  <div class="card p-4 shadow-lg" style="width: 400px; border-radius: 16px;">
    <div class="text-center mb-4">
      <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3"
           style="width: 64px; height: 64px;">
        <i class="bi bi-tools" style="font-size: 1.8rem;"></i>
      </div>
      <h4 class="fw-bold mb-1">CampusFix</h4>
      <p class="text-muted mb-0" style="font-size: 0.85rem;">Campus Maintenance Tracking System</p>
    </div>
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label small fw-semibold">Username</label>
        <input type="text" name="username" class="form-control form-control-lg"
               value="{{ old('username') }}" required autofocus style="font-size:0.95rem;">
      </div>
      <div class="mb-3">
        <label class="form-label small fw-semibold">Password</label>
        <input type="password" name="password" class="form-control form-control-lg" required style="font-size:0.95rem;">
      </div>
      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label small" for="remember">Remember me</label>
      </div>
      <button class="btn btn-primary btn-lg w-100 fw-semibold">Log In</button>
    </form>
  </div>
</div>
@endsection