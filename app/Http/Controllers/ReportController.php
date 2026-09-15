<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\MaintenanceRequest;
use App\Models\MaterialRequest;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function maintenance(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $requests = MaintenanceRequest::with(['teacher', 'technician', 'department'])
            ->whereBetween('date_reported', [$from, $to])
            ->get();

        $byStatus = $requests->groupBy('status')->map->count();
        $byWorkType = $requests->groupBy('work_type')->map->count();
        $byDept = $requests->groupBy(fn ($r) => $r->department->dept_name ?? 'Unassigned')->map->count();

        return view('reports.maintenance', compact('requests', 'byStatus', 'byWorkType', 'byDept', 'from', 'to'));
    }

    public function inventory(Request $request)
    {
        $items = Inventory::withSum('materialRequests as total_released', 'qty_released')
            ->withSum('materialRequests as total_used', 'qty_used')
            ->withSum('materialRequests as total_returned', 'qty_returned')
            ->get();

        $recentTransactions = StockTransaction::with('item', 'handler')
            ->latest()->take(20)->get();

        return view('reports.inventory', compact('items', 'recentTransactions'));
    }

    public function auditTrail()
    {
        $logs = AuditLog::with('user')->latest()->paginate(30);
        return view('reports.audit', compact('logs'));
    }
}