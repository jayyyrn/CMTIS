<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\MaterialRequest;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('low_stock')) {
            $query->whereNotNull('low_stock_threshold')
                  ->whereColumn('qty_on_hand', '<=', 'low_stock_threshold');
        }

        $items = $query->orderBy('item_name')->paginate(20)->withQueryString();

        return view('inventory.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name'           => 'required|string|max:255',
            'category'            => 'required|in:electrical,carpentry,plumbing,fabrication,consumables,tools,other',
            'unit'                => 'required|string|max:20',
            'qty_on_hand'         => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $item = Inventory::create($data);

        AuditLog::record('create_item', "Added item: {$item->item_name}", $item);

        return back()->with('success', 'Item added.');
    }

    public function stockIn(Request $request, $itemId)
    {
        $data = $request->validate([
            'qty'              => 'required|integer|min:1',
            'supplier'         => 'nullable|string|max:255',
            'reference_no'     => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'remarks'          => 'nullable|string',
        ]);

        $item = Inventory::findOrFail($itemId);

        DB::transaction(function () use ($item, $data) {
            $item->increment('qty_on_hand', $data['qty']);

            StockTransaction::create([
                'item_id'          => $item->item_id,
                'type'             => 'in',
                'quantity'         => $data['qty'],
                'supplier'         => $data['supplier'] ?? null,
                'reference_no'     => $data['reference_no'] ?? null,
                'transaction_date' => $data['transaction_date'],
                'handled_by'       => Auth::id(),
                'remarks'          => $data['remarks'] ?? null,
            ]);
        });

        AuditLog::record('stock_in', "Stock-in {$data['qty']} {$item->unit} of {$item->item_name}", $item);

        return back()->with('success', 'Stock added and logged.');
    }

    public function approve(Request $request, $matReqId)
{
    $matReq = MaterialRequest::with('item')->findOrFail($matReqId);

    if ($matReq->status !== 'pending') {
        return back()->withErrors(['status' => 'Only pending requests can be endorsed.']);
    }

    // Step 1: Head endorses to LGU
    $matReq->update([
        'status'               => 'endorsed_to_lgu',
        'endorsed_to_lgu_by'   => Auth::id(),
        'endorsed_to_lgu_at'   => now(),
    ]);

    AuditLog::record(
        'endorse_to_lgu',
        "Endorsed material request #{$matReqId} to LGU",
        $matReq
    );

    AppNotification::notify(
        $matReq->requested_by,
        'Material Request Endorsed to LGU',
        "Your request for {$matReq->item->item_name} was endorsed to LGU for approval.",
        route('requests.show', $matReq->request_id)
    );

    // Notify inventory officer so they can track it
    $officers = User::where('role', 'inventory_officer')->get();
    foreach ($officers as $o) {
        AppNotification::notify(
            $o->user_id,
            'Material Endorsed to LGU',
            "Request for {$matReq->item->item_name} is now with LGU.",
            route('requests.show', $matReq->request_id)
        );
    }

    return back()->with('success', 'Endorsed to LGU. Awaiting LGU approval and EPR/PR.');
}

public function markLguApproved(Request $request, $matReqId)
{
    $data = $request->validate([
        'epr_no'    => 'nullable|string|max:100',
        'pr_no'     => 'nullable|string|max:100',
        'po_no'     => 'nullable|string|max:100',
        'lgu_notes' => 'nullable|string',
    ]);

    $matReq = MaterialRequest::with('item')->findOrFail($matReqId);

    if ($matReq->status !== 'endorsed_to_lgu') {
        return back()->withErrors(['status' => 'Only endorsed requests can be marked as LGU-approved.']);
    }

    $matReq->update([
        'status'      => 'approved',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
        'epr_no'      => $data['epr_no'] ?? null,
        'pr_no'       => $data['pr_no'] ?? null,
        'po_no'       => $data['po_no'] ?? null,
        'lgu_notes'   => $data['lgu_notes'] ?? null,
    ]);

    AuditLog::record(
        'lgu_approve',
        "LGU approved material request #{$matReqId} (EPR: {$data['epr_no']})",
        $matReq
    );

    AppNotification::notify(
        $matReq->requested_by,
        'Material Approved by LGU',
        "Your request for {$matReq->item->item_name} was approved. Awaiting release.",
        route('requests.show', $matReq->request_id)
    );

    return back()->with('success', 'LGU approval recorded.');
}

}