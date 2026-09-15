@extends('layouts.app')
@section('title', "Request {$req->reference_no}")
@section('content')
<div class="row g-3">
  <div class="col-md-8">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>{{ $req->reference_no }}</strong>
        <span class="badge bg-{{ $req->status_badge }} text-capitalize">{{ str_replace('_',' ', $req->status) }}</span>
      </div>
      <div class="card-body">
        <p><strong>Teacher:</strong> {{ $req->teacher->full_name ?? '—' }}</p>
        <p><strong>Department:</strong> {{ $req->department->dept_name ?? '—' }}</p>
        <p><strong>Location:</strong> {{ $req->location }}</p>
        <p><strong>Equipment:</strong> {{ $req->equipment->name ?? '—' }}</p>
        <p><strong>Priority:</strong> <span class="text-capitalize">{{ $req->priority }}</span></p>
        <p><strong>Reported:</strong> {{ $req->date_reported->format('M d, Y') }}</p>
        <p><strong>Description:</strong><br>{{ $req->problem_description }}</p>
        @if($req->photo_evidence)
          <img src="{{ asset('storage/'.$req->photo_evidence) }}" class="img-fluid rounded" style="max-width: 400px;">
        @endif
        @if($req->after_repair_photo)
          <hr><p><strong>After Repair:</strong></p>
          <img src="{{ asset('storage/'.$req->after_repair_photo) }}" class="img-fluid rounded" style="max-width: 400px;">
        @endif
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header">Diagnoses</div>
      <ul class="list-group list-group-flush">
        @forelse($req->diagnoses as $d)
          <li class="list-group-item">
            <div class="d-flex justify-content-between">
              <strong>{{ $d->technician->full_name ?? '—' }}</strong>
              <span class="badge bg-{{ $d->verification_status==='verified' ? 'success' : ($d->verification_status==='rejected' ? 'danger' : 'warning') }}">
                {{ ucfirst($d->verification_status) }}
              </span>
            </div>
            <p class="mb-1 mt-2"><strong>Findings:</strong> {{ $d->findings }}</p>
            <p class="mb-1"><strong>Recommended:</strong> {{ $d->recommended_action ?? '—' }}</p>
            @if($d->is_major)
              <p class="text-danger mb-1"><i class="bi bi-exclamation-triangle"></i> Major diagnosis</p>
            @endif
            @if($d->verification_notes)
              <p class="text-muted mb-0"><em>Verifier notes: {{ $d->verification_notes }}</em></p>
            @endif

            @if(auth()->user()->isAnyRole(['lead_technician','admin']) && $d->verification_status === 'pending')
              <form method="POST" action="{{ route('diagnosis.verify', $d->diagnosis_id) }}" class="mt-2 row g-2">
                @csrf
                <div class="col-md-4">
                  <select name="verification_status" class="form-select form-select-sm" required>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
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
          <li class="list-group-item text-muted">No diagnosis yet.</li>
        @endforelse
      </ul>
    </div>

    <div class="card mb-3">
      <div class="card-header">Material Requests</div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr><th>Item</th><th>Qty</th><th>Status</th><th>Released By</th></tr></thead>
          <tbody>
            @forelse($req->materialRequests as $mr)
            <tr>
              <td>{{ $mr->item->item_name ?? '—' }}</td>
              <td>
                {{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}
                (rel: {{ $mr->qty_released }}, ret: {{ $mr->qty_returned }})
              </td>
              <td><span class="badge bg-secondary text-capitalize">{{ $mr->status }}</span></td>
              <td>{{ $mr->releaser->full_name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">No material requests.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    @if(auth()->user()->isAnyRole(['coordinator','lead_technician','admin']) && $req->status === 'new')
      <div class="card mb-3">
        <div class="card-header">Assign Technician</div>
        <div class="card-body">
          <form method="POST" action="{{ route('requests.assign', $req->request_id) }}">
            @csrf
            <select name="technician_id" class="form-select mb-2" required>
              <option value="">Select...</option>
              @foreach($technicians as $t)
                <option value="{{ $t->user_id }}">{{ $t->full_name }}</option>
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
            <div class="mb-2">
              <textarea name="findings" class="form-control" rows="3" placeholder="Findings..." required></textarea>
            </div>
            <div class="mb-2">
              <textarea name="recommended_action" class="form-control" rows="2" placeholder="Recommended action..."></textarea>
            </div>
            <div class="form-check mb-2">
              <input type="checkbox" name="is_major" value="1" class="form-check-input" id="isMajor">
              <label for="isMajor" class="form-check-label">Flag as major (needs verification)</label>
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
            <button class="btn btn-info w-100 text-white">Submit Request</button>
          </form>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header">Update Status</div>
        <div class="card-body">
          <form method="POST" action="{{ route('requests.status', $req->request_id) }}" enctype="multipart/form-data">
            @csrf
            <select name="status" class="form-select mb-2" required>
              @foreach(['inspecting','diagnosed','repairing','repaired','closed'] as $s)
                <option value="{{ $s }}" @selected($req->status == $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
              @endforeach
            </select>
            <input type="file" name="after_repair_photo" class="form-control mb-2" accept="image/*">
            <button class="btn btn-success w-100">Update</button>
          </form>
        </div>
      </div>
    @endif

    @if(auth()->user()->isAnyRole(['inventory_officer','admin']))
      @foreach($req->materialRequests->where('status', 'pending') as $mr)
        <div class="card mb-3">
          <div class="card-header">Release: {{ $mr->item->item_name ?? '—' }}</div>
          <div class="card-body">
            <p class="small">
              Requested: {{ $mr->qty_requested }} {{ $mr->item->unit ?? '' }}<br>
              On hand: {{ $mr->item->qty_on_hand ?? 0 }}
            </p>
            <form method="POST" action="{{ route('material.release', $mr->mat_req_id) }}">
              @csrf
              <input type="number" name="qty_released" class="form-control mb-2" min="1" max="{{ $mr->qty_requested }}" required>
              <button class="btn btn-success w-100">RELEASE MATERIAL</button>
            </form>
          </div>
        </div>
      @endforeach
    @endif
  </div>
</div>
@endsection