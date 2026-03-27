<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 14px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td, th { border: 1px solid #1f1f1f; padding: 3px 4px; vertical-align: middle; word-wrap: break-word; }
        .header-main { background: #7a1f7b; color: #fff; font-weight: 700; text-transform: uppercase; font-size: 16px; }
        .header-section { background: #8f2a90; color: #fff; font-weight: 700; text-transform: uppercase; text-align: center; font-size: 12px; }
        .header-gray { background: #b5b5b5; font-weight: 700; text-transform: uppercase; text-align: center; }
        .label { background: #bdbdbd; font-weight: 700; text-transform: uppercase; }
        .small-label { background: #c7c7c7; font-weight: 700; text-transform: uppercase; font-size: 10px; }
        .center { text-align: center; }
        .text-justify { text-align: justify; }
        .id-image { width: 100%; max-height: 260px; object-fit: contain; }
        .h44 { height: 44px; }
        .h64 { height: 64px; }
        .h80 { height: 80px; }
        .signature-box img { max-height: 84px; max-width: 100%; }
        .block-space { margin-top: 3px; }
    </style>
</head>
<body>
    <table>
        <tr class="header-main h44">
            <td style="width: 60%;" class="center">SOLICITUD DE CREDITO</td>
            <td style="width: 40%;" class="right">NETSECURITYBERBOU<br>NIT: 21388364-8</td>
        </tr>
    </table>

    <table class="block-space">
        <tr><td class="header-gray" colspan="8">AUTORIZACION DE DESCUENTO</td></tr>
        <tr>
            <td class="label" colspan="2">EMPLEADOR:</td>
            <td colspan="4">{{ $application->employer_name }}</td>
            <td class="label">FECHA</td>
            <td>{{ optional($application->discount_authorization_date)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">NIT:</td>
            <td colspan="6">{{ $application->employer_nit }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">NOMBRE DEL EMPLEADO:</td>
            <td colspan="3">{{ $application->employee_name }}</td>
            <td class="label">DOCUMENTO:</td>
            <td colspan="2">{{ $application->employee_document }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">CARGO:</td>
            <td colspan="6">{{ $application->employee_position }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">DESCUENTO POR:</td>
            <td colspan="6">{{ $application->discount_concept }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">VALOR TOTAL:</td>
            <td colspan="6">${{ number_format((float) $application->discount_total_value, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="8" class="text-justify h64">
                Mediante la firma del presente documento, manifiesto de manera expresa y voluntaria mi autorización para que la empresa realice el descuento correspondiente en la nómina que me corresponde, derivado del crédito registrado.
            </td>
        </tr>
        <tr>
            <td class="header-gray" colspan="4">VALOR CUOTA QUINCENAL</td>
            <td class="header-gray" colspan="4">NRO. DE CUOTAS</td>
        </tr>
        <tr>
            <td colspan="4" class="center">${{ number_format((float) $application->installment_value, 0, ',', '.') }}</td>
            <td colspan="4" class="center">{{ $application->installments_count }}</td>
        </tr>
        <tr>
            <td colspan="4" class="center">FIRMA DEL EMPLEADO</td>
            <td colspan="4" class="center">FIRMA</td>
        </tr>
        <tr>
            <td colspan="4">{{ $application->full_name }}</td>
            <td colspan="4" class="signature-box center">
                @if ($application->signature_path)
                    <img src="{{ public_path($application->signature_path) }}" alt="Firma">
                @endif
            </td>
        </tr>
    </table>

    <table class="block-space">
        <tr><td class="header-section" colspan="2">DOCUMENTO DE IDENTIDAD</td></tr>
        <tr>
            <td style="width: 50%;" class="center">
                <strong>CÉDULA FRENTE</strong><br>
                @if ($application->id_front_path)
                    <img class="id-image" src="{{ public_path($application->id_front_path) }}" alt="Cédula frente">
                @endif
            </td>
            <td style="width: 50%;" class="center">
                <strong>CÉDULA REVERSO</strong><br>
                @if ($application->id_back_path)
                    <img class="id-image" src="{{ public_path($application->id_back_path) }}" alt="Cédula reverso">
                @endif
            </td>
        </tr>
    </table>

    <table class="block-space">
        <tr><td class="header-section" colspan="8">CONSTANCIA DE ENTREGA</td></tr>
        <tr>
            <td class="label" colspan="2">NOMBRES Y APELLIDOS</td>
            <td colspan="6">{{ $application->full_name }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">DOCUMENTO DE IDENTIDAD</td>
            <td class="small-label">TIPO</td>
            <td>{{ $application->document_type }}</td>
            <td class="small-label">NUMERO</td>
            <td colspan="3">{{ $application->document_number }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">DIRECCION DE ENTREGA</td>
            <td colspan="6">{{ $application->residential_address }}</td>
        </tr>
        <tr>
            <td class="label" colspan="2">BARRIO</td>
            <td colspan="3">{{ $application->neighborhood }}</td>
            <td class="small-label">CIUDAD</td>
            <td colspan="2">{{ $application->city }}</td>
        </tr>
        <tr>
            <td class="header-gray" colspan="8">ARTICULOS ENTREGADOS</td>
        </tr>
        <tr>
            <td colspan="8" class="h44">{{ $application->requested_products }}</td>
        </tr>
        <tr>
            <td colspan="8" class="text-justify h64 center"><strong>
                CONFIRMO QUE RECIBÍ A SATISFACCIÓN LOS PRODUCTOS ARRIBA DESCRITOS.
            </strong></td>
        </tr>
        <tr>
            <td class="label" colspan="2">RECIBI A SATISFACCION</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td class="label" colspan="2">FIRMA</td>
            <td colspan="2"></td>
            <td class="label" colspan="2">ENTREGADO POR</td>
            <td colspan="2">Santiago Muñoz Henao</td>
        </tr>
        <tr>
            <td class="label" colspan="2">DOCUMENTO DE IDENTIDAD</td>
            <td class="small-label">TIPO</td>
            <td>{{ $application->document_type }}</td>
            <td class="small-label">NUMERO</td>
            <td colspan="3">{{ $application->document_number }}</td>
        </tr>
    </table>
</body>
</html>
