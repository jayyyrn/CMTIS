<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment';
    protected $primaryKey = 'equipment_id';
    protected $fillable = ['asset_no', 'name', 'location', 'dept_id', 'status', 'acquired_date'];

    protected $casts = ['acquired_date' => 'date'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    public function requests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'equipment_id', 'equipment_id');
    }
}