<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Inventory;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function maintenance(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());

        $requests = MaintenanceRequest::with(['teacher', 'technician', 'department'])
            ->whereBetween('date_reported', [$from, $to])
            ->get();

        $byStatus = $requests->groupBy('status')->map->count();
        $byDept = $requests->groupBy(fn ($r) => $r->department?->dept_name ?? 'Unassigned')->map->count();

        return view('reports.maintenance', compact('requests', 'byStatus', 'byDept', 'from', 'to'));
    }

    public function inventory()
    {
        $items = Inventory::withSum('materialRequests as total_released', 'qty_released')->get();
        return view('reports.inventory', compact('items'));
    }

    public function auditTrail()
    {
        $logs = AuditLog::with('user')->latest()->paginate(30);
        return view('reports.audit', compact('logs'));
    }
}