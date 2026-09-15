<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('username', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
            });
        }

        $users = $query->paginate(20)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::orderBy('dept_name')->get();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'username'   => 'required|string|unique:users,username',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',
            'role' => 'required|in:teacher,coordinator,technician,lead_technician,inventory_officer,head,admin',
            'dept_id'    => 'nullable|exists:departments,dept_id',
            'specialization' => 'nullable|in:electrical,aircon,carpentry,fabrication,plumbing,general',
        ]);

        $specialization = $data['specialization'] ?? 'general';
        unset($data['specialization']);

        $data['password'] = Hash::make($data['password']);
        $data['status']   = 'active';

        $user = User::create($data);

        if (in_array($data['role'], ['technician', 'lead_technician'], true)) {
            Technician::create([
                'user_id'        => $user->user_id,
                'specialization' => $specialization,
            ]);
        }

        AuditLog::record('create_user', "Created user {$user->username} ({$user->role})", $user);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->user_id === auth()->id()) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        AuditLog::record('toggle_user', "Toggled user {$user->username} to {$user->status}", $user);

        return back()->with('success', 'User status updated.');
    }
}