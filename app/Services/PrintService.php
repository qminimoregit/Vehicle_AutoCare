<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\PrinterSetting;
use App\Models\PrintLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PrintService
{
    protected string $storagePath;

    protected string $certPath;

    protected string $keyPath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/qz');
        $this->certPath = $this->storagePath.'/digital-certificate.txt';
        $this->keyPath = $this->storagePath.'/private-key.pem';
    }

    /**
     * Get X.509 public digital certificate for QZ Tray authentication.
     */
    public function getCertificate(): string
    {
        $this->ensureKeysExist();

        if (File::exists($this->certPath)) {
            return File::get($this->certPath);
        }

        return '';
    }

    /**
     * Securely sign a payload string using the backend private key.
     * The private key is never exposed to the client or JavaScript.
     */
    public function signRequest(string $toSign): string
    {
        $this->ensureKeysExist();

        if (! File::exists($this->keyPath)) {
            throw new \RuntimeException('QZ Tray private signing key not available.');
        }

        $privateKeyContent = File::get($this->keyPath);
        $privateKey = openssl_pkey_get_private($privateKeyContent);

        if (! $privateKey) {
            throw new \RuntimeException('Invalid QZ Tray private key: '.openssl_error_string());
        }

        $signature = '';
        $success = openssl_sign($toSign, $signature, $privateKey, OPENSSL_ALGO_SHA512);

        if (! $success) {
            throw new \RuntimeException('Failed to sign QZ Tray request: '.openssl_error_string());
        }

        return base64_encode($signature);
    }

    /**
     * Ensure certificate and private key exist. If missing, generate standard RSA 2048 pair.
     */
    public function ensureKeysExist(): void
    {
        if (File::exists($this->certPath) && File::exists($this->keyPath)) {
            return;
        }

        if (! File::isDirectory($this->storagePath)) {
            File::makeDirectory($this->storagePath, 0755, true, true);
        }

        $openSslConfig = $this->getOpenSslConfig();

        $privateKey = openssl_pkey_new($openSslConfig);
        if (! $privateKey) {
            Log::error('OpenSSL Key Generation Error: '.openssl_error_string());
            throw new \RuntimeException('Failed to generate OpenSSL private key. Check OpenSSL configuration.');
        }

        $privateKeyOut = '';
        openssl_pkey_export($privateKey, $privateKeyOut, null, $openSslConfig);

        $dn = [
            'countryName' => 'LK',
            'stateOrProvinceName' => 'Western',
            'localityName' => 'Mattegoda',
            'organizationName' => 'Viran Auto Care',
            'organizationalUnitName' => 'AutoCare Printing Subsystem',
            'commonName' => 'Viran Auto Care Root Authority',
            'emailAddress' => 'admin@autocare.com',
        ];

        $csr = openssl_csr_new($dn, $privateKey, $openSslConfig);
        $certificate = openssl_csr_sign($csr, null, $privateKey, 3650, $openSslConfig);

        $certOut = '';
        openssl_x509_export($certificate, $certOut);

        File::put($this->keyPath, $privateKeyOut);
        File::put($this->certPath, $certOut);
    }

    /**
     * Resolve OpenSSL configuration path (handles Windows PHP differences).
     */
    protected function getOpenSslConfig(): array
    {
        $baseConfig = [
            'digest_alg' => 'sha512',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        // Test if default works
        $testKey = @openssl_pkey_new($baseConfig);
        if ($testKey) {
            return $baseConfig;
        }

        // Try candidate paths for openssl.cnf on Windows/Linux
        $candidates = [
            dirname(PHP_BINARY).'/extras/ssl/openssl.cnf',
            dirname(PHP_BINARY).'/openssl.cnf',
            'C:/php/extras/ssl/openssl.cnf',
            'C:/xampp/apache/bin/openssl.cnf',
            'C:/xampp/php/extras/ssl/openssl.cnf',
            'C:/Program Files/Git/usr/ssl/openssl.cnf',
            '/etc/ssl/openssl.cnf',
            '/etc/pki/tls/openssl.cnf',
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand)) {
                $baseConfig['config'] = $cand;
                $testKey = @openssl_pkey_new($baseConfig);
                if ($testKey) {
                    return $baseConfig;
                }
            }
        }

        return $baseConfig;
    }

    /**
     * Resolve printer mapping and auto-route decision for a given document.
     */
    public function resolvePrinter(string $documentType, ?int $branchId = null, ?string $workstationName = null): array
    {
        $setting = PrinterSetting::resolveFor($branchId, $workstationName);

        if (! $setting) {
            return [
                'configured' => false,
                'target_printer' => null,
                'printer_role' => null,
                'auto_route' => false,
                'paper_size' => '80mm',
                'workstation' => $workstationName ?? 'Default-Workstation',
                'message' => 'No printer configuration found for this workstation/branch.',
            ];
        }

        $targetPrinter = $setting->getPrinterForDocument($documentType);
        $role = $this->getDocumentRole($documentType);

        return [
            'configured' => ! empty($targetPrinter),
            'target_printer' => $targetPrinter,
            'printer_role' => $role,
            'bill_printer' => $setting->bill_printer,
            'job_card_printer' => $setting->job_card_printer,
            'auto_route' => (bool) $setting->auto_route,
            'paper_size' => $setting->paper_size ?? '80mm',
            'workstation' => $setting->workstation_name,
            'branch_id' => $setting->branch_id,
            'message' => empty($targetPrinter)
                ? ($role === 'bill' ? 'Bill Printer is not connected.' : 'Job Card Printer is not connected.')
                : 'Printer resolved successfully.',
        ];
    }

    /**
     * Get printer role ('bill' or 'job_card') from document type.
     */
    public function getDocumentRole(string $documentType): string
    {
        $doc = strtolower(trim($documentType));
        if (in_array($doc, [
            'bill',
            'direct_sale',
            'direct_sale_receipt',
            'invoice_80mm',
            'sales_invoice_80mm',
            'payment_receipt',
            'partial_payment_receipt',
            'advance_receipt',
            'advance_payment_receipt',
        ])) {
            return 'bill';
        }

        return 'job_card';
    }

    /**
     * Generate HTML test print slip for 80mm thermal printer.
     */
    public function generateTestSlipHtml(string $printerRole, ?string $printerName = null, ?string $workstationName = null, ?int $branchId = null): string
    {
        $company = CompanySetting::getSettings();
        $branch = $branchId ? Branch::find($branchId) : null;
        $isBill = strtolower($printerRole) === 'bill';
        $roleTitle = $isBill ? 'BILL PRINTER' : 'JOB CARD PRINTER';
        $roleScope = $isBill
            ? 'Bills, 80mm Invoices, POS Direct Sales, Payment Receipts'
            : 'Job Cards, Selected Services/Products, Job Instructions';

        $branchName = $branch?->name ?? ($company->company_name ?? 'VIRAN AUTO CARE');
        $branchAddress = $branch?->address ?? ($company->address ?? 'Mattegoda, Colombo');
        $branchPhone = $branch?->phone ?? ($company->phone ?? '011-2345678');
        $now = now()->format('Y-m-d H:i:s');

        $logoHtml = '';
        if (! empty($company->logo) && file_exists(public_path('storage/'.$company->logo))) {
            $logoHtml = '<img src="'.asset('storage/'.$company->logo).'" style="max-height: 48px; max-width: 140px; margin: 0 auto 6px auto; display: block;" alt="Logo" />';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Print - {$roleTitle}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Courier New", monospace; }
        body { background: #fff; color: #000; font-size: 11px; line-height: 1.35; padding: 0; width: 80mm; margin: 0 auto; }
        .receipt { width: 76mm; margin: 0 auto; padding: 6mm 2mm; }
        .center { text-align: center; }
        .bold { font-weight: 700; }
        .border-t { border-top: 1px dashed #000; margin: 6px 0; }
        .border-b { border-bottom: 1px dashed #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; font-size: 10.5px; margin-bottom: 3px; }
        .banner { background: #000; color: #fff; padding: 4px; text-align: center; font-weight: 900; font-size: 13px; letter-spacing: 0.5px; margin: 6px 0; }
        .grid-line { font-family: monospace; font-size: 9px; text-align: center; margin: 4px 0; }
        .cut-margin { height: 15mm; }
    </style>
</head>
<body>
    <div class="receipt">
        {$logoHtml}
        <div class="center bold" style="font-size: 13px; text-transform: uppercase;">{$branchName}</div>
        <div class="center" style="font-size: 9.5px; color: #333;">{$branchAddress}</div>
        <div class="center" style="font-size: 9.5px; color: #333;">Tel: {$branchPhone}</div>

        <div class="banner">*** {$roleTitle} TEST ***</div>

        <div class="border-t"></div>
        <div class="row"><span class="bold">Hardware Role:</span><span>{$roleTitle}</span></div>
        <div class="row"><span class="bold">Assigned Printer:</span><span>{$printerName}</span></div>
        <div class="row"><span class="bold">Workstation:</span><span>{$workstationName}</span></div>
        <div class="row"><span class="bold">Paper Size:</span><span>80mm Thermal</span></div>
        <div class="row"><span class="bold">Test Timestamp:</span><span>{$now}</span></div>
        <div class="border-b"></div>

        <div style="font-size: 10px; margin: 5px 0;">
            <div class="bold" style="margin-bottom: 2px;">Assigned Routing Scope:</div>
            <div style="color: #222;">{$roleScope}</div>
        </div>

        <div class="border-t"></div>
        <div class="center bold" style="font-size: 11px; margin: 4px 0;">[ HARDWARE STATUS: READY ]</div>
        <div class="grid-line">|---+---10---+---20---+---30---+---40---|</div>
        <div class="center" style="font-size: 9px;">80mm Width & Alignment Calibrated</div>
        <div class="border-b"></div>

        <div class="center" style="font-size: 9px; margin-top: 8px;">AutoCare System QZ Tray Silent Integration</div>
        <div class="cut-margin"></div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Record a print attempt, success, fallback or error.
     */
    public function logPrint(array $data): PrintLog
    {
        return PrintLog::create([
            'user_id' => $data['user_id'] ?? auth()->id(),
            'branch_id' => $data['branch_id'] ?? null,
            'workstation_name' => $data['workstation_name'] ?? null,
            'document_type' => $data['document_type'] ?? 'unknown',
            'document_id' => $data['document_id'] ?? null,
            'document_number' => $data['document_number'] ?? null,
            'printer_name' => $data['printer_name'] ?? null,
            'status' => $data['status'] ?? 'submitted',
            'error_message' => $data['error_message'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
        ]);
    }
}
