<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'item_id';
    protected $fillable = [
        'item_name', 'category', 'unit',
        'qty_on_hand', 'low_stock_threshold',
    ];

    public function materialRequests()
    {
        return $this->hasMany(MaterialRequest::class, 'item_id', 'item_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'item_id', 'item_id');
    }

    public function isLowStock(): bool
    {
        if ($this->low_stock_threshold === null) {
            return false;
        }
        return $this->qty_on_hand <= $this->low_stock_threshold;
    }

    public function getCategoryLabelAttribute(): string
    {
        return ucfirst($this->category);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->qty_on_hand === 0) return 'out';
        if ($this->isLowStock()) return 'low';
        return 'ok';
    }
}