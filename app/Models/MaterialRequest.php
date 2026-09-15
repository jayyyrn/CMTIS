<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $table = 'material_requests';
    protected $primaryKey = 'mat_req_id';

    protected $fillable = [
    'request_id', 'item_id', 'requested_by', 'approved_by', 'released_by',
    'endorsed_to_lgu_by',
    'qty_requested', 'qty_released', 'qty_used', 'qty_returned',
    'status', 'remarks', 'approved_at', 'released_at',
    'endorsed_to_lgu_at',
    'epr_no', 'pr_no', 'po_no', 'lgu_notes',
];

protected $casts = [
    'approved_at' => 'datetime',
    'released_at' => 'datetime',
    'endorsed_to_lgu_at' => 'datetime',
];

    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'request_id', 'request_id');
    }

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function releaser()
    {
        return $this->belongsTo(User::class, 'released_by', 'user_id');
    }

    public function lguEndorser()
{
    return $this->belongsTo(User::class, 'endorsed_to_lgu_by', 'user_id');
}

public function getStatusBadgeAttribute(): string
{
    return [
        'pending' => 'warning',
        'endorsed_to_head' => 'info',
        'endorsed_to_lgu' => 'primary',
        'approved' => 'success',
        'released' => 'primary',
        'rejected' => 'danger',
        'returned' => 'secondary',
    ][$this->status] ?? 'secondary';
}
}