<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .title { background: #7b1f7a; color: #fff; font-weight: bold; text-align: center; padding: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        td, th { border: 1px solid #222; padding: 4px; vertical-align: top; }
        .label { font-weight: bold; width: 28%; background: #f2f2f2; }
        .signature { height: 90px; border: 1px solid #222; }
    </style>
</head>
<body>
    <div class="title">SOLICITUD DE CRÉDITO</div>
    <table>
        <tr><td class="label">Fecha de solicitud</td><td>{{ optional($application->request_date)->format('Y-m-d') }}</td></tr>
    </table>

    <div class="title">DATOS PERSONALES</div>
    <table>
        <tr><td class="label">Nombres y apellidos</td><td>{{ $application->full_name }}</td></tr>
        <tr><td class="label">Documento</td><td>{{ $application->document_type }} - {{ $application->document_number }}</td></tr>
        <tr><td class="label">Teléfonos</td><td>{{ $application->phone_primary }} / {{ $application->phone_secondary }}</td></tr>
        <tr><td class="label">Correo</td><td>{{ $application->email }}</td></tr>
        <tr><td class="label">Dirección</td><td>{{ $application->residential_address }}, {{ $application->neighborhood }}, {{ $application->city }}</td></tr>
    </table>

    <div class="title">DATOS LABORALES Y CRÉDITO</div>
    <table>
        <tr><td class="label">Empresa</td><td>{{ $application->company_name }}</td></tr>
        <tr><td class="label">Sede / Contrato</td><td>{{ $application->work_site }} / {{ $application->contract_type }}</td></tr>
        <tr><td class="label">Ingreso mensual</td><td>${{ number_format((float) $application->monthly_income, 2, ',', '.') }}</td></tr>
        <tr><td class="label">Productos solicitados</td><td>{{ $application->requested_products }}</td></tr>
        <tr><td class="label">Valor cuota / # cuotas</td><td>${{ number_format((float) $application->installment_value, 2, ',', '.') }} / {{ $application->installments_count }}</td></tr>
        <tr><td class="label">Frecuencia pago</td><td>{{ $application->payment_frequency }}</td></tr>
        <tr><td class="label">Observaciones</td><td>{{ $application->observations }}</td></tr>
    </table>

    <div class="title">AUTORIZACIÓN DE DESCUENTO</div>
    <table>
        <tr><td class="label">Empleador / NIT</td><td>{{ $application->employer_name }} / {{ $application->employer_nit }}</td></tr>
        <tr><td class="label">Empleado / Documento</td><td>{{ $application->employee_name }} / {{ $application->employee_document }}</td></tr>
        <tr><td class="label">Cargo</td><td>{{ $application->employee_position }}</td></tr>
        <tr><td class="label">Descuento por</td><td>{{ $application->discount_concept }}</td></tr>
        <tr><td class="label">Valor total</td><td>${{ number_format((float) $application->discount_total_value, 2, ',', '.') }}</td></tr>
    </table>

    <p><strong>Firma del solicitante:</strong></p>
    <div class="signature">
        @if ($application->signature_path)
            <img src="{{ public_path('storage/' . $application->signature_path) }}" style="height: 88px;">
        @endif
    </div>
</body>
</html>
