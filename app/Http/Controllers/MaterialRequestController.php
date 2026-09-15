<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\MaintenanceRequest;
use App\Models\MaterialRequest;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialRequestController extends Controller
{
    public function store(Request $request, $requestId)
    {
        $data = $request->validate([
            'item_id'       => 'required|exists:inventory,item_id',
            'qty_requested' => 'required|integer|min:1',
            'remarks'       => 'nullable|string',
        ]);

        $req = MaintenanceRequest::findOrFail($requestId);

        MaterialRequest::create([
            'request_id'    => $req->request_id,
            'item_id'       => $data['item_id'],
            'requested_by'  => Auth::id(),
            'qty_requested' => $data['qty_requested'],
            'remarks'       => $data['remarks'] ?? null,
            'status'        => 'pending',
        ]);

        $req->update(['status' => 'waiting_for_materials']);

        AuditLog::record('material_request', "Material requested for {$req->reference_no}");

        $officers = User::where('role', 'inventory_officer')->get();
        foreach ($officers as $o) {
            AppNotification::notify(
                $o->user_id,
                'New Material Request',
                "Material requested for {$req->reference_no}.",
                route('requests.show', $req->request_id)
            );
        }

        $supervisors = User::where('role', 'lead_technician')->get();
        foreach ($supervisors as $s) {
            AppNotification::notify(
                $s->user_id,
                'Material Request Needs Approval',
                "Material requested for {$req->reference_no}.",
                route('requests.show', $req->request_id)
            );
        }

        return back()->with('success', 'Material requested. Awaiting approval.');
    }

    public function recordUsage(Request $request, $matReqId)
    {
        $data = $request->validate([
            'qty_used'     => 'required|integer|min:0',
            'qty_returned' => 'required|integer|min:0',
        ]);

        $matReq = MaterialRequest::with('item', 'request')->findOrFail($matReqId);

        if ($matReq->status !== 'released') {
            return back()->withErrors(['qty_used' => 'Only released materials can have usage recorded.']);
        }

        if ($data['qty_used'] + $data['qty_returned'] > $matReq->qty_released) {
            return back()->withErrors(['qty_used' => 'Used + Returned cannot exceed Qty Released.']);
        }

        DB::transaction(function () use ($matReq, $data) {
            $matReq->update([
                'qty_used'     => $data['qty_used'],
                'qty_returned' => $data['qty_returned'],
                'status'       => 'returned',
            ]);

            if ($data['qty_returned'] > 0) {
                $matReq->item->increment('qty_on_hand', $data['qty_returned']);

                StockTransaction::create([
                    'item_id'          => $matReq->item_id,
                    'type'             => 'return',
                    'quantity'         => $data['qty_returned'],
                    'reference_no'     => $matReq->request->reference_no ?? null,
                    'transaction_date' => now()->toDateString(),
                    'handled_by'       => Auth::id(),
                    'remarks'          => "Returned from request #{$matReq->request_id}",
                ]);
            }
        });

        AuditLog::record(
            'material_usage',
            "Recorded: {$data['qty_used']} used, {$data['qty_returned']} returned for {$matReq->item->item_name}",
            $matReq
        );

        return back()->with('success', 'Material usage recorded.');
    }
}