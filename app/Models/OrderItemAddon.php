<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemAddon extends Model
{
    use HasFactory;

    protected $table = 'order_item_addons';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'order_item_id',
        'product_addon_id'
    ];

    public function item()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function addon()
    {
        return $this->belongsTo(ProductAddon::class, 'product_addon_id');
    }
}
