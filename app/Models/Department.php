<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'dept_id';
    protected $fillable = ['dept_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'dept_id', 'dept_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'dept_id', 'dept_id');
    }

    public function requests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'dept_id', 'dept_id');
    }
}