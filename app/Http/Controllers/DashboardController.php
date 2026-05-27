<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CreditApplication;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Fecha actual
        $today = Carbon::today();
        $month = Carbon::now()->month;

        // Total ventas del día (cerrados y pagados)
        $salesToday = 0;

        // Total ventas del mes
        $salesMonth = 0;

        // Cantidad de pedidos del día
        $ordersToday = 0;

        // Cantidad de pedidos del mes
        $ordersMonth = 0;

        $recentCreditApplications = CreditApplication::query()
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->take(5)
            ->get(['id', 'full_name', 'document_number', 'submitted_at', 'status']);

        $recentApprovedPayments = CreditPayment::query()
            ->where('status', 'approved')
            ->latest('paid_at')
            ->take(5)
            ->get(['id', 'credit_application_id', 'reference', 'amount', 'paid_at', 'status']);

        return view('admin.home.dashboard', compact(
            'salesToday',
            'salesMonth',
            'ordersToday',
            'ordersMonth',
            'recentCreditApplications',
            'recentApprovedPayments'
        ));
    }

    
}
