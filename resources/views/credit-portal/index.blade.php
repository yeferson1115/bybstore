<x-public-layout>
    <style>
        .credit-portal-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 14px 32px rgba(15, 23, 42, .08);
        }

        .pay-installment-btn {
            width: auto;
            min-height: 44px;
        }

        @media (max-width: 767.98px) {
            .credit-portal-wrap {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .credit-portal-wrap h4 {
                font-size: 1.15rem;
            }

            .credit-portal-wrap .card-body,
            .credit-portal-wrap .card-header {
                padding: 1rem;
            }

            .credit-summary {
                text-align: left !important;
                width: 100%;
            }

            .credit-portal-wrap .btn,
            .credit-portal-wrap .form-control {
                min-height: 44px;
            }

            .credit-portal-wrap .table {
                font-size: .85rem;
            }

            .pay-installment-btn {
                width: 100%;
            }
        }
    </style>

    <div class="container py-4 credit-portal-wrap">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card mb-4 credit-portal-card">
            <div class="card-body">
                <h4 class="mb-3">Consultar y pagar crédito</h4>
                <form method="GET" action="{{ route('credit-portal.index') }}" class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Cédula</label>
                        <input class="form-control" name="document_number" value="{{ $documentNumber }}" required>
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <button class="btn btn-soft-brand w-100 fw-semibold">Consultar</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($documentNumber)
            <div class="card mb-4 credit-portal-card">
                <div class="card-header"><h5 class="mb-0">Créditos aprobados</h5></div>
                <div class="card-body">
                    @forelse ($applications as $application)
                        @php
                            $paidInstallments = (int) ($application->approved_payments_count ?? 0);
                            $pendingInstallments = max(((int) $application->installments_count) - $paidInstallments, 0);
                        @endphp
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                <div>
                                    <strong>Crédito #{{ $application->id }}</strong><br>
                                    Cliente: {{ $application->full_name }}
                                </div>
                                <div class="text-md-end credit-summary">
                                    Cuota: <strong>${{ number_format((float) $application->installment_value, 0, ',', '.') }}</strong><br>
                                    Pendientes: <strong>{{ $pendingInstallments }}</strong>
                                </div>
                            </div>
                            @if ($pendingInstallments > 0)
                                <form method="POST" action="{{ route('credit-portal.pay') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="credit_application_id" value="{{ $application->id }}">
                                    <input type="hidden" name="document_number" value="{{ $documentNumber }}">
                                    <button class="btn btn-success pay-installment-btn">Pagar próxima cuota con Wompi</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No encontramos créditos aprobados para esta cédula.</p>
                    @endforelse
                </div>
            </div>

            <div class="card credit-portal-card">
                <div class="card-header"><h5 class="mb-0">Historial de pagos</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Referencia</th>
                                <th>Crédito</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td>{{ $payment->reference }}</td>
                                    <td>#{{ $payment->credit_application_id }}</td>
                                    <td>${{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-{{ $payment->status === 'approved' ? 'success' : 'secondary' }}">{{ $payment->status }}</span></td>
                                    <td>{{ optional($payment->paid_at ?? $payment->created_at)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($payment->status !== 'approved')
                                            <form method="POST" action="{{ route('credit-portal.refresh', $payment) }}">
                                                @csrf
                                                <input type="hidden" name="document_number" value="{{ $documentNumber }}">
                                                <button class="btn btn-sm btn-outline-primary w-100">Consultar transacción</button>
                                            </form>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-muted">Sin pagos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-public-layout>
