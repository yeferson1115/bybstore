<?php

namespace App\Http\Controllers;

use App\Models\CreditApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCreditApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $applications = CreditApplication::query()
            ->with('company:id,name,nit')
            ->when($statusFilter !== '', function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nestedQuery) use ($search) {
                    $nestedQuery->where('full_name', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%")
                        ->orWhere('phone_primary', 'like', "%{$search}%")
                        ->orWhere('public_token', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.credit-applications.index', [
            'applications' => $applications,
            'statuses' => $this->statuses(),
            'statusFilter' => $statusFilter,
            'search' => $search,
        ]);
    }

    public function show(CreditApplication $creditApplication): View
    {
        $creditApplication->load('company:id,name,nit');

        return view('admin.credit-applications.show', [
            'application' => $creditApplication,
            'statuses' => $this->statuses(),
        ]);
    }

    public function updateStatus(Request $request, CreditApplication $creditApplication): RedirectResponse
    {
        $previousStatus = $creditApplication->status;
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ]);

        $creditApplication->status = $data['status'];
        $creditApplication->save();

        if ($previousStatus !== $creditApplication->status) {
            $phone = $this->normalizePhone((string) $creditApplication->phone_primary);

            if (preg_match('/^57\d{10}$/', $phone)) {
                $statusLabel = $this->statuses()[$creditApplication->status] ?? $creditApplication->status;
                $statusMessage = "Tu solicitud de crédito ahora está en estado: {$statusLabel}.";

                $this->sendSms($phone, $statusMessage);
            }
        }

        return redirect()
            ->route('admin.credit-applications.show', $creditApplication)
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function regeneratePdfs(CreditApplication $creditApplication): RedirectResponse
    {
        $basePath = "credit-applications/{$creditApplication->public_token}";
        $pdfPath = $this->generatePdf($creditApplication, $basePath);
        $authorizationPdfPath = $this->generateAuthorizationPdf($creditApplication, $basePath);

        $creditApplication->pdf_path = $pdfPath;
        $creditApplication->authorization_pdf_path = $authorizationPdfPath;
        $creditApplication->save();

        return redirect()
            ->route('admin.credit-applications.show', $creditApplication)
            ->with('success', 'PDFs regenerados correctamente.');
    }

    private function statuses(): array
    {
        return [
            'draft' => 'Borrador',
            'submitted' => 'Enviada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
        ];
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '57') && strlen($digits) === 12) {
            return $digits;
        }

        if (strlen($digits) === 10) {
            return '57' . $digits;
        }

        return $digits;
    }

    private function sendSms(string $phone, string $message): bool
    {
        $from = (string) config('services.hablame.from', '9409110331');
        $campaignName = (string) config('services.hablame.campaign_name', 'B&B STORE');

        $formattedMessage = str_contains($message, 'B&B STORE')
            ? $message
            : "B&B STORE {$message}";

        $response = Http::withOptions([
                'verify' => false,
            ])
            ->withHeaders([
                'X-Hablame-Key' => (string) config('services.hablame.api_key', ''),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->asJson()
            ->post('https://www.hablame.co/api/sms/v5/send', [
                'priority' => true,
                'certificate' => false,
                'sendDate' => 'Now',
                'campaignName' => $campaignName,
                'from' => $from,
                'flash' => true,
                'messages' => [[
                    'to' => $phone,
                    'text' => $formattedMessage,
                ]],
            ]);

        return $response->successful();
    }

    private function generatePdf(CreditApplication $application, string $basePath): string
    {
        $pdf = Pdf::loadView('credit-applications.pdf', [
            'application' => $application,
        ])->setPaper('a4');

        $path = $basePath . '/solicitud.pdf';
        $fullPath = public_path($path);
        $this->ensurePublicDirectoryExists(dirname($fullPath));
        file_put_contents($fullPath, $pdf->output());

        return $path;
    }

    private function generateAuthorizationPdf(CreditApplication $application, string $basePath): string
    {
        $pdf = Pdf::loadView('credit-applications.authorization-pdf', [
            'application' => $application,
        ])->setPaper('a4');

        $path = $basePath . '/autorizacion-descuento.pdf';
        $fullPath = public_path($path);
        $this->ensurePublicDirectoryExists(dirname($fullPath));
        file_put_contents($fullPath, $pdf->output());

        return $path;
    }

    private function ensurePublicDirectoryExists(string $directoryPath): void
    {
        if (! is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }
    }
}
