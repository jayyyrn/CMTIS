<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $table = 'maintenance_requests';
    protected $primaryKey = 'request_id';

    protected $fillable = [
        'reference_no', 'teacher_id', 'equipment_id', 'assigned_tech_id',
        'dept_id', 'location', 'problem_description', 'work_type',
        'photo_evidence', 'after_repair_photo', 'priority', 'status',
        'date_reported', 'date_assigned', 'date_completed',
    ];

    protected $casts = [
        'date_reported'  => 'date',
        'date_assigned'  => 'datetime',
        'date_completed' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_tech_id', 'user_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class, 'request_id', 'request_id');
    }

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequest::class, 'request_id', 'request_id');
    }

    public static function generateReferenceNo(): string
    {
        $last = self::orderByDesc('request_id')->first();
        $num = $last ? $last->request_id + 1 : 1;
        return 'REQ-' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'new' => 'primary',
            'reviewed' => 'info',
            'assigned' => 'primary',
            'inspecting' => 'warning',
            'diagnosed' => 'warning',
            'waiting_for_materials' => 'dark',
            'pending_verification' => 'danger',
            'verified' => 'info',
            'repairing' => 'primary',
            'repaired' => 'success',
            'closed' => 'success',
            'rejected' => 'secondary',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityBadgeAttribute(): string
    {
        return [
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
        ][$this->priority] ?? 'secondary';
    }

    public function getWorkTypeIconAttribute(): string
    {
        return [
            'electrical' => 'bi-lightning-charge',
            'aircon' => 'bi-snow',
            'carpentry' => 'bi-hammer',
            'fabrication' => 'bi-tools',
            'plumbing' => 'bi-droplet',
            'general' => 'bi-gear',
            'other' => 'bi-question-circle',
        ][$this->work_type] ?? 'bi-gear';
    }

    public function isWaitingForMaterials(): bool
    {
        return $this->status === 'waiting_for_materials';
    }
}