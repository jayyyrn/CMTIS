@extends('layouts.app')
@section('title', 'New Maintenance Request')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label class="form-label">Problem Description</label>
        <textarea name="problem_description" class="form-control" rows="4" required>{{ old('problem_description') }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Department / Location</label>
        <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Equipment / Asset (optional)</label>
        <select name="equipment_id" class="form-select">
          <option value="">— None —</option>
          @foreach($equipment as $eq)
            <option value="{{ $eq->equipment_id }}" @selected(old('equipment_id') == $eq->equipment_id)>
              {{ $eq->name }} ({{ $eq->asset_no }}) — {{ $eq->location }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Priority</label>
        <select name="priority" class="form-select" required>
          <option value="low">Low</option>
          <option value="medium" selected>Medium</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Attach Photo Evidence (optional)</label>
        <input type="file" name="photo_evidence" class="form-control" accept="image/*">
      </div>
      <div class="mb-3">
        <label class="form-label">Date Reported</label>
        <input type="text" class="form-control" value="{{ now()->format('M d, Y') }}" disabled>
      </div>
      <button class="btn btn-primary">SUBMIT REQUEST</button>
    </form>
  </div>
</div>
@endsection