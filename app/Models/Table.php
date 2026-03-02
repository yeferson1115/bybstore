<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $table = 'tables';
    protected $primaryKey = 'id';
    protected $fillable = ['name','seats','status','note'];
    public function orders(){ 
        return $this->hasMany(Order::class); 
    }

   
}

