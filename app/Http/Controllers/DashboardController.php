<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Diagnosis;
use App\Models\Inventory;
use App\Models\MaintenanceRequest;
use App\Models\MaterialRequest;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = AppNotification::where('user_id', $user->user_id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        return match ($user->role) {
            'teacher'           => $this->teacherDashboard($user, $notifications),
            'coordinator'       => $this->coordinatorDashboard($user, $notifications),
            'technician'        => $this->technicianDashboard($user, $notifications),
            'lead_technician'   => $this->supervisorDashboard($user, $notifications),
            'inventory_officer' => $this->inventoryDashboard($user, $notifications),
            'admin'             => $this->adminDashboard($user, $notifications),
            default             => abort(403),
        };
    }

    private function teacherDashboard($user, $notifications)
    {
        $myRequests = MaintenanceRequest::where('teacher_id', $user->user_id)
            ->latest()->take(10)->get();

        $stats = [
            'total'   => MaintenanceRequest::where('teacher_id', $user->user_id)->count(),
            'pending' => MaintenanceRequest::where('teacher_id', $user->user_id)
                ->whereNotIn('status', ['closed', 'rejected'])->count(),
        ];

        return view('dashboards.teacher', compact('myRequests', 'stats', 'notifications'));
    }

    private function coordinatorDashboard($user, $notifications)
    {
        $deptRequests = MaintenanceRequest::where('dept_id', $user->dept_id)
            ->latest()->get();

        $stats = [
            'total'     => $deptRequests->count(),
            'new'       => $deptRequests->where('status', 'new')->count(),
            'ongoing'   => $deptRequests->whereIn('status', ['assigned', 'inspecting', 'diagnosed', 'repairing'])->count(),
            'completed' => $deptRequests->whereIn('status', ['repaired', 'closed'])->count(),
        ];

        return view('dashboards.coordinator', compact('deptRequests', 'stats', 'notifications'));
    }

    private function technicianDashboard($user, $notifications)
    {
        $myTasks = MaintenanceRequest::where('assigned_tech_id', $user->user_id)
            ->latest()->get();

        $stats = [
            'assigned'  => $myTasks->whereIn('status', ['assigned', 'inspecting'])->count(),
            'ongoing'   => $myTasks->whereIn('status', ['diagnosed', 'repairing'])->count(),
            'completed' => $myTasks->whereIn('status', ['repaired', 'closed'])->count(),
        ];

        return view('dashboards.technician', compact('myTasks', 'stats', 'notifications'));
    }

    private function supervisorDashboard($user, $notifications)
    {
        $pendingVerify = Diagnosis::where('verification_status', 'pending')
            ->where('is_major', true)
            ->with('request', 'technician')
            ->get();

        $allRequests = MaintenanceRequest::latest()->take(20)->get();

        $stats = [
            'new'            => MaintenanceRequest::where('status', 'new')->count(),
            'assigned'       => MaintenanceRequest::where('status', 'assigned')->count(),
            'pending_verify' => $pendingVerify->count(),
            'completed'      => MaintenanceRequest::whereIn('status', ['repaired', 'closed'])->count(),
        ];

        return view('dashboards.supervisor', compact('pendingVerify', 'allRequests', 'stats', 'notifications'));
    }

    private function inventoryDashboard($user, $notifications)
    {
        $items = Inventory::all();
        $pendingRequests = MaterialRequest::where('status', 'pending')
            ->with('item', 'requester', 'request')->get();
        $lowStock = $items->filter(fn ($i) => $i->isLowStock());

        return view('dashboards.inventory', compact('items', 'pendingRequests', 'lowStock', 'notifications'));
    }

    private function adminDashboard($user, $notifications)
    {
        $stats = [
            'users'           => User::count(),
            'requests'        => MaintenanceRequest::count(),
            'open'            => MaintenanceRequest::whereNotIn('status', ['closed', 'rejected'])->count(),
            'inventory_items' => Inventory::count(),
        ];

        $recentLogs = AuditLog::with('user')->latest()->take(10)->get();

        return view('dashboards.admin', compact('stats', 'recentLogs', 'notifications'));
    }
}