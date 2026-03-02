<x-guest-layout>
    <div class="container py-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Solicitud de crédito + autorización de descuento</h4>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('credit-applications.store') }}" method="POST" enctype="multipart/form-data" id="credit-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ old('token', $token) }}">
                    <input type="hidden" name="signature_data" id="signature_data">

                    <h5>Datos personales</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Fecha solicitud</label><input type="date" class="form-control" name="request_date" value="{{ old('request_date', optional($application?->request_date)->format('Y-m-d')) }}"></div>
                        <div class="col-md-8"><label class="form-label">Nombres y apellidos</label><input class="form-control" name="full_name" value="{{ old('full_name', $application?->full_name) }}"></div>
                        <div class="col-md-3"><label class="form-label">Tipo documento</label><input class="form-control" name="document_type" value="{{ old('document_type', $application?->document_type) }}"></div>
                        <div class="col-md-3"><label class="form-label">Número documento</label><input class="form-control" name="document_number" value="{{ old('document_number', $application?->document_number) }}"></div>
                        <div class="col-md-3"><label class="form-label">Celular 1</label><input class="form-control" name="phone_primary" value="{{ old('phone_primary', $application?->phone_primary) }}"></div>
                        <div class="col-md-3"><label class="form-label">Celular 2</label><input class="form-control" name="phone_secondary" value="{{ old('phone_secondary', $application?->phone_secondary) }}"></div>
                        <div class="col-md-6"><label class="form-label">Correo</label><input type="email" class="form-control" name="email" value="{{ old('email', $application?->email) }}"></div>
                        <div class="col-md-6"><label class="form-label">Dirección residencia</label><input class="form-control" name="residential_address" value="{{ old('residential_address', $application?->residential_address) }}"></div>
                        <div class="col-md-6"><label class="form-label">Barrio</label><input class="form-control" name="neighborhood" value="{{ old('neighborhood', $application?->neighborhood) }}"></div>
                        <div class="col-md-6"><label class="form-label">Ciudad</label><input class="form-control" name="city" value="{{ old('city', $application?->city) }}"></div>
                    </div>

                    <hr>
                    <h5>Datos laborales y crédito</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Empresa donde labora</label>
                            <select class="form-control" name="company_id">
                                <option value="">Selecciona una empresa</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" @selected((string) old('company_id', $application?->company_id) === (string) $company->id)>
                                        {{ $company->name }} - NIT {{ $company->nit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Sede</label><input class="form-control" name="work_site" value="{{ old('work_site', $application?->work_site) }}"></div>
                        <div class="col-md-4"><label class="form-label">Tipo contrato</label><input class="form-control" name="contract_type" value="{{ old('contract_type', $application?->contract_type) }}"></div>
                        <div class="col-md-4"><label class="form-label">Ingresos mensuales</label><input type="number" step="0.01" class="form-control" name="monthly_income" value="{{ old('monthly_income', $application?->monthly_income) }}"></div>
                        <div class="col-md-4"><label class="form-label">Fecha ingreso</label><input type="date" class="form-control" name="hire_date" value="{{ old('hire_date', optional($application?->hire_date)->format('Y-m-d')) }}"></div>
                        <div class="col-md-12"><label class="form-label">Productos solicitados</label><textarea class="form-control" name="requested_products" rows="2">{{ old('requested_products', $application?->requested_products) }}</textarea></div>
                        <div class="col-md-4"><label class="form-label">Valor neto sin interés</label><input type="number" step="0.01" class="form-control" name="net_value_without_interest" value="{{ old('net_value_without_interest', $application?->net_value_without_interest) }}"></div>
                        <div class="col-md-4"><label class="form-label">Valor cuota</label><input type="number" step="0.01" class="form-control" name="installment_value" value="{{ old('installment_value', $application?->installment_value) }}"></div>
                        <div class="col-md-4"><label class="form-label">Número de cuotas</label><input type="number" class="form-control" name="installments_count" value="{{ old('installments_count', $application?->installments_count) }}"></div>
                        <div class="col-md-4"><label class="form-label">Frecuencia</label>
                            <select class="form-control" name="payment_frequency">
                                <option value="">Selecciona</option>
                                @foreach (['decadal' => 'Decadal', 'biweekly' => 'Quincenal', 'monthly' => 'Mensual'] as $key => $label)
                                    <option value="{{ $key }}" @selected(old('payment_frequency', $application?->payment_frequency) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8"><label class="form-label">Observaciones</label><input class="form-control" name="observations" value="{{ old('observations', $application?->observations) }}"></div>
                    </div>

                    <hr>
                    <h5>Autorización de descuento</h5>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Empleador</label><input class="form-control" name="employer_name" value="{{ old('employer_name', $application?->employer_name) }}"></div>
                        <div class="col-md-6"><label class="form-label">NIT</label><input class="form-control" name="employer_nit" value="{{ old('employer_nit', $application?->employer_nit) }}"></div>
                        <div class="col-md-4"><label class="form-label">Nombre empleado</label><input class="form-control" name="employee_name" value="{{ old('employee_name', $application?->employee_name) }}"></div>
                        <div class="col-md-4"><label class="form-label">Documento</label><input class="form-control" name="employee_document" value="{{ old('employee_document', $application?->employee_document) }}"></div>
                        <div class="col-md-4"><label class="form-label">Cargo</label><input class="form-control" name="employee_position" value="{{ old('employee_position', $application?->employee_position) }}"></div>
                        <div class="col-md-6"><label class="form-label">Descuento por</label><input class="form-control" name="discount_concept" value="{{ old('discount_concept', $application?->discount_concept) }}"></div>
                        <div class="col-md-3"><label class="form-label">Valor total</label><input type="number" step="0.01" class="form-control" name="discount_total_value" value="{{ old('discount_total_value', $application?->discount_total_value) }}"></div>
                        <div class="col-md-3"><label class="form-label">Fecha</label><input type="date" class="form-control" name="discount_authorization_date" value="{{ old('discount_authorization_date', optional($application?->discount_authorization_date)->format('Y-m-d')) }}"></div>
                    </div>

                    <hr>
                    <h5>Adjuntos obligatorios</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Cédula frente</label><input type="file" class="form-control" name="id_front"></div>
                        <div class="col-md-4"><label class="form-label">Cédula reverso</label><input type="file" class="form-control" name="id_back"></div>
                        <div class="col-md-4"><label class="form-label">Selfie con cédula</label><input type="file" class="form-control" name="selfie_with_id"></div>
                    </div>

                    <hr>
                    <h5>Firma en pantalla (obligatoria al enviar)</h5>
                    <div class="border rounded p-2 bg-light">
                        <canvas id="signature-pad" width="800" height="220" style="width:100%;max-width:100%;border:1px dashed #6c757d;background:#fff"></canvas>
                        <div class="mt-2 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-signature">Limpiar firma</button>
                        </div>
                    </div>

                    @if ($application?->signature_path)
                        <p class="mt-2 mb-0 text-success">Ya existe una firma guardada para este borrador.</p>
                    @endif

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-outline-primary" type="submit" name="action" value="draft">Guardar borrador</button>
                        <button class="btn btn-primary" type="submit" name="action" value="submit">Enviar solicitud</button>
                        @if ($application?->pdf_path)
                            <a class="btn btn-success" href="{{ route('credit-applications.pdf', $application) }}">Descargar PDF</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const canvas = document.getElementById('signature-pad');
            const hiddenInput = document.getElementById('signature_data');
            const clearBtn = document.getElementById('clear-signature');
            const form = document.getElementById('credit-form');
            const ctx = canvas.getContext('2d');
            let drawing = false;

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#111827';

            const position = (e) => {
                const rect = canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: (clientX - rect.left) * (canvas.width / rect.width),
                    y: (clientY - rect.top) * (canvas.height / rect.height),
                };
            };

            const start = (e) => {
                drawing = true;
                const p = position(e);
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
                e.preventDefault();
            };

            const move = (e) => {
                if (!drawing) {
                    return;
                }
                const p = position(e);
                ctx.lineTo(p.x, p.y);
                ctx.stroke();
                e.preventDefault();
            };

            const end = () => {
                drawing = false;
            };

            ['mousedown', 'touchstart'].forEach(evt => canvas.addEventListener(evt, start, { passive: false }));
            ['mousemove', 'touchmove'].forEach(evt => canvas.addEventListener(evt, move, { passive: false }));
            ['mouseup', 'mouseleave', 'touchend'].forEach(evt => canvas.addEventListener(evt, end));

            clearBtn.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hiddenInput.value = '';
            });

            form.addEventListener('submit', () => {
                hiddenInput.value = canvas.toDataURL('image/png');
            });
        })();
    </script>
</x-guest-layout>
