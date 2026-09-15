<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    protected $table = 'diagnoses';
    protected $primaryKey = 'diagnosis_id';

    protected $fillable = [
        'request_id', 'tech_id', 'findings', 'recommended_action',
        'is_major', 'verification_status', 'verified_by', 'verification_notes',
    ];

    protected $casts = ['is_major' => 'boolean'];

    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id', 'request_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'tech_id', 'user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'user_id');
    }

    public function getVerificationBadgeAttribute(): string
    {
        return [
            'pending' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
        ][$this->verification_status] ?? 'secondary';
    }
}