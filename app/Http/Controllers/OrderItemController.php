<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;

class OrderItemController extends Controller
{
    //
    public function show($id)
    {
        $item = OrderItem::with('addons.addon','product')->findOrFail($id);
        return response()->json($item);
    }

}
