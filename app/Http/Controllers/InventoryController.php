<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\MaterialRequest;
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

        $items = $query->orderBy('item_name')->paginate(20)->withQueryString();

        return view('inventory.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name'           => 'required|string',
            'category'            => 'nullable|string',
            'unit'                => 'required|string',
            'qty_on_hand'         => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
        ]);

        $item = Inventory::create($data);

        AuditLog::record('create_item', "Added item: {$item->item_name}", $item);

        return back()->with('success', 'Item added.');
    }

    public function stockIn(Request $request, $itemId)
    {
        $data = $request->validate(['qty' => 'required|integer|min:1']);
        $item = Inventory::findOrFail($itemId);
        $item->increment('qty_on_hand', $data['qty']);

        AuditLog::record('stock_in', "Stock-in {$data['qty']} {$item->unit} of {$item->item_name}", $item);

        return back()->with('success', 'Stock added.');
    }

    public function releaseMaterial(Request $request, $matReqId)
    {
        $data = $request->validate([
            'qty_released' => 'required|integer|min:1',
        ]);

        $matReq = MaterialRequest::with('item')->findOrFail($matReqId);

        if ($matReq->item->qty_on_hand < $data['qty_released']) {
            return back()->withErrors(['qty_released' => 'Not enough stock available.']);
        }

        DB::transaction(function () use ($matReq, $data) {
            $matReq->update([
                'qty_released' => $data['qty_released'],
                'released_by'  => Auth::id(),
                'status'       => 'released',
                'released_at'  => now(),
            ]);

            $matReq->item->decrement('qty_on_hand', $data['qty_released']);
        });

        AuditLog::record(
            'release_material',
            "Released {$data['qty_released']} of {$matReq->item->item_name}",
            $matReq
        );

        AppNotification::notify(
            $matReq->requested_by,
            'Material Released',
            "Your request for {$matReq->item->item_name} was released.",
            route('requests.show', $matReq->request_id)
        );

        if ($matReq->item->fresh()->isLowStock()) {
            $officers = User::where('role', 'inventory_officer')->get();
            foreach ($officers as $o) {
                AppNotification::notify(
                    $o->user_id,
                    'Low Stock Alert',
                    "{$matReq->item->item_name} is running low."
                );
            }
        }

        return back()->with('success', 'Material released.');
    }
}