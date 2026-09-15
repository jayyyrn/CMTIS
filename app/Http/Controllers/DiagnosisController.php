<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AuditLog;
use App\Models\Diagnosis;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiagnosisController extends Controller
{
    public function store(Request $request, $requestId)
    {
        $data = $request->validate([
            'findings'           => 'required|string',
            'recommended_action' => 'nullable|string',
            'is_major'           => 'nullable|boolean',
        ]);

        $req = MaintenanceRequest::findOrFail($requestId);
        $isMajor = $request->boolean('is_major');

        $diagnosis = Diagnosis::create([
            'request_id'          => $req->request_id,
            'tech_id'             => Auth::id(),
            'findings'            => $data['findings'],
            'recommended_action'  => $data['recommended_action'] ?? null,
            'is_major'            => $isMajor,
            'verification_status' => $isMajor ? 'pending' : 'verified',
        ]);

        $req->update([
            'status' => $isMajor ? 'pending_verification' : 'diagnosed',
        ]);

        AuditLog::record('create_diagnosis', "Diagnosis for {$req->reference_no}", $diagnosis);

        if ($isMajor) {
            $supervisors = User::where('role', 'lead_technician')->get();
            foreach ($supervisors as $s) {
                AppNotification::notify(
                    $s->user_id,
                    'Diagnosis Needs Verification',
                    "Diagnosis for {$req->reference_no} needs your verification.",
                    route('requests.show', $req->request_id)
                );
            }
        }

        return back()->with('success', 'Diagnosis recorded.');
    }

    public function verify(Request $request, $diagnosisId)
    {
        $data = $request->validate([
            'verification_status' => 'required|in:verified,rejected',
            'verification_notes'  => 'nullable|string',
        ]);

        $diagnosis = Diagnosis::findOrFail($diagnosisId);

        $diagnosis->update([
            'verification_status' => $data['verification_status'],
            'verified_by'         => Auth::id(),
            'verification_notes'  => $data['verification_notes'] ?? null,
        ]);

        $req = $diagnosis->request;

        if ($data['verification_status'] === 'verified') {
            $req->update(['status' => 'verified']);
        }

        AuditLog::record(
            'verify_diagnosis',
            "Diagnosis {$diagnosisId} - {$data['verification_status']}",
            $diagnosis
        );

        AppNotification::notify(
            $diagnosis->tech_id,
            'Diagnosis Verification Result',
            "Your diagnosis for {$req->reference_no} was {$data['verification_status']}.",
            route('requests.show', $req->request_id)
        );

        return back()->with('success', 'Verification saved.');
    }
}