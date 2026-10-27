<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\PrinterSetting;
use App\Models\PrintLog;
use App\Services\PrintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrinterSettingController extends Controller
{
    public function __construct(
        protected PrintService $printService
    ) {}

    /**
     * Display Printer Settings management interface.
     */
    public function index(Request $request): View
    {
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $settings = PrinterSetting::with('branch')
            ->orderBy('branch_id')
            ->orderBy('workstation_name')
            ->get();

        $recentLogs = PrintLog::with(['user', 'branch'])
            ->latest()
            ->limit(15)
            ->get();

        return view('admin.printer-settings.index', compact('branches', 'settings', 'recentLogs'));
    }

    /**
     * Store or update a printer mapping for a branch and workstation.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'workstation_name' => ['required', 'string', 'max:100'],
            'bill_printer' => ['nullable', 'string', 'max:255'],
            'job_card_printer' => ['nullable', 'string', 'max:255'],
            'paper_size' => ['required', 'in:80mm,A4'],
            'auto_route' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['auto_route'] = $request->boolean('auto_route', true);
        $validated['is_active'] = true;

        $setting = PrinterSetting::updateOrCreate(
            [
                'branch_id' => $validated['branch_id'] ?: null,
                'workstation_name' => trim($validated['workstation_name']),
            ],
            $validated
        );

        ActivityLog::log(
            action: 'updated_printer_settings',
            description: "Configured printer mapping for workstation '{$setting->workstation_name}' by ".Auth::user()->name.'.',
            module: 'Administration'
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Printer settings saved successfully.',
                'setting' => $setting->load('branch'),
            ]);
        }

        return back()->with('success', "Printer settings saved for '{$setting->workstation_name}'.");
    }

    /**
     * Update an existing printer setting.
     */
    public function update(Request $request, PrinterSetting $printerSetting): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'workstation_name' => ['required', 'string', 'max:100'],
            'bill_printer' => ['nullable', 'string', 'max:255'],
            'job_card_printer' => ['nullable', 'string', 'max:255'],
            'paper_size' => ['required', 'in:80mm,A4'],
            'auto_route' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $validated['auto_route'] = $request->boolean('auto_route', true);

        $printerSetting->update($validated);

        ActivityLog::log(
            action: 'updated_printer_settings',
            description: "Updated printer mapping for workstation '{$printerSetting->workstation_name}' by ".Auth::user()->name.'.',
            module: 'Administration'
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Printer settings updated successfully.',
                'setting' => $printerSetting->load('branch'),
            ]);
        }

        return back()->with('success', "Printer settings updated for '{$printerSetting->workstation_name}'.");
    }

    /**
     * Remove a printer setting mapping.
     */
    public function destroy(PrinterSetting $printerSetting): RedirectResponse|JsonResponse
    {
        $name = $printerSetting->workstation_name;
        $printerSetting->delete();

        ActivityLog::log(
            action: 'deleted_printer_setting',
            description: "Deleted printer mapping for workstation '{$name}' by ".Auth::user()->name.'.',
            module: 'Administration'
        );

        return back()->with('success', "Printer setting for '{$name}' deleted.");
    }

    /**
     * Serve QZ Tray digital certificate (public key).
     */
    public function getCertificate(): Response
    {
        $cert = $this->printService->getCertificate();

        return response($cert, 200, [
            'Content-Type' => 'text/plain',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Sign QZ Tray request using server private key.
     */
    public function sign(Request $request): JsonResponse
    {
        $toSign = $request->input('request') ?? $request->getContent();

        if (empty($toSign)) {
            return response()->json(['error' => 'No payload provided to sign.'], 400);
        }

        try {
            $signature = $this->printService->signRequest((string) $toSign);

            return response()->json([
                'signature' => $signature,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active printer routing configuration for the client workstation.
     */
    public function getActiveConfig(Request $request): JsonResponse
    {
        $branchId = $request->has('branch_id') ? (int) $request->input('branch_id') : null;
        $workstation = $request->input('workstation') ? trim($request->input('workstation')) : null;

        $resolved = $this->printService->resolvePrinter('bill', $branchId, $workstation);
        $jobResolved = $this->printService->resolvePrinter('job_card', $branchId, $workstation);

        return response()->json([
            'workstation' => $workstation ?? 'Default-Workstation',
            'branch_id' => $branchId,
            'bill_printer' => $resolved['bill_printer'] ?? null,
            'job_card_printer' => $jobResolved['job_card_printer'] ?? null,
            'paper_size' => $resolved['paper_size'] ?? '80mm',
            'auto_route' => (bool) ($resolved['auto_route'] ?? true),
        ]);
    }

    /**
     * Generate 80mm test print payload.
     */
    public function testPrint(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'printer_role' => ['required', 'in:bill,job_card'],
            'printer_name' => ['required', 'string', 'max:255'],
            'workstation_name' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $html = $this->printService->generateTestSlipHtml(
            printerRole: $validated['printer_role'],
            printerName: $validated['printer_name'],
            workstationName: $validated['workstation_name'] ?? 'Local Workstation',
            branchId: $validated['branch_id'] ?? null
        );

        $this->printService->logPrint([
            'branch_id' => $validated['branch_id'] ?? null,
            'workstation_name' => $validated['workstation_name'] ?? null,
            'document_type' => 'test_print',
            'printer_name' => $validated['printer_name'],
            'status' => 'submitted',
        ]);

        return response()->json([
            'success' => true,
            'printer_role' => $validated['printer_role'],
            'printer_name' => $validated['printer_name'],
            'html' => $html,
        ]);
    }

    /**
     * Client endpoint to log printing outcome or errors.
     */
    public function logStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:50'],
            'document_id' => ['nullable', 'integer'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'printer_name' => ['nullable', 'string', 'max:255'],
            'workstation_name' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['nullable', 'integer'],
            'status' => ['required', 'in:submitted,printed,failed,fallback_browser,cancelled'],
            'error_message' => ['nullable', 'string', 'max:1000'],
        ]);

        $log = $this->printService->logPrint($validated);

        return response()->json([
            'success' => true,
            'log_id' => $log->id,
        ]);
    }
}
