<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BYB Store | Créditos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="p-5 bg-white shadow-sm rounded-4 text-center">
            <h1 class="display-5 fw-bold">Financiación BYB Store</h1>
            <p class="lead text-muted mt-3">Solicita tu crédito o consulta y paga tus cuotas de forma rápida y segura.</p>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                <a href="{{ route('credit-applications.create') }}" class="btn btn-primary btn-lg px-4">Solicitar crédito</a>
                <a href="{{ route('credit-portal.index') }}" class="btn btn-outline-dark btn-lg px-4">Consultar y pagar crédito</a>
            </div>
        </div>
    </div>
</body>
</html>
