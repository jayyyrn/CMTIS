<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'item_id', 'type', 'quantity', 'supplier',
        'reference_no', 'transaction_date', 'handled_by', 'remarks',
    ];

    protected $casts = ['transaction_date' => 'date'];

    public function item()
    {
        return $this->belongsTo(Inventory::class, 'item_id', 'item_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by', 'user_id');
    }

    public function getTypeBadgeAttribute(): string
    {
        return [
            'in' => 'success',
            'out' => 'warning',
            'return' => 'info',
            'adjustment' => 'secondary',
        ][$this->type] ?? 'secondary';
    }
}