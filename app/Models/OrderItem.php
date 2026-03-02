<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';

   protected $fillable = ['order_id','product_id','quantity','price','note'];
    public function product(){ 
        return $this->belongsTo(Product::class);
     }
    public function order(){ 
        return $this->belongsTo(Order::class);
    }

    public function addons(){
        return $this->hasMany(OrderItemAddon::class, 'order_item_id');
    }

}
