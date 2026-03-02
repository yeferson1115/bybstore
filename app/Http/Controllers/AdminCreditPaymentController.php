<?php

namespace App\Http\Controllers;

use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminCreditPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditPayment::query()->with('creditApplication');

        if ($request->filled('credit_application_id')) {
            $query->where('credit_application_id', $request->integer('credit_application_id'));
        }

        if ($request->filled('document_number')) {
            $query->where('document_number', $request->string('document_number'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        return view('admin.credit-payments.index', compact('payments'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = CreditPayment::query()->with('creditApplication');

        if ($request->filled('credit_application_id')) {
            $query->where('credit_application_id', $request->integer('credit_application_id'));
        }

        if ($request->filled('document_number')) {
            $query->where('document_number', $request->string('document_number'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $rows = $query->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Crédito', 'Cédula', 'Cliente', 'Referencia', 'Monto', 'Estado', 'Fecha']);

            foreach ($rows as $payment) {
                fputcsv($handle, [
                    $payment->id,
                    $payment->credit_application_id,
                    $payment->document_number,
                    $payment->payer_name,
                    $payment->reference,
                    $payment->amount,
                    $payment->status,
                    optional($payment->paid_at ?? $payment->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'reporte-pagos-credito.csv', ['Content-Type' => 'text/csv']);
    }
}
