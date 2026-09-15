<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Equipment;
use App\Models\Inventory;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['teacher', 'technician', 'equipment', 'department']);

        if (Auth::user()->role === 'teacher') {
            $query->where('teacher_id', Auth::id());
        } elseif (Auth::user()->role === 'coordinator') {
            $query->where('dept_id', Auth::user()->dept_id);
        } elseif (Auth::user()->role === 'technician') {
            $query->where('assigned_tech_id', Auth::id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('from')) {
            $query->whereDate('date_reported', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date_reported', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference_no', 'like', "%$s%")
                  ->orWhere('location', 'like', "%$s%")
                  ->orWhere('problem_description', 'like', "%$s%");
            });
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $equipment = Equipment::orderBy('name')->get();
        return view('requests.create', compact('equipment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location'            => 'required|string|max:255',
            'equipment_id'        => 'nullable|exists:equipment,equipment_id',
            'problem_description' => 'required|string',
            'work_type'           => 'required|in:electrical,aircon,carpentry,fabrication,plumbing,general,other',
            'priority'            => 'required|in:low,medium,high,urgent',
            'photo_evidence'      => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('photo_evidence')) {
            $data['photo_evidence'] = $request->file('photo_evidence')->store('evidence', 'public');
        }

        $data['reference_no']  = MaintenanceRequest::generateReferenceNo();
        $data['teacher_id']    = Auth::id();
        $data['dept_id']       = Auth::user()->dept_id;
        $data['date_reported'] = now();
        $data['status']        = 'new';

        $req = MaintenanceRequest::create($data);

        AuditLog::record('create_request', "Created request {$req->reference_no}", $req);

        $coordinators = User::where('role', 'coordinator')
            ->where('dept_id', Auth::user()->dept_id)->get();

        foreach ($coordinators as $c) {
            AppNotification::notify(
                $c->user_id,
                'New Maintenance Request',
                "New {$req->work_type} request {$req->reference_no} submitted.",
                route('requests.show', $req->request_id)
            );
        }

        return redirect()->route('requests.show', $req->request_id)
            ->with('success', 'Request submitted successfully.');
    }

    public function show($id)
    {
        $req = MaintenanceRequest::with([
            'teacher', 'technician', 'equipment', 'department',
            'diagnoses.technician', 'diagnoses.verifier',
            'materialRequests.item', 'materialRequests.requester',
            'materialRequests.approver', 'materialRequests.releaser',
        ])->findOrFail($id);

        $technicians = User::whereIn('role', ['technician', 'lead_technician'])
            ->where('status', 'active')
            ->with('technician')
            ->get();

        $inventoryItems = Inventory::orderBy('item_name')->get();

        return view('requests.show', compact('req', 'technicians', 'inventoryItems'));
    }

    public function assign(Request $request, $id)
    {
        $data = $request->validate([
            'technician_id' => 'required|exists:users,user_id',
        ]);

        $req = MaintenanceRequest::findOrFail($id);
        $tech = User::with('technician')->find($data['technician_id']);

        $specialization = $tech->technician->specialization ?? 'general';
        if ($specialization !== 'general' && $req->work_type !== $specialization) {
            session()->flash('warning', "Note: {$tech->full_name}'s specialization ({$specialization}) differs from the request type ({$req->work_type}).");
        }

        $req->update([
            'assigned_tech_id' => $data['technician_id'],
            'status'           => 'assigned',
            'date_assigned'    => now(),
        ]);

        AuditLog::record('assign_request', "Assigned {$req->reference_no} to {$tech->full_name}", $req);

        AppNotification::notify(
            (int) $data['technician_id'],
            'New Task Assigned',
            "You have been assigned to {$req->reference_no} ({$req->work_type}).",
            route('requests.show', $req->request_id)
        );

        return back()->with('success', 'Technician assigned.');
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:new,reviewed,assigned,inspecting,diagnosed,waiting_for_materials,pending_verification,verified,repairing,repaired,closed,rejected',
            'after_repair_photo' => 'nullable|image|max:5120',
        ]);

        $req = MaintenanceRequest::findOrFail($id);

        if ($data['status'] === 'repaired'
            && !$request->hasFile('after_repair_photo')
            && !$req->after_repair_photo) {
            return back()->withErrors(['after_repair_photo' => 'Completion photo is required before marking as repaired.']);
        }

        $old = $req->status;
        $req->status = $data['status'];

        if ($request->hasFile('after_repair_photo')) {
            $req->after_repair_photo = $request->file('after_repair_photo')->store('repairs', 'public');
        }

        if ($data['status'] === 'closed') {
            $req->date_completed = now();
        }

        $req->save();

        AuditLog::record('update_status', "Request {$req->reference_no}: $old -> {$data['status']}", $req);

        AppNotification::notify(
            $req->teacher_id,
            'Request Updated',
            "Your request {$req->reference_no} status: {$data['status']}.",
            route('requests.show', $req->request_id)
        );

        return back()->with('success', 'Status updated.');
    }
}