@extends('layouts.app')
@section('title', "Request {$req->reference_no}")
@section('content')
<div class="row g-3">
  <div class="col-md-8">

    {{-- Main info --}}
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <strong style="font-size:1.1rem;">{{ $req->reference_no }}</strong>
          <span class="badge bg-{{ $req->priority_badge }} ms-2">{{ ucfirst($req->priority) }}</span>
        </div>
        <span class="badge bg-{{ $req->status_badge }}" style="font-size:0.85rem;">
          {{ ucwords(str_replace('_',' ', $req->status)) }}
        </span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <small class="text-muted d-block">Teacher</small>
            <strong>{{ $req->teacher->full_name ?? '—' }}</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Department</small>
            <strong>{{ $req->department->dept_name ?? '—' }}</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Type of Work</small>
            <strong><i class="bi {{ $req->work_type_icon }} text-primary"></i> {{ ucfirst($req->work_type) }}</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Location</small>
            <strong>{{ $req->location }}</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Equipment</small>
            <strong>{{ $req->equipment->name ?? '—' }}</strong>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block">Date Reported</small>
            <strong>{{ $req->date_reported->format('M d, Y') }}</strong>
          </div>
        </div>
        <hr>
        <small class="text-muted d-block mb-1">Problem Description</small>
        <p class="mb-0">{{ $req->problem_description }}</p>

        @if($req->photo_evidence)
          <hr>
          <small class="text-muted d-block mb-1">Photo Evidence</small>
          <img src="{{ asset('storage/'.$req->photo_evidence) }}" class="img-fluid rounded" style="max-width: 400px;">
        @endif
        @if($req->after_repair_photo)
          <hr>
          <small class="text-muted d-block mb-1">After Repair</small>
          <img src="{{ asset('storage/'.$req->after_repair_photo) }}" class="img-fluid rounded" style="max-width: 400px;">
        @endif
      </div>
    </div>

    {{-- Diagnoses --}}
    <div class="card mb-3">
      <div class="card-header">Diagnoses</div>
      <ul class="list-group list-group-flush">
        @forelse($req->diagnoses as $d)
          <li class="list-group-item">
            <div class="d-flex justify-content-between">
              <strong>{{ $d->technician->full_name ?? '—' }}</strong>
              <span class="badge bg-{{ $d->verification_badge }}">{{ ucfirst($d->verification_status) }}</span>
            </div>
            <p class="mb-1 mt-2"><strong>Findings:</strong> {{ $d->findings }}</p>
            @if($d->recommended_action)
              <p class="mb-1"><strong>Recommended:</strong> {{ $d->recommended_action }}</p>
            @endif
            @if($d->is_major)
              <p class="text-danger mb-1"><i class="bi bi-exclamation-triangle"></i> Flagged as major</p>
            @endif
            @if($d->verification_notes)
              <p class="text-muted mb-0 small"><em>Verifier notes: {{ $d->verification_notes }}</em></p>
            @endif

            @if(auth()->user()->isAnyRole(['lead_technician','admin']) && $d->verification_status === 'pending')
              <form method="POST" action="{{ route('diagnosis.verify', $d->diagnosis_id) }}" class="mt-2 row g-2">
                @csrf
                <div class="col-md-4">
                  <select name="verification_status" class="form-select form-select-sm" required>
                    <option value="verified">✓ Verified</option>
                    <option value="rejected">✗ Rejected</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <input type="text" name="verification_notes" class="form-control form-control-sm" placeholder="Notes...">
                </div>
                <div class="col-md-2">
                  <button class="btn btn-sm btn-primary w-100">Save</button>
                </div>
              </form>
            @endif
          </li>
        @empty
          <li class="list-group-item text-muted text-center py-3">No diagnosis recorded.</li>
        @endforelse
      </ul>
    </div>

    {{-- Material Requests --}}
    <div class="card mb-3">
      <div class="card-header">Material Requests</div>
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead><tr><th>Item</th><th>Qty</th><th>Status</th><th>Approved By</th><th>Released By</th></tr></thead>
          <tbody>
            @forelse($req->materialRequests as $mr)
            <tr>
              <td>{{ $mr->item->item_name ?? '—' }}</td>
              <td>
                {{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}
                @if($mr->qty_released > 0)
                  <div class="small text-muted">
                    Released: {{ $mr->qty_released }} | Used: {{ $mr->qty_used }} | Returned: {{ $mr->qty_returned }}
                  </div>
                @endif
              </td>
              <td><span class="badge bg-{{ $mr->status_badge }}">{{ ucfirst($mr->status) }}</span></td>
              <td class="small">{{ $mr->approver->full_name ?? '—' }}</td>
              <td class="small">{{ $mr->releaser->full_name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-3">No material requests yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Right sidebar actions --}}
  <div class="col-md-4">

    @if(auth()->user()->isAnyRole(['coordinator','lead_technician','admin']) && $req->status === 'new')
      <div class="card mb-3">
        <div class="card-header">Assign Technician</div>
        <div class="card-body">
          <form method="POST" action="{{ route('requests.assign', $req->request_id) }}">
            @csrf
            <select name="technician_id" class="form-select mb-2" required>
              <option value="">Select technician...</option>
              @foreach($technicians as $t)
                <option value="{{ $t->user_id }}">
                  {{ $t->full_name }} — {{ ucfirst($t->technician->specialization ?? 'general') }}
                </option>
              @endforeach
            </select>
            <button class="btn btn-primary w-100">Assign</button>
          </form>
        </div>
      </div>
    @endif

    @if(auth()->user()->isAnyRole(['technician','lead_technician']) && $req->assigned_tech_id === auth()->id())

      <div class="card mb-3">
        <div class="card-header">Inspection / Diagnosis</div>
        <div class="card-body">
          <form method="POST" action="{{ route('diagnosis.store', $req->request_id) }}">
            @csrf
            <textarea name="findings" class="form-control mb-2" rows="3" placeholder="Findings..." required></textarea>
            <textarea name="recommended_action" class="form-control mb-2" rows="2" placeholder="Recommended action..."></textarea>
            <div class="form-check mb-2">
              <input type="checkbox" name="is_major" value="1" class="form-check-input" id="isMajor">
              <label for="isMajor" class="form-check-label small">Flag as major (needs supervisor verification)</label>
            </div>
            <button class="btn btn-warning w-100">Save Diagnosis</button>
          </form>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header">Request Material</div>
        <div class="card-body">
          <form method="POST" action="{{ route('material.store', $req->request_id) }}">
            @csrf
            <select name="item_id" class="form-select mb-2" required>
              <option value="">Select item...</option>
              @foreach($inventoryItems as $i)
                <option value="{{ $i->item_id }}">{{ $i->item_name }} ({{ $i->qty_on_hand }} {{ $i->unit }})</option>
              @endforeach
            </select>
            <input type="number" name="qty_requested" class="form-control mb-2" min="1" placeholder="Quantity" required>
            <input type="text" name="remarks" class="form-control mb-2" placeholder="Remarks (optional)">
            <button class="btn btn-info text-white w-100">Submit Request</button>
          </form>
        </div>
      </div>

      @foreach($req->materialRequests->where('status', 'released') as $mr)
        <div class="card mb-3">
          <div class="card-header">Record Usage: {{ $mr->item->item_name }}</div>
          <div class="card-body">
            <p class="small text-muted mb-2">Released: {{ $mr->qty_released }} {{ $mr->item->unit }}</p>
            <form method="POST" action="{{ route('material.usage', $mr->mat_req_id) }}">
              @csrf
              <div class="row g-2 mb-2">
                <div class="col-6">
                  <label class="form-label small">Qty Used</label>
                  <input type="number" name="qty_used" class="form-control form-control-sm" min="0" max="{{ $mr->qty_released }}" required>
                </div>
                <div class="col-6">
                  <label class="form-label small">Qty Returned</label>
                  <input type="number" name="qty_returned" class="form-control form-control-sm" min="0" max="{{ $mr->qty_released }}" value="0" required>
                </div>
              </div>
              <button class="btn btn-info text-white w-100 btn-sm">Save Usage</button>
            </form>
          </div>
        </div>
      @endforeach

      <div class="card mb-3">
        <div class="card-header">Update Status</div>
        <div class="card-body">
          <form method="POST" action="{{ route('requests.status', $req->request_id) }}" enctype="multipart/form-data">
            @csrf
            <select name="status" class="form-select mb-2" required>
              @foreach(['inspecting','diagnosed','waiting_for_materials','repairing','repaired','closed'] as $s)
                <option value="{{ $s }}" @selected($req->status == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
              @endforeach
            </select>
            <label class="form-label small">Completion Photo (required for "Repaired")</label>
            <input type="file" name="after_repair_photo" class="form-control mb-2" accept="image/*">
            <button class="btn btn-success w-100">Update Status</button>
          </form>
        </div>
      </div>
    @endif

    @if(auth()->user()->isAnyRole(['lead_technician','admin']))
      @foreach($req->materialRequests->where('status', 'pending') as $mr)
        <div class="card mb-3">
          <div class="card-header bg-warning">Approve: {{ $mr->item->item_name }}</div>
          <div class="card-body">
            <p class="small mb-2">Requested: <strong>{{ $mr->qty_requested }} {{ $mr->item->unit }}</strong></p>
            <form method="POST" action="{{ route('material.approve', $mr->mat_req_id) }}">
              @csrf
              <button class="btn btn-success w-100">Approve</button>
            </form>
          </div>
        </div>
      @endforeach
    @endif

    @if(auth()->user()->isAnyRole(['inventory_officer','admin']))
      @foreach($req->materialRequests->where('status', 'approved') as $mr)
        <div class="card mb-3">
          <div class="card-header bg-primary text-white">Release: {{ $mr->item->item_name }}</div>
          <div class="card-body">
            <p class="small mb-2">
              Approved: <strong>{{ $mr->qty_requested }} {{ $mr->item->unit }}</strong><br>
              On hand: <strong>{{ $mr->item->qty_on_hand }}</strong>
            </p>
            <form method="POST" action="{{ route('material.release', $mr->mat_req_id) }}">
              @csrf
              <input type="number" name="qty_released" class="form-control mb-2" min="1" max="{{ min($mr->qty_requested, $mr->item->qty_on_hand) }}" required>
              <button class="btn btn-success w-100">Release Material</button>
            </form>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</div>
@endsection