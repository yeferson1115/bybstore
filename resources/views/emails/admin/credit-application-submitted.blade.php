<p>Se registró una nueva solicitud de crédito.</p>
<ul>
    <li><strong>ID:</strong> {{ $application->id }}</li>
    <li><strong>Cliente:</strong> {{ $application->full_name }}</li>
    <li><strong>Documento:</strong> {{ $application->document_number }}</li>
    <li><strong>Email:</strong> {{ $application->email }}</li>
    <li><strong>Celular:</strong> {{ $application->phone_primary }}</li>
    <li><strong>Fecha envío:</strong> {{ optional($application->submitted_at)->format('d/m/Y H:i') }}</li>
</ul>
