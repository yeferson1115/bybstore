<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">

<style>
@page { margin: 12px; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 9px;
    color:#000;
}

table {
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

td {
    border:1px solid #000;
    padding:3px;
    vertical-align:middle;
}

.header {
    background:#7a1f7b;
    color:#fff;
    font-weight:bold;
    text-align:center;
    font-size:14px;
}

.subheader {
    background:#bfbfbf;
    font-weight:bold;
    text-align:center;
}

.label {
    font-weight:bold;
}

.center { text-align:center; }
.right { text-align:right; }
.small { font-size:8px; }
.text { text-align:justify; }

.signature img {
    max-height:70px;
}

.id-img {
    width:100%;
    max-height:180px;
    object-fit:contain;
}
</style>
</head>

<body>

@php
$commercialName = trim(($application->commercialUser->name ?? '') . ' ' . ($application->commercialUser->last_name ?? ''));
$commercialContact = $application->commercialUser->contact ?? $application->commercialUser->phone ?? '';
$commercialSignaturePath = $application->commercialUser->signature_path ?? null;
@endphp

<!-- HEADER -->
<table>
<tr>
<td colspan="6" class="header">SOLICITUD DE CREDITO</td>
<td colspan="2" class="center header">
B&B STORE S.A.S.<br>
NIT: 902034686-0
</td>
</tr>

<!-- DATOS PERSONALES -->
<tr><td colspan="8" class="subheader">DATOS PERSONALES</td></tr>

<tr>
<td class="label">NOMBRES Y APELLIDOS</td>
<td colspan="7">{{ $application->full_name }}</td>
</tr>

<tr>
<td class="label">DOCUMENTO</td>
<td colspan="3">{{ $application->document_type }} {{ $application->document_number }}</td>
<td class="label">FECHA EXPEDICION</td>
<td colspan="3">{{ optional($application->expedition_date)->format('d/m/Y') }}</td>
</tr>

<tr>
<td class="label">CELULAR</td>
<td colspan="3">{{ $application->phone_primary }}</td>
<td class="label">CORREO</td>
<td colspan="3">{{ $application->email }}</td>
</tr>

<tr>
<td class="label">DIRECCION</td>
<td colspan="3">{{ $application->residential_address }}</td>
<td class="label">BARRIO</td>
<td colspan="3">{{ $application->neighborhood }}</td>
</tr>

<tr>
<td class="label">CIUDAD</td>
<td colspan="3">{{ $application->city }}</td>
<td class="label">FECHA SOLICITUD</td>
<td colspan="3">{{ optional($application->created_at)->format('d/m/Y') }}</td>
</tr>

<!-- DATOS LABORALES -->
<tr><td colspan="8" class="subheader">DATOS LABORALES</td></tr>

<tr>
<td class="label">EMPRESA</td>
<td colspan="3">{{ $application->employer_name }}</td>
<td class="label">NIT</td>
<td colspan="3">{{ $application->employer_nit }}</td>
</tr>

<tr>
<td class="label">CARGO</td>
<td colspan="3">{{ $application->employee_position }}</td>
<td class="label">TIPO CONTRATO</td>
<td colspan="3">{{ $application->contract_type }}</td>
</tr>

<tr>
<td class="label">INGRESOS</td>
<td colspan="3">${{ number_format($application->monthly_income,0,',','.') }}</td>
<td class="label">FECHA INGRESO</td>
<td colspan="3">{{ optional($application->hire_date)->format('d/m/Y') }}</td>
</tr>

<!-- DATOS CREDITO -->
<tr><td colspan="8" class="subheader">DATOS DEL CREDITO</td></tr>

<tr>
<td class="label">PRODUCTO</td>
<td colspan="7">{{ $application->requested_products }}</td>
</tr>

<tr>
<td class="label">VALOR</td>
<td>${{ number_format($application->discount_total_value,0,',','.') }}</td>

<td class="label">CUOTA</td>
<td>${{ number_format($application->installment_value,0,',','.') }}</td>

<td class="label">CUOTAS</td>
<td>{{ $application->installments_count }}</td>

<td class="label">1RA CUOTA</td>
<td>{{ optional($application->first_payment_date)->format('d/m/Y') }}</td>
</tr>

<tr>
<td colspan="8" class="center">
DECADAL &nbsp;&nbsp; QUINCENAL &nbsp;&nbsp; MENSUAL
</td>
</tr>

<!-- TEXTO LEGAL -->
<tr>
<td colspan="8" class="text small">
Autorizo a B&B STORE S.A.S para la toma y verificación de los datos suministrados con el fin de realizar los cobros pertinentes, en caso de incumplir con las cuotas o pagos pactados, se procederá con el cobro jurídico conforme a la ley.

<br><br>

Los datos personales aquí recolectados serán tratados conforme a la Ley 1581 de 2012, Decreto 1377 de 2013 y demás normas aplicables. El titular tiene derecho a conocer, actualizar y rectificar sus datos personales, solicitar prueba de la autorización otorgada y revocar la misma.
</td>
</tr>

<!-- AUTORIZACION -->
<tr><td colspan="8" class="subheader">AUTORIZACION DE DESCUENTO</td></tr>

<tr>
<td class="label">EMPLEADOR</td>
<td colspan="3">{{ $application->employer_name }}</td>
<td class="label">FECHA</td>
<td colspan="3">{{ optional($application->discount_authorization_date)->format('d/m/Y') }}</td>
</tr>

<tr>
<td class="label">NIT</td>
<td colspan="7">{{ $application->employer_nit }}</td>
</tr>

<tr>
<td class="label">EMPLEADO</td>
<td colspan="3">{{ $application->full_name }}</td>
<td class="label">DOCUMENTO</td>
<td colspan="3">{{ $application->document_number }}</td>
</tr>

<tr>
<td class="label">DESCUENTO POR</td>
<td colspan="7">{{ $application->discount_concept }}</td>
</tr>

<tr>
<td class="label">VALOR TOTAL</td>
<td colspan="7">${{ number_format($application->discount_total_value,0,',','.') }}</td>
</tr>

<tr>
<td colspan="8" class="text small">
Autorizo a mi empleador para que descuente de mi salario el valor correspondiente al crédito adquirido con B&B STORE S.A.S., conforme a la normativa laboral vigente.
</td>
</tr>

<tr>
<td colspan="4" class="center">FIRMA EMPLEADO</td>
<td colspan="4" class="center">ASESOR</td>
</tr>

<tr>
<td colspan="4" class="signature center">
@if($application->signature_path)
<img src="{{ public_path($application->signature_path) }}">
@endif
</td>

<td colspan="4" class="signature center">
@if($commercialSignaturePath)
<img src="{{ public_path($commercialSignaturePath) }}">
@endif
</td>
</tr>

<tr>
<td colspan="4" class="center">{{ $application->full_name }}</td>
<td colspan="4" class="center">{{ $commercialName }}</td>
</tr>

<tr>
<td colspan="4" class="center">CONTACTO: {{ $application->phone_primary }}</td>
<td colspan="4" class="center">CONTACTO: {{ $commercialContact }}</td>
</tr>

<!-- DOCUMENTO -->
<tr><td colspan="8" class="subheader">DOCUMENTO DE IDENTIDAD</td></tr>

<tr>
<td colspan="4" class="center">
@if($application->id_front_path)
<img class="id-img" src="{{ public_path($application->id_front_path) }}">
@endif
</td>

<td colspan="4" class="center">
@if($application->id_back_path)
<img class="id-img" src="{{ public_path($application->id_back_path) }}">
@endif
</td>
</tr>

<!-- ENTREGA -->
<tr><td colspan="8" class="subheader">CONSTANCIA DE ENTREGA</td></tr>

<tr>
<td class="label">NOMBRE</td>
<td colspan="7">{{ $application->full_name }}</td>
</tr>

<tr>
<td class="label">DIRECCION</td>
<td colspan="7">{{ $application->residential_address }}</td>
</tr>

<tr>
<td class="label">CIUDAD</td>
<td colspan="3">{{ $application->city }}</td>
<td class="label">BARRIO</td>
<td colspan="3">{{ $application->neighborhood }}</td>
</tr>

<tr>
<td colspan="8" class="subheader">ARTICULOS ENTREGADOS</td>
</tr>

<tr>
<td colspan="8">{{ $application->requested_products }}</td>
</tr>

<tr>
<td colspan="8" class="center text">
CONFIRMO QUE RECIBÍ A SATISFACCIÓN LOS PRODUCTOS EN BUEN ESTADO Y FUNCIONAMIENTO.
</td>
</tr>

<tr>
<td colspan="4" class="center">FIRMA</td>
<td colspan="4" class="center">ENTREGADO POR</td>
</tr>

<tr>
<td colspan="4" class="signature center">
@if($application->signature_path)
<img src="{{ public_path($application->signature_path) }}">
@endif
</td>

<td colspan="4" class="center">{{ $commercialName }}</td>
</tr>

</table>

</body>
</html>