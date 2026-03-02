<?php

namespace App\Http\Controllers;

use App\Models\CreditApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CreditApplicationController extends Controller
{
    public function create(Request $request): View
    {
        $application = null;

        if ($request->filled('token')) {
            $application = CreditApplication::where('public_token', $request->string('token'))->first();
        }

        return view('credit-applications.create', [
            'application' => $application,
            'token' => $application?->public_token ?? (string) Str::uuid(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'draft');
        $isSubmit = $action === 'submit';

        $rules = [
            'token' => ['required', 'string'],
            'request_date' => ['nullable', 'date'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'document_type' => ['nullable', 'string', 'max:50'],
            'document_number' => ['nullable', 'string', 'max:60'],
            'document_issue_date' => ['nullable', 'date'],
            'phone_primary' => ['nullable', 'string', 'max:30'],
            'phone_secondary' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'residential_address' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'work_site' => ['nullable', 'string', 'max:120'],
            'hire_date' => ['nullable', 'date'],
            'contract_type' => ['nullable', 'string', 'max:120'],
            'monthly_income' => ['nullable', 'numeric', 'min:0'],
            'requested_products' => ['nullable', 'string'],
            'net_value_without_interest' => ['nullable', 'numeric', 'min:0'],
            'installment_value' => ['nullable', 'numeric', 'min:0'],
            'first_installment_date' => ['nullable', 'date'],
            'installments_count' => ['nullable', 'integer', 'min:1'],
            'payment_frequency' => ['nullable', Rule::in(['decadal', 'biweekly', 'monthly'])],
            'observations' => ['nullable', 'string'],
            'employer_name' => ['nullable', 'string', 'max:255'],
            'discount_authorization_date' => ['nullable', 'date'],
            'employer_nit' => ['nullable', 'string', 'max:60'],
            'employee_name' => ['nullable', 'string', 'max:255'],
            'employee_document' => ['nullable', 'string', 'max:60'],
            'employee_position' => ['nullable', 'string', 'max:120'],
            'discount_concept' => ['nullable', 'string', 'max:255'],
            'discount_total_value' => ['nullable', 'numeric', 'min:0'],
            'signature_data' => ['nullable', 'string'],
            'id_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'id_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'selfie_with_id' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];

        if ($isSubmit) {
            $requiredFields = [
                'request_date', 'full_name', 'document_type', 'document_number', 'phone_primary',
                'email', 'residential_address', 'city', 'company_name', 'monthly_income',
                'requested_products', 'installment_value', 'installments_count', 'payment_frequency',
                'employer_name', 'employee_name',
            ];

            foreach ($requiredFields as $field) {
                $rules[$field][0] = 'required';
            }

            $rules['signature_data'] = ['required', 'string'];
        }

        $data = $request->validate($rules);

        $application = CreditApplication::firstOrNew([
            'public_token' => $data['token'],
        ]);

        $application->fill($data);
        $application->status = $isSubmit ? 'submitted' : 'draft';

        $basePath = "credit-applications/{$data['token']}";

        foreach (['id_front', 'id_back', 'selfie_with_id'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $path = $request->file($fileField)->store($basePath, 'public');
                $modelField = $fileField . '_path';
                $application->{$modelField} = $path;
            }
        }

        if (! empty($data['signature_data'])) {
            $signaturePath = $this->saveSignature($data['signature_data'], $basePath);
            if ($signaturePath !== null) {
                $application->signature_path = $signaturePath;
            }
        }

        if ($isSubmit) {
            if (! $application->id_front_path || ! $application->id_back_path || ! $application->selfie_with_id_path) {
                return back()->withErrors([
                    'documents' => 'Debes adjuntar cédula frente, cédula reverso y foto sosteniendo la cédula para enviar la solicitud.',
                ])->withInput();
            }

            if (! $application->signature_path) {
                return back()->withErrors([
                    'signature_data' => 'La firma en pantalla es obligatoria para enviar la solicitud.',
                ])->withInput();
            }

            $application->submitted_at = now();
        }

        $application->save();

        if ($isSubmit) {
            $pdfPath = $this->generatePdf($application, $basePath);
            $application->pdf_path = $pdfPath;
            $application->save();

            return redirect()
                ->route('credit-applications.create', ['token' => $application->public_token])
                ->with('status', 'Solicitud enviada correctamente. PDF generado.');
        }

        return redirect()
            ->route('credit-applications.create', ['token' => $application->public_token])
            ->with('status', 'Borrador guardado correctamente.');
    }

    public function downloadPdf(CreditApplication $creditApplication)
    {
        abort_unless($creditApplication->pdf_path && Storage::disk('public')->exists($creditApplication->pdf_path), 404);

        return Storage::disk('public')->download($creditApplication->pdf_path, "solicitud-{$creditApplication->id}.pdf");
    }

    private function saveSignature(string $signatureData, string $basePath): ?string
    {
        if (! str_starts_with($signatureData, 'data:image/png;base64,')) {
            return null;
        }

        $rawData = substr($signatureData, strpos($signatureData, ',') + 1);
        $decoded = base64_decode($rawData, true);

        if ($decoded === false) {
            return null;
        }

        $signaturePath = $basePath . '/signature.png';
        Storage::disk('public')->put($signaturePath, $decoded);

        return $signaturePath;
    }

    private function generatePdf(CreditApplication $application, string $basePath): string
    {
        $pdf = Pdf::loadView('credit-applications.pdf', [
            'application' => $application,
        ])->setPaper('a4');

        $path = $basePath . '/solicitud.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
