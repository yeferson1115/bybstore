<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Fecha actual
        $today = Carbon::today();
        $month = Carbon::now()->month;

        // Total ventas del día (cerrados y pagados)
        $salesToday = Order::whereDate('created_at', $today)
            ->where('status', 'cerrado')
            ->sum('total');

        // Total ventas del mes
        $salesMonth = Order::whereMonth('created_at', $month)
            ->where('status', 'cerrado')
            ->sum('total');

        // Cantidad de pedidos del día
        $ordersToday = Order::whereDate('created_at', $today)
            ->where('status', 'cerrado')
            ->count();

        // Cantidad de pedidos del mes
        $ordersMonth = Order::whereMonth('created_at', $month)
            ->where('status', 'cerrado')
            ->count();

        return view('admin.home.dashboard', compact(
            'salesToday',
            'salesMonth',
            'ordersToday',
            'ordersMonth'
        ));
    }

    
}
