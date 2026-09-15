<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('department');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('asset_no', 'like', "%$s%")
                  ->orWhere('location', 'like', "%$s%");
            });
        }

        $equipment = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('equipment.index', compact('equipment'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_no'      => 'required|string|unique:equipment,asset_no',
            'name'          => 'required|string',
            'location'      => 'required|string',
            'dept_id'       => 'nullable|exists:departments,dept_id',
            'status'        => 'required|in:working,under_repair,broken,retired',
            'acquired_date' => 'nullable|date',
        ]);

        $eq = Equipment::create($data);

        AuditLog::record('create_equipment', "Added equipment {$eq->asset_no}", $eq);

        return back()->with('success', 'Equipment added.');
    }

    public function history($id)
    {
        $equipment = Equipment::findOrFail($id);
        $requests = $equipment->requests()
            ->with('technician', 'teacher')
            ->latest()
            ->get();

        return view('equipment.history', compact('equipment', 'requests'));
    }
}