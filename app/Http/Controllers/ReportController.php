<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Order::query()
            ->with('customer','table')
            ->where('status', 'cerrado');

        // FILTROS
        if ($request->filled('start')) {
            $query->whereDate('created_at', '>=', $request->start);
        }

        if ($request->filled('end')) {
            $query->whereDate('created_at', '<=', $request->end);
        }

        if ($request->filled('search')) {
            $term = $request->search;

            $query->where(function($q) use ($term) {
                $q->where('id', $term)
                  ->orWhere('table_id', $term)
                  ->orWhere('customer_name', 'LIKE', "%$term%");
            });
        }
        $query->where('total', '>', 0);

        $orders = $query->orderBy('id','desc')->paginate(20);

        return view('admin.reports.sales', compact('orders'));
    }


    /** EXPORTAR EXCEL */
   public function exportSales(Request $request)
    {
        $start  = $request->start;
        $end    = $request->end;
        $search = $request->search;

        return Excel::download(
            new SalesExport($start, $end, $search),
            'reporte_ventas.xlsx'
        );
    }
}
