<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'item_id';
    protected $fillable = ['item_name', 'category', 'unit', 'qty_on_hand', 'low_stock_threshold'];

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequest::class, 'item_id', 'item_id');
    }

    public function isLowStock(): bool
    {
        return $this->qty_on_hand <= $this->low_stock_threshold;
    }
}