@props([
    'company' => null,
    'branch' => null,
])

@php
    $companySetting = $company ?? \App\Models\CompanySetting::getSettings();
    $companyName = trim($companySetting?->company_name ?? 'Viran Auto Care');

    // Prefer trimmed thermal logo for 80mm receipts
    if (file_exists(public_path('images/branding/viran_auto_care_logo_thermal.png'))) {
        $logoUrl = asset('images/branding/viran_auto_care_logo_thermal.png');
    } elseif ($companySetting && method_exists($companySetting, 'getThermalLogoUrl')) {
        $logoUrl = $companySetting->getThermalLogoUrl();
    } elseif ($companySetting && method_exists($companySetting, 'getLogoUrl')) {
        $logoUrl = $companySetting->getLogoUrl();
    } else {
        $logoUrl = asset('images/branding/viran_auto_care_logo.png');
    }

    // Resolve branch
    $activeBranch = $branch;
    if (!$activeBranch && $companySetting && method_exists($companySetting, 'mainBranch')) {
        $activeBranch = $companySetting->mainBranch;
    }

    // Clean branch name to remove duplicated company name
    $rawBranchName = trim($activeBranch?->branch_name ?? $activeBranch?->name ?? '');
    $branchSubtitle = '';

    if (!empty($rawBranchName)) {
        $clean = $rawBranchName;

        // Strip exact company name prefix (e.g. "Viran Auto Care Mattegoda" -> "Mattegoda")
        $pattern = '/^\s*' . preg_quote($companyName, '/') . '\s*[-–—:]*\s*/i';
        $clean = preg_replace($pattern, '', $clean);

        // Strip first word prefix if multi-word company (e.g. "Viran Mattegoda" -> "Mattegoda")
        $words = preg_split('/\s+/', $companyName);
        if (count($words) > 1 && !empty($words[0])) {
            $firstWord = preg_quote($words[0], '/');
            $clean = preg_replace('/^\s*' . $firstWord . '\s*[-–—:]*\s*/i', '', $clean);
        }

        // Strip remaining "AutoCare" or "Auto Care" prefix
        $clean = preg_replace('/^\s*Auto\s*Care\s*[-–—:]*\s*/i', '', $clean);

        // If the resulting string is not identical to company name and not empty, use it
        if (strcasecmp(trim($clean), $companyName) !== 0 && !empty(trim($clean))) {
            $branchSubtitle = trim($clean);
        }
    }

    $address = $activeBranch?->address ?: ($companySetting?->address ?? '');
    $phone = $activeBranch?->phone ?: ($companySetting?->phone ?? '');
@endphp

<div class="company-header">
    @if(!empty($logoUrl))
        <img src="{{ $logoUrl }}" alt="{{ $companyName }}" class="logo-img">
    @endif
    <div class="company-name">{{ strtoupper($companyName) }}</div>
    @if(!empty($branchSubtitle))
        <div class="branch-name">{{ strtoupper($branchSubtitle) }}</div>
    @endif
    @if(!empty($address))
        <div class="branch-meta">{{ $address }}</div>
    @endif
    @if(!empty($phone))
        <div class="branch-meta">Tel: {{ $phone }}</div>
    @endif
</div>
