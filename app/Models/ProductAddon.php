<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model
{
    protected $table = 'product_addons';
    protected $primaryKey = 'id';
    protected $fillable = ['product_id','name','price'];
    public function product(){ 
        return $this->belongsTo(Product::class); 
    }

   
}

