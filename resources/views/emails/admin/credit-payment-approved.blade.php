<p>Se confirmó un pago aprobado por Wompi.</p>
<ul>
    <li><strong>Referencia:</strong> {{ $payment->reference }}</li>
    <li><strong>Crédito:</strong> #{{ $payment->credit_application_id }}</li>
    <li><strong>Documento:</strong> {{ $payment->document_number }}</li>
    <li><strong>Monto:</strong> ${{ number_format((float) $payment->amount, 0, ',', '.') }}</li>
    <li><strong>Estado:</strong> {{ strtoupper($payment->status) }}</li>
    <li><strong>Fecha pago:</strong> {{ optional($payment->paid_at)->format('d/m/Y H:i') }}</li>
</ul>
