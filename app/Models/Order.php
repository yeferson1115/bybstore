<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $fillable = [
        'table_id', 'type', 'total', 'status', 'customer_name', 'customer_phone', 
        'customer_address', 'note', 'customer_id', 'shipping_cost', 'paid', 
        'payment_method', 'cancelled', 'cancelled_at', 'cancelled_reason'
    ];

    protected $casts = [
        'cancelled' => 'boolean',
        'paid' => 'boolean',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function table(){ 
        return $this->belongsTo(Table::class);
    }
    
    public function items(){ 
        return $this->hasMany(OrderItem::class); 
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Scope para filtrar órdenes activas (no anuladas)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('cancelled', false);
    }

    /**
     * Scope para filtrar órdenes anuladas
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('cancelled', true);
    }

    /**
     * Scope para filtrar órdenes no canceladas (alias de active)
     */
    public function scopeNotCancelled(Builder $query): Builder
    {
        return $query->where('cancelled', false);
    }
}