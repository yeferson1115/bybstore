<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id','name','sku','price','note','single_option','image','active'];

    public function category(){ 
        return $this->belongsTo(Category::class); 
    }

    public function addons(){ 
        return $this->hasMany(ProductAddon::class); 
    }

   
}

