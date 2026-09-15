<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $table = 'material_requests';
    protected $primaryKey = 'mat_req_id';

    protected $fillable = [
        'request_id', 'item_id', 'requested_by', 'released_by',
        'qty_requested', 'qty_released', 'qty_returned',
        'status', 'remarks', 'released_at',
    ];

    protected $casts = ['released_at' => 'datetime'];

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

    public function releaser()
    {
        return $this->belongsTo(User::class, 'released_by', 'user_id');
    }
}