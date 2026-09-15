<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\MaintenanceRequest;
use App\Models\MaterialRequest;
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

        return back()->with('success', 'Material requested.');
    }

    public function returnMaterial(Request $request, $matReqId)
    {
        $data = $request->validate(['qty_returned' => 'required|integer|min:1']);

        $matReq = MaterialRequest::with('item')->findOrFail($matReqId);

        DB::transaction(function () use ($matReq, $data) {
            $matReq->increment('qty_returned', $data['qty_returned']);
            $matReq->update(['status' => 'returned']);
            $matReq->item->increment('qty_on_hand', $data['qty_returned']);
        });

        AuditLog::record(
            'return_material',
            "Returned {$data['qty_returned']} of {$matReq->item->item_name}",
            $matReq
        );

        return back()->with('success', 'Material returned.');
    }
}