@extends('layouts.app')
@section('title', 'New Maintenance Request')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-9">
    <div class="card">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-semibold">Problem Description <span class="text-danger">*</span></label>
            <textarea name="problem_description" class="form-control" rows="4" required
                      placeholder="Describe what's wrong...">{{ old('problem_description') }}</textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Type of Work / Problem <span class="text-danger">*</span></label>
              <select name="work_type" class="form-select" required>
                <option value="">Select type...</option>
                <option value="electrical" @selected(old('work_type')=='electrical')>⚡ Electrical</option>
                <option value="aircon" @selected(old('work_type')=='aircon')>❄️ Air Conditioning</option>
                <option value="carpentry" @selected(old('work_type')=='carpentry')>🔨 Carpentry</option>
                <option value="fabrication" @selected(old('work_type')=='fabrication')>🛠️ Fabrication / Welding</option>
                <option value="plumbing" @selected(old('work_type')=='plumbing')>💧 Plumbing</option>
                <option value="general" @selected(old('work_type')=='general')>⚙️ General</option>
                <option value="other" @selected(old('work_type')=='other')>❓ Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
              <select name="priority" class="form-select" required>
                <option value="low" @selected(old('priority')=='low')>🟢 Low</option>
                <option value="medium" @selected(old('priority','medium')=='medium')>🔵 Medium</option>
                <option value="high" @selected(old('priority')=='high')>🟡 High</option>
                <option value="urgent" @selected(old('priority')=='urgent')>🔴 Urgent</option>
              </select>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="form-label fw-semibold">Department / Location <span class="text-danger">*</span></label>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                   placeholder="e.g., Room 201, CCS Building" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Equipment / Asset (optional)</label>
            <select name="equipment_id" class="form-select">
              <option value="">— None / Not sure —</option>
              @foreach($equipment as $eq)
                <option value="{{ $eq->equipment_id }}" @selected(old('equipment_id') == $eq->equipment_id)>
                  {{ $eq->name }} ({{ $eq->asset_no }}) — {{ $eq->location }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Attach Photo Evidence (optional)</label>
            <input type="file" name="photo_evidence" class="form-control" accept="image/*">
            <small class="text-muted">Max 5MB. JPG, PNG. Helps the technician understand the issue.</small>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Date Reported</label>
            <input type="text" class="form-control" value="{{ now()->format('F d, Y') }}" disabled>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-primary px-4"><i class="bi bi-send"></i> Submit Request</button>
            <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection