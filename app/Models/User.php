<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'dept_id', 'role', 'first_name', 'last_name',
        'username', 'email', 'password', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'status'   => 'string',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'dept_id');
    }

    public function technician()
    {
        return $this->hasOne(Technician::class, 'user_id', 'user_id');
    }

    public function requests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'teacher_id', 'user_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(MaintenanceRequest::class, 'assigned_tech_id', 'user_id');
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class, 'user_id', 'user_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}