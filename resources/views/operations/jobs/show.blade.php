@extends('layouts.app')

@section('title', 'Job Card #' . $jobCard->job_number)

@section('content')
@php
    $customer = $jobCard->customer;
    $vehicle = $jobCard->vehicle;
    $vehicleType = $jobCard->vehicleType;
    $assignedTech = $jobCard->assignedTechnician;
    $status = $jobCard->status;

    // Status label and badge class
    $statusMap = [
        'pending' => ['label' => 'Pending', 'class' => 'status-badge pending', 'icon' => 'clock-history'],
        'in_progress' => ['label' => 'In Progress', 'class' => 'status-badge in-progress', 'icon' => 'gear-wide-connected'],
        'additional_work' => ['label' => 'Additional Work', 'class' => 'status-badge in-progress', 'icon' => 'tools'],
        'completed' => ['label' => 'Completed', 'class' => 'status-badge completed', 'icon' => 'check-circle-fill'],
        'delivered' => ['label' => 'Delivered', 'class' => 'status-badge delivered', 'icon' => 'check2-all'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'status-badge cancelled', 'icon' => 'x-circle-fill'],
    ];
    $curStatus = $statusMap[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'status-badge pending', 'icon' => 'clock'];

    $isReadOnly = in_array($status, ['completed', 'delivered', 'cancelled']);
    $totalCount = $jobCard->services->count() + $jobCard->products->count();
    $resolvedInvoice = $resolvedInvoice ?? $jobCard->getResolvedInvoice();
    $canViewInvoice = Auth::user()->isAdmin() || Auth::user()->hasPermission('view_invoices');

    // Map carpet categories
    $carpetCategories = $vehicleType ? $vehicleType->carpetCategories : collect();
    $existingCarpets = $jobCard->carpets->keyBy('carpet_category_id');

    // Phone normalization for WhatsApp
    $rawPhone = $customer?->phone ?? '';
    $normPhone = \App\Services\PhoneNumberHelper::normalizeForWhatsApp($rawPhone) ?? '';
    $waText = "Hello {$customer?->customer_name},\n\nUpdate regarding your vehicle {$vehicle?->vehicle_number} (Job #{$jobCard->job_number}):\nStatus: {$curStatus['label']}\nTotal Services & Parts: {$totalCount} items.\n\nThank you for choosing " . (config('app.name', 'Viran Auto Care')) . "!";

    // Prepare initial reactive cart payload
    $initialServices = $jobCard->services->map(function ($s) {
        return [
            'id' => $s->id,
            'service_id' => $s->service_id,
            'service_name' => $s->service_name,
            'service_code' => $s->service_code,
            'technician_name' => $s->technician_name,
            'quantity' => (float) $s->quantity,
            'unit_price' => (float) $s->unit_price,
            'line_total' => (float) $s->line_total,
            'is_price_custom' => (bool) $s->is_price_custom,
            'is_price_pending' => (bool) $s->is_price_pending,
            'notes' => $s->notes,
        ];
    })->values()->all();

    $initialProducts = $jobCard->products->map(function ($p) {
        return [
            'id' => $p->id,
            'product_id' => $p->product_id,
            'product_name' => $p->product_name,
            'product_sku' => $p->product_sku,
            'quantity' => (float) $p->quantity,
            'formatted_quantity' => \App\Services\QuantityFormatter::format((float) $p->quantity),
            'unit_price' => (float) $p->unit_price,
            'unit_cost' => (float) $p->unit_cost,
            'line_total' => (float) $p->line_total,
            'is_price_pending' => (bool) $p->is_price_pending,
            'notes' => $p->notes,
        ];
    })->values()->all();

    $initialTotals = [
        'service_total' => (float) $jobCard->service_total,
        'product_total' => (float) $jobCard->product_total,
        'discount_amount' => (float) $jobCard->discount_amount,
        'tax_amount' => (float) $jobCard->tax_amount,
        'grand_total' => (float) $jobCard->grand_total,
        'total_count' => count($initialServices) + count($initialProducts),
    ];

    $initialStocks = [];
    foreach ($availableProducts as $prod) {
        $initialStocks[$prod->id] = (float) $prod->available_stock;
    }
@endphp

<!-- External Google Font & Bootstrap Icons for 100% Lovable Design Fidelity -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* ==========================================================================
   LOVABLE JOB CARD EXACT DESIGN SYSTEM STYLES
   ========================================================================== */
:root {
    --lv-navy: #0D1946;
    --lv-navy-2: #172B63;
    --lv-blue: #2563EB;
    --lv-blue-soft: #EAF1FF;
    --lv-green: #059669;
    --lv-green-soft: #E7F7F1;
    --lv-bg: #F5F7FB;
    --lv-white: #ffffff;
    --lv-border: #E5EAF2;
    --lv-text: #111827;
    --lv-muted: #64748B;
    --lv-warn: #F59E0B;
    --lv-warn-soft: #FEF5E3;
    --lv-red: #DC2626;
    --lv-red-soft: #FDECEC;
    --lv-r: 14px;
    --lv-r-sm: 10px;
    --lv-shadow: 0 1px 3px rgba(13,25,70,.06), 0 6px 18px rgba(13,25,70,.05);
}

.lovable-job-card-wrap {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: var(--lv-text);
    background-color: var(--lv-bg);
    border-radius: 16px;
    padding-bottom: 24px;
}

/* Card Container */
.lv-card {
    background: #ffffff;
    border: 1px solid var(--lv-border);
    border-radius: var(--lv-r);
    box-shadow: var(--lv-shadow);
}

/* Header Top Bar */
.lv-crumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--lv-muted);
    margin-bottom: 14px;
}
.lv-crumbs a {
    color: var(--lv-muted);
    text-decoration: none;
    transition: color 0.15s;
}
.lv-crumbs a:hover {
    color: var(--lv-blue);
}
.lv-crumbs strong {
    color: var(--lv-text);
    font-weight: 700;
}

/* Job Header Card */
.lv-job-head {
    padding: 18px 22px;
    margin-bottom: 18px;
}
.lv-job-head-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
}
.lv-job-no {
    font-size: 15px;
    color: var(--lv-muted);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.lv-job-no strong {
    color: var(--lv-text);
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.3px;
}
.lv-job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 20px;
    margin-top: 8px;
    color: var(--lv-muted);
    font-size: 13.5px;
}
.lv-job-meta b {
    color: var(--lv-navy);
    font-weight: 700;
    letter-spacing: 0.3px;
}
.lv-job-meta i {
    margin-right: 4px;
    color: var(--lv-muted);
}
.lv-job-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

/* Status Badges */
.status-badge {
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.status-badge.pending {
    background: var(--lv-warn-soft);
    color: #B45309;
}
.status-badge.in-progress {
    background: var(--lv-blue-soft);
    color: var(--lv-blue);
}
.status-badge.completed, .status-badge.delivered {
    background: var(--lv-green-soft);
    color: var(--lv-green);
}
.status-badge.cancelled {
    background: var(--lv-red-soft);
    color: var(--lv-red);
}

/* Lovable Buttons */
.lv-btn {
    border-radius: 10px;
    font-weight: 600;
    font-size: 13.5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 40px;
    padding: 8px 14px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    border: 1px solid transparent;
    text-decoration: none;
    white-space: nowrap;
}
.lv-btn-sm {
    min-height: 34px;
    font-size: 12.5px;
    padding: 5px 11px;
}
.lv-btn-brand {
    background: #0B1849;
    color: #ffffff;
    border-color: #0B1849;
}
.lv-btn-brand:hover {
    background: #172B63;
    color: #ffffff;
    border-color: #172B63;
}
.lv-btn-blue {
    background: var(--lv-blue);
    color: #fff;
    border-color: var(--lv-blue);
}
.lv-btn-blue:hover {
    background: #1D4ED8;
    color: #fff;
}
.lv-btn-success {
    background: var(--lv-green);
    color: #fff;
    border-color: var(--lv-green);
}
.lv-btn-success:hover {
    background: #047857;
    color: #fff;
}
.lv-btn-soft {
    background: #ffffff;
    border-color: var(--lv-border);
    color: var(--lv-text);
}
.lv-btn-soft:hover {
    background: var(--lv-bg);
    border-color: #D3DBE8;
    color: var(--lv-text);
}
.lv-btn-danger {
    background: var(--lv-red);
    color: #fff;
}
.lv-btn-danger:hover {
    background: #B91C1C;
}

/* Main Workspace 2-Column Layout */
.lv-workspace {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.lv-catalog-col {
    flex: 1 1 0%;
    min-width: 0;
}
.lv-cart-col {
    width: 410px;
    flex-shrink: 0;
}

/* Section Tabs */
.lv-section-tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 16px;
    background: #fff;
    border: 1px solid var(--lv-border);
    border-radius: var(--lv-r);
    padding: 5px;
    overflow-x: auto;
    scrollbar-width: none;
}
.lv-section-tabs::-webkit-scrollbar { display: none; }
.lv-tab-btn {
    flex: 1;
    white-space: nowrap;
    border: 0;
    background: none;
    padding: 9px 14px;
    border-radius: 9px;
    font-weight: 600;
    font-size: 13.5px;
    color: var(--lv-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 42px;
    cursor: pointer;
    transition: all 0.15s;
}
.lv-tab-btn:hover {
    color: var(--lv-text);
    background: #F8FAFC;
}
.lv-tab-btn.active {
    background: var(--lv-navy);
    color: #fff;
}
.lv-pill {
    background: var(--lv-warn);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    border-radius: 10px;
    padding: 1px 7px;
}

/* Catalog Tools & Chips */
.lv-catalog-tools {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
}
.lv-search-box {
    flex: 1;
    position: relative;
}
.lv-search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--lv-muted);
    font-size: 15px;
}
.lv-search-box input {
    width: 100%;
    height: 48px;
    border: 1px solid var(--lv-border);
    border-radius: var(--lv-r);
    padding: 0 44px 0 42px;
    font-size: 14px;
    background: #fff;
    box-shadow: var(--lv-shadow);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.lv-search-box input:focus {
    border-color: var(--lv-blue);
    box-shadow: 0 0 0 3px var(--lv-blue-soft);
}
.lv-search-box kbd {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--lv-bg);
    color: var(--lv-muted);
    border: 1px solid var(--lv-border);
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: inherit;
}
.lv-view-toggle {
    display: flex;
    background: #fff;
    border: 1px solid var(--lv-border);
    border-radius: var(--lv-r);
    padding: 3px;
    box-shadow: var(--lv-shadow);
}
.lv-view-toggle button {
    border: 0;
    background: none;
    width: 42px;
    border-radius: 9px;
    color: var(--lv-muted);
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
}
.lv-view-toggle button.active {
    background: var(--lv-blue-soft);
    color: var(--lv-blue);
}

.lv-chips {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 14px;
    scrollbar-width: none;
}
.lv-chips::-webkit-scrollbar { display: none; }
.lv-chip {
    white-space: nowrap;
    border: 1px solid var(--lv-border);
    background: #fff;
    border-radius: 30px;
    padding: 7px 15px;
    font-weight: 600;
    font-size: 13px;
    color: var(--lv-text);
    min-height: 38px;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    transition: all 0.15s;
}
.lv-chip .n {
    font-size: 11px;
    color: var(--lv-muted);
    background: var(--lv-bg);
    border-radius: 10px;
    padding: 1px 7px;
}
.lv-chip:hover {
    border-color: #CBD5E1;
}
.lv-chip.active {
    background: var(--lv-navy);
    border-color: var(--lv-navy);
    color: #fff;
}
.lv-chip.active .n {
    background: rgba(255,255,255,.18);
    color: #fff;
}

/* Catalog Grid & Cards */
.lv-catalog.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(215px, 1fr));
    gap: 14px;
}
.lv-catalog.list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.lv-item {
    background: #fff;
    border: 1px solid var(--lv-border);
    border-radius: var(--lv-r);
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    transition: border-color .15s, box-shadow .15s, transform .15s;
    position: relative;
    box-shadow: var(--lv-shadow);
}
.lv-item:hover {
    border-color: #C7D5F5;
    box-shadow: 0 8px 20px rgba(13,25,70,.08);
}
.lv-item-top {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.lv-item-ico {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 19px;
    flex-shrink: 0;
}
.lv-item-ico.service {
    background: var(--lv-blue-soft);
    color: var(--lv-blue);
}
.lv-item-ico.product {
    background: #FFF3E0;
    color: #C2410C;
}
.lv-item-name {
    font-weight: 700;
    font-size: 13.5px;
    line-height: 1.25;
    color: var(--lv-text);
}
.lv-item-sub {
    font-size: 11.5px;
    color: var(--lv-muted);
    margin-top: 3px;
}
.lv-item-meta {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.lv-tag {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
    background: var(--lv-bg);
    color: var(--lv-muted);
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.lv-tag.ok {
    background: var(--lv-green-soft);
    color: var(--lv-green);
}
.lv-tag.low {
    background: var(--lv-warn-soft);
    color: #B45309;
}
.lv-tag.out {
    background: var(--lv-red-soft);
    color: var(--lv-red);
}
.lv-item-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: auto;
    padding-top: 4px;
}
.lv-price {
    font-weight: 800;
    font-size: 14.5px;
    color: var(--lv-navy);
}
.lv-price small {
    font-size: 10.5px;
    color: var(--lv-muted);
    font-weight: 600;
    margin-right: 2px;
}

/* List view variations */
.lv-catalog.list .lv-item {
    flex-direction: row;
    align-items: center;
    padding: 10px 14px;
}
.lv-catalog.list .lv-item-top {
    flex: 1;
    align-items: center;
}
.lv-catalog.list .lv-item-meta {
    width: 180px;
}
.lv-catalog.list .lv-item-foot {
    margin: 0;
    gap: 16px;
    padding: 0;
}

/* Current Job Cart (Lovable Style) */
.lv-cart {
    display: flex;
    flex-direction: column;
    height: auto;
}
.lv-cart-head {
    padding: 16px 18px;
    border-bottom: 1px solid var(--lv-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    background: #FAFBFD;
}
.lv-cart-head h2 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--lv-text);
}
.lv-cart-head .sub {
    font-size: 12px;
    color: var(--lv-muted);
    margin-top: 3px;
}
.lv-cart-head .cnt {
    background: var(--lv-blue);
    color: #fff;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
}
.lv-cart-body {
    padding: 8px 16px 14px;
}
.lv-cart-group {
    margin-top: 12px;
}
.lv-cart-group h4 {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: var(--lv-muted);
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
    padding: 0 4px;
}

/* Cart Line Items */
.lv-line {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 6px;
    border-bottom: 1px solid var(--lv-border);
    transition: background-color 0.15s;
}
.lv-line:hover {
    background-color: #FAFBFD;
}
.lv-line:last-child {
    border-bottom: 0;
}
.lv-line-chk {
    flex: 0 0 auto;
    width: 16px;
    height: 16px;
    border-radius: 4px;
    cursor: pointer;
    accent-color: var(--lv-blue);
}
.lv-line-main {
    flex: 1 1 auto;
    min-width: 0;
}
.lv-line-name {
    font-weight: 600;
    font-size: 13px;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--lv-text);
}
.lv-line-tech {
    font-size: 11px;
    color: var(--lv-muted);
    margin-top: 1px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.lv-line-qty {
    font-size: 12px;
    color: var(--lv-muted);
    font-weight: 600;
    background: var(--lv-bg);
    border-radius: 6px;
    padding: 2px 6px;
    flex-shrink: 0;
}
.lv-line-total {
    flex: 0 0 auto;
    text-align: right;
    min-width: 80px;
    font-weight: 700;
    font-size: 13px;
    color: var(--lv-navy);
    font-variant-numeric: tabular-nums;
}
.lv-rm-btn {
    flex: 0 0 auto;
    border: 0;
    background: none;
    color: #94A3B8;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s;
}
.lv-rm-btn:hover {
    background: var(--lv-red-soft);
    color: var(--lv-red);
}

/* Stepper Quantity Controls [-] [qty] [+] */
.lv-stepper {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--lv-border);
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
    flex-shrink: 0;
}
.lv-step-btn {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--lv-text);
    cursor: pointer;
    font-size: 11px;
    transition: background-color 0.15s, color 0.15s;
    padding: 0;
}
.lv-step-btn:hover:not(:disabled) {
    background: var(--lv-blue-soft);
    color: var(--lv-blue);
}
.lv-step-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
.lv-step-val {
    min-width: 22px;
    padding: 0 4px;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: var(--lv-text);
    font-variant-numeric: tabular-nums;
}
.lv-edit-btn {
    flex: 0 0 auto;
    border: 0;
    background: none;
    color: #94A3B8;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s;
}
.lv-edit-btn:hover {
    background: var(--lv-blue-soft);
    color: var(--lv-blue);
}

/* Floating Toast Notification */
.lv-toast {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 9999;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.25s ease;
}
.lv-toast.success {
    background: #064E3B;
    color: #ECFDF5;
    border: 1px solid #059669;
}
.lv-toast.error {
    background: #7F1D1D;
    color: #FEF2F2;
    border: 1px solid #DC2626;
}

/* Empty Cart State */
.lv-cart-empty {
    text-align: center;
    padding: 40px 14px;
    color: var(--lv-muted);
}
.lv-cart-empty .ic {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--lv-bg);
    display: grid;
    place-items: center;
    margin: 0 auto 12px;
    font-size: 24px;
    color: #9AA8C0;
}

/* Cart Footer & Totals */
.lv-cart-foot {
    border-top: 1px solid var(--lv-border);
    padding: 14px 18px 16px;
    background: #FBFCFE;
}
.lv-tot {
    font-size: 13px;
}
.lv-tot div {
    display: flex;
    justify-content: space-between;
    padding: 3px 0;
    color: var(--lv-muted);
}
.lv-tot div b {
    color: var(--lv-text);
    font-weight: 600;
}
.lv-tot .grand {
    border-top: 1px dashed var(--lv-border);
    margin-top: 8px;
    padding-top: 10px;
    color: var(--lv-navy);
    font-weight: 800;
    font-size: 17px;
}
.lv-tot .grand b {
    font-weight: 800;
    color: var(--lv-navy);
}

.lv-cart-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 14px;
}
.lv-cart-actions .main-action-btn {
    width: 100%;
    min-height: 44px;
    font-size: 14px;
    font-weight: 700;
}
.lv-cart-actions .lv-mini-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    align-items: stretch;
}
.lv-cart-actions .lv-mini-grid > * {
    min-width: 0;
}

/* Mobile Bottom Cart Bar */
.lv-cart-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 40;
    padding: 10px 14px calc(10px + env(safe-area-inset-bottom));
    background: linear-gradient(to top, rgba(245, 247, 251, 0.96) 75%, transparent);
}
.lv-cart-bar-btn {
    width: 100%;
    border: 0;
    background: var(--lv-navy);
    color: #fff;
    border-radius: 14px;
    min-height: 56px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 18px;
    box-shadow: 0 10px 25px rgba(13,25,70,.3);
    font-weight: 600;
    cursor: pointer;
}
.lv-cb-count {
    font-size: 13px;
    opacity: .9;
}
.lv-cb-total {
    font-weight: 800;
    font-size: 16px;
    margin-left: auto;
}
.lv-cb-view {
    background: var(--lv-blue);
    border-radius: 9px;
    padding: 6px 12px;
    font-size: 12.5px;
    font-weight: 700;
}

/* Vehicle & Issues Panels */
.lv-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
}
.lv-info-box {
    background: var(--lv-bg);
    border-radius: var(--lv-r-sm);
    padding: 12px 14px;
    border: 1px solid var(--lv-border);
}
.lv-info-box span {
    display: block;
    font-size: 11px;
    color: var(--lv-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 2px;
}
.lv-info-box b {
    font-size: 14px;
    color: var(--lv-text);
}

.lv-issues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 14px;
}
.lv-issue-card {
    padding: 16px;
    border: 1px solid var(--lv-border);
    border-radius: var(--lv-r);
    background: #fff;
    box-shadow: var(--lv-shadow);
}
.lv-issue-card h5 {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    color: var(--lv-text);
}
.lv-issue-rep {
    font-size: 13px;
    color: var(--lv-muted);
    margin: 6px 0 10px;
}
.lv-issue-notes {
    font-size: 12px;
    background: var(--lv-bg);
    border-radius: 8px;
    padding: 8px 10px;
    margin-top: 8px;
    color: var(--lv-muted);
}
.lv-issue-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 12px;
}

/* Modal Styling */
.lv-modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(13, 25, 70, 0.45);
    backdrop-filter: blur(4px);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.lv-modal-content {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(13,25,70,0.2);
    position: relative;
    z-index: 60;
}
.lv-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--lv-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.lv-modal-header h3 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    color: var(--lv-navy);
    display: flex;
    align-items: center;
    gap: 8px;
}
.lv-modal-body {
    padding: 20px;
}
.lv-modal-footer {
    padding: 14px 20px;
    border-top: 1px solid var(--lv-border);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    background: #FAFBFD;
}

/* Responsive Rules */
@media (max-width: 1199.98px) {
    .lv-workspace {
        flex-direction: column;
        gap: 24px;
        padding-bottom: 24px;
    }
    .lv-cart-col {
        display: block;
        width: 100%;
        max-width: 100%;
    }
}
@media (max-width: 767.98px) {
    .lv-job-head-top {
        flex-direction: column;
    }
    .lv-job-actions {
        width: 100%;
    }
    .lv-job-actions .lv-btn {
        flex: 1 1 auto;
    }
    .lv-catalog.grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
}
@media (max-width: 480px) {
    .lv-catalog.grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div 
    class="lovable-job-card-wrap"
    x-data="{
        // Tabs
        activeTab: 'catalog',
        catalogCat: 'all',
        searchTerm: '',
        viewMode: 'grid',

        // Offcanvas Mobile Cart
        mobileCartOpen: false,

        // Reactive Cart State
        cartServices: {{ Js::from($initialServices) }},
        cartProducts: {{ Js::from($initialProducts) }},
        totals: {{ Js::from($initialTotals) }},
        productStocks: {{ Js::from($initialStocks) }},

        // Selection for Selective Printing
        selectedServices: [{{ $jobCard->services->pluck('id')->implode(',') }}],
        selectedProducts: [{{ $jobCard->products->pluck('id')->implode(',') }}],

        // Modals
        showTechModal: false,
        showVehicleModal: false,
        showCarpetModal: false,
        showIssueModal: false,
        showApprovalModal: false,
        showWhatsAppModal: false,
        showEditItemModal: false,

        // Edit Cart Item State
        editingItem: {
            type: 'service',
            id: null,
            title: '',
            quantity: 1,
            technician_name: '',
            unit_price: 0,
            notes: '',
            is_price_custom: false,
            is_price_pending: false
        },
        highlightPending: false,

        // Issue Approval State
        selectedIssueId: null,
        selectedIssueDesc: '',

        // More dropdown
        moreOpen: false,

        // Loading state and toast
        loadingAction: null,
        toast: {
            show: false,
            message: '',
            type: 'success',
            timer: null
        },

        showNotification(msg, type = 'success') {
            if (this.toast.timer) clearTimeout(this.toast.timer);
            let displayMsg = msg || '';
            if (typeof displayMsg === 'string' && (
                displayMsg.includes('SQLSTATE') ||
                displayMsg.includes('Connection:') ||
                displayMsg.includes('SELECT ') ||
                displayMsg.includes('Unknown column') ||
                displayMsg.includes('syntax error')
            )) {
                displayMsg = 'A system error occurred. Please try again or contact support.';
            }
            this.toast.message = displayMsg;
            this.toast.type = type;
            this.toast.show = true;
            this.toast.timer = setTimeout(() => {
                this.toast.show = false;
            }, 3200);
        },

        formatMoney(val) {
            return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        isServiceInCart(serviceId) {
            return this.cartServices.some(s => s.service_id == serviceId);
        },

        isProductInCart(productId) {
            return this.cartProducts.some(p => p.product_id == productId);
        },

        getProductStock(productId) {
            return this.productStocks[productId] !== undefined ? this.productStocks[productId] : 0;
        },

        applyCartPayload(data) {
            if (data && data.cart) {
                if (Array.isArray(data.cart.services)) {
                    this.cartServices = data.cart.services;
                }
                if (Array.isArray(data.cart.products)) {
                    this.cartProducts = data.cart.products;
                }
                if (data.cart.totals && typeof data.cart.totals === 'object') {
                    this.totals = data.cart.totals;
                }
                if (data.cart.product_stocks && typeof data.cart.product_stocks === 'object') {
                    this.productStocks = data.cart.product_stocks;
                }
                this.selectedServices = this.cartServices.map(s => s.id);
                this.selectedProducts = this.cartProducts.map(p => p.id);
            }
        },

        async directAddService(serviceId, serviceName, configuredPrice) {
            @if($isReadOnly)
                this.showNotification('Job card is finished or cancelled. Cannot add items.', 'error');
                return;
            @endif

            if (this.loadingAction) return;

            const actionKey = 'srv_' + serviceId;
            this.loadingAction = actionKey;

            try {
                const resp = await fetch('{{ route('operations.job-cards.services.add', $jobCard) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        service_id: serviceId,
                        quantity: 1
                    })
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to add service');
                }

                this.applyCartPayload(data);
                this.showNotification(data.message || ('Service \'' + serviceName + '\' added to Job Card.'), 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error adding service', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        async directAddProduct(productId, productName) {
            @if($isReadOnly)
                this.showNotification('Job card is finished or cancelled. Cannot add items.', 'error');
                return;
            @endif

            if (this.loadingAction) return;

            const stock = this.getProductStock(productId);
            if (stock <= 0) {
                this.showNotification('\'' + productName + '\' is out of stock.', 'error');
                return;
            }

            const actionKey = 'prod_' + productId;
            this.loadingAction = actionKey;

            try {
                const resp = await fetch('{{ route('operations.job-cards.products.add', $jobCard) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to add product');
                }

                this.applyCartPayload(data);
                this.showNotification(data.message || ('Product \'' + productName + '\' added to Job Card.'), 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error adding product', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        async changeServiceQty(srvItem, action) {
            @if($isReadOnly)
                return;
            @endif
            if (this.loadingAction) return;

            const actionKey = 'srv_qty_' + srvItem.id;
            this.loadingAction = actionKey;

            try {
                const url = '{{ url('/operations/job-cards/' . $jobCard->id . '/services') }}/' + srvItem.id + '/quantity';
                const resp = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: action })
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to update quantity');
                }

                this.applyCartPayload(data);
                this.showNotification(data.message, 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error updating quantity', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        async changeProductQty(prodItem, action) {
            @if($isReadOnly)
                return;
            @endif
            if (this.loadingAction) return;

            const actionKey = 'prod_qty_' + prodItem.id;
            this.loadingAction = actionKey;

            try {
                const url = '{{ url('/operations/job-cards/' . $jobCard->id . '/products') }}/' + prodItem.id + '/quantity';
                const resp = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: action })
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to update quantity');
                }

                this.applyCartPayload(data);
                this.showNotification(data.message, 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error updating quantity', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        async removeServiceItem(srvItem) {
            @if($isReadOnly)
                return;
            @endif
            if (!confirm('Remove service \'' + srvItem.service_name + '\' from Job Card?')) {
                return;
            }
            if (this.loadingAction) return;

            const actionKey = 'rm_srv_' + srvItem.id;
            this.loadingAction = actionKey;

            try {
                const url = '{{ url('/operations/job-cards/' . $jobCard->id . '/services') }}/' + srvItem.id;
                const resp = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to remove service');
                }

                this.applyCartPayload(data);
                this.showNotification(data.message, 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error removing service', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        async removeProductItem(prodItem) {
            @if($isReadOnly)
                return;
            @endif
            if (!confirm('Remove part \'' + prodItem.product_name + '\' and return stock?')) {
                return;
            }
            if (this.loadingAction) return;

            const actionKey = 'rm_prod_' + prodItem.id;
            this.loadingAction = actionKey;

            try {
                const url = '{{ url('/operations/job-cards/' . $jobCard->id . '/products') }}/' + prodItem.id;
                const resp = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to remove product');
                }

                this.applyCartPayload(data);
                this.showNotification(data.message, 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error removing product', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        hasPendingPrices() {
            const pendingServices = this.cartServices.some(s => !!s.is_price_pending || s.unit_price === null || Number(s.unit_price) < 0);
            const pendingProducts = this.cartProducts.some(p => !!p.is_price_pending || p.unit_price === null || Number(p.unit_price) < 0);
            return pendingServices || pendingProducts;
        },

        submitCompleteJob(event) {
            if (this.hasPendingPrices()) {
                this.highlightPending = true;
                this.showNotification('Some items do not have prices. Please assign prices before completing the job.', 'error');
                return;
            }

            if (this.cartServices.length === 0 && this.cartProducts.length === 0) {
                this.showNotification('Please add at least one service or product before completing the job.', 'error');
                return;
            }

            if (!confirm('Complete this job card? Bill will be generated automatically for the Cashier.')) {
                return;
            }

            event.target.submit();
        },

        openEditItem(item, type) {
            this.editingItem = {
                type: type,
                id: item.id,
                title: type === 'service' ? item.service_name : item.product_name,
                quantity: item.quantity,
                technician_name: item.technician_name || '',
                unit_price: item.is_price_pending ? '' : item.unit_price,
                notes: item.notes || '',
                is_price_custom: !!item.is_price_custom,
                is_price_pending: !!item.is_price_pending
            };
            this.showEditItemModal = true;
        },

        async saveEditItem() {
            if (!this.editingItem.id) return;

            if (this.editingItem.is_price_pending) {
                if (this.editingItem.unit_price === '' || this.editingItem.unit_price === null || isNaN(this.editingItem.unit_price) || Number(this.editingItem.unit_price) < 0) {
                    this.showNotification('Please enter a valid selling price.', 'error');
                    return;
                }
            }

            this.loadingAction = 'save_edit';

            try {
                const url = this.editingItem.type === 'service'
                    ? '{{ url('/operations/job-cards/' . $jobCard->id . '/services') }}/' + this.editingItem.id
                    : '{{ url('/operations/job-cards/' . $jobCard->id . '/products') }}/' + this.editingItem.id;

                const payload = {
                    notes: this.editingItem.notes
                };
                if (this.editingItem.type === 'service') {
                    payload.technician_name = this.editingItem.technician_name;
                    if (this.editingItem.unit_price !== '' && this.editingItem.unit_price !== null) {
                        payload.custom_price = Number(this.editingItem.unit_price);
                    }
                } else {
                    if (this.editingItem.unit_price !== '' && this.editingItem.unit_price !== null) {
                        payload.unit_price = Number(this.editingItem.unit_price);
                    }
                }

                const resp = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await resp.json();
                if (!resp.ok || !data.success) {
                    throw new Error(data.message || 'Failed to update item');
                }

                this.applyCartPayload(data);
                this.showEditItemModal = false;
                this.showNotification(data.message, 'success');
            } catch (err) {
                this.showNotification(err.message || 'Error saving changes', 'error');
            } finally {
                this.loadingAction = null;
            }
        },

        // Print Functions
        printJobCard() {
            const url = '{{ route('operations.job-cards.print', $jobCard) }}';
            if (window.AutoCarePrinter && window.AutoCarePrinter.currentConfig?.auto_route) {
                window.AutoCarePrinter.printUrl(url, {
                    type: 'job_card',
                    title: 'Job Card #{{ $jobCard->job_number }}',
                    documentId: {{ $jobCard->id }},
                    documentNumber: '{{ $jobCard->job_number }}',
                    fallbackUrl: url
                });
            } else {
                window.open(url, '_blank');
            }
        },

        printSelectedItems() {
            if (this.selectedServices.length === 0 && this.selectedProducts.length === 0) {
                this.showNotification('Please tick the checkboxes on items you want to print.', 'error');
                return;
            }
            const params = new URLSearchParams();
            params.append('selected', '1');
            this.selectedServices.forEach(id => params.append('services[]', id));
            this.selectedProducts.forEach(id => params.append('products[]', id));
            const url = '{{ route('operations.job-cards.print', $jobCard) }}?' + params.toString();
            if (window.AutoCarePrinter && window.AutoCarePrinter.currentConfig?.auto_route) {
                window.AutoCarePrinter.printUrl(url, {
                    type: 'job_card',
                    title: 'Selected Items - Job #{{ $jobCard->job_number }}',
                    documentId: {{ $jobCard->id }},
                    documentNumber: '{{ $jobCard->job_number }}',
                    fallbackUrl: url
                });
            } else {
                window.open(url, '_blank');
            }
        },

        openApproval(id, desc) {
            this.selectedIssueId = id;
            this.selectedIssueDesc = desc;
            this.showApprovalModal = true;
        }
    }"
    @keydown.escape.window="
        showTechModal = false;
        showVehicleModal = false;
        showCarpetModal = false;
        showIssueModal = false;
        showApprovalModal = false;
        showWhatsAppModal = false;
        showEditItemModal = false;
        mobileCartOpen = false;
        moreOpen = false;
    "
>
    <!-- Top Breadcrumb Bar -->
    <div class="lv-crumbs">
        <a href="{{ route('operations.job-cards.index') }}">Operations</a>
        <i class="bi bi-chevron-right text-xs"></i>
        <a href="{{ route('operations.job-cards.index') }}">Job Cards</a>
        <i class="bi bi-chevron-right text-xs"></i>
        <strong>{{ $jobCard->job_number }}</strong>
    </div>

    <!-- 1. JOB HEADER (Exact Lovable Layout) -->
    <div class="lv-card lv-job-head">
        <div class="lv-job-head-top">
            <div>
                <div class="lv-job-no">
                    Job Card <strong>#{{ $jobCard->job_number }}</strong>
                    <span class="{{ $curStatus['class'] }}">
                        <i class="bi bi-{{ $curStatus['icon'] }}"></i> {{ $curStatus['label'] }}
                    </span>
                </div>
                <div class="lv-job-meta">
                    <span><i class="bi bi-car-front"></i> <b>{{ $vehicle?->vehicle_number ?? 'Vehicle' }}</b></span>
                    <span><i class="bi bi-person"></i> {{ $customer?->customer_name ?? 'Walk-in Customer' }}</span>
                    <span><i class="bi bi-speedometer2"></i> {{ number_format($jobCard->current_mileage) }} km</span>
                    <span><i class="bi bi-calendar3"></i> {{ $jobCard->created_at->format('d M Y') }}</span>
                    <span><i class="bi bi-person-gear"></i> Tech: <b>{{ $assignedTech?->name ?? 'Unassigned' }}</b></span>
                </div>
            </div>

            <!-- Primary Action Buttons Neatly on the RIGHT -->
            <div class="lv-job-actions">
                <!-- Assign Technician Button -->
                <button 
                    type="button" 
                    @click="showTechModal = true" 
                    class="lv-btn lv-btn-soft"
                    title="Assign or change technician"
                >
                    <i class="bi bi-person-gear text-blue-600"></i>
                    <span>{{ $assignedTech ? 'Change Tech' : 'Assign Tech' }}</span>
                </button>

                <!-- Status Transition Action Button (Direct Complete Job) -->
                @if($status === 'pending' || $status === 'in_progress' || $status === 'additional_work')
                    <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="inline" @submit.prevent="submitCompleteJob($event)">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="complete_job">
                        <button type="submit" class="lv-btn lv-btn-success shadow-xs">
                            <i class="bi bi-check2-circle"></i>
                            <span>Complete Job</span>
                        </button>
                    </form>
                @elseif($status === 'completed' || $status === 'delivered')
                    <span class="lv-btn lv-btn-soft text-emerald-700 border-emerald-200 bg-emerald-50 cursor-default">
                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                        <span>Completed</span>
                    </span>

                    @if($canViewInvoice)
                        @if($resolvedInvoice)
                            <a 
                                href="{{ route('operations.invoices.show', $resolvedInvoice) }}" 
                                class="lv-btn lv-btn-brand shadow-xs"
                                title="Open Invoice #{{ $resolvedInvoice->invoice_number }}"
                            >
                                <i class="bi bi-receipt"></i>
                                <span>Go to Invoice</span>
                            </a>
                        @else
                            <button 
                                type="button" 
                                @click="showNotification('Invoice not available for this completed job. Please contact the cashier or administrator.', 'error')" 
                                class="lv-btn lv-btn-brand opacity-80 shadow-xs"
                                title="Invoice not available"
                            >
                                <i class="bi bi-receipt"></i>
                                <span>Go to Invoice</span>
                            </button>
                        @endif
                    @else
                        <span class="lv-btn lv-btn-soft text-blue-700 border-blue-200 bg-blue-50 cursor-default" title="Job completed and handed off for Cashier billing">
                            <i class="bi bi-shield-check text-blue-600"></i>
                            <span>Billing Ready</span>
                        </span>
                    @endif
                @endif

                <!-- Print Job Card (Full) -->
                <button 
                    type="button" 
                    @click="printJobCard()" 
                    class="lv-btn lv-btn-soft" 
                    title="Print Complete Job Card"
                >
                    <i class="bi bi-printer"></i>
                    <span class="hidden sm:inline">Print Job Card</span>
                </button>

                <!-- Print Selected Items -->
                <button 
                    type="button" 
                    @click="printSelectedItems()" 
                    class="lv-btn lv-btn-soft" 
                    title="Print Selected Items Only"
                >
                    <i class="bi bi-check2-square text-indigo-600"></i>
                    <span class="hidden sm:inline">Print Selected</span>
                </button>

                <!-- Secondary Functions Inside 'More' Dropdown -->
                <div class="relative inline-block text-left" @click.away="moreOpen = false">
                    <button 
                        type="button" 
                        @click="moreOpen = !moreOpen" 
                        class="lv-btn lv-btn-soft"
                        aria-label="More actions"
                    >
                        <i class="bi bi-three-dots"></i>
                        <span class="hidden md:inline">More</span>
                    </button>

                    <div 
                        x-show="moreOpen" 
                        x-transition 
                        class="absolute right-0 mt-2 w-56 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 z-50 text-xs"
                        style="display: none;"
                    >
                        <div class="py-1">
                            <button 
                                type="button" 
                                @click="showVehicleModal = true; moreOpen = false;" 
                                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
                            >
                                <i class="bi bi-car-front text-gray-500"></i> Edit Vehicle & Mileage
                            </button>
                            <button 
                                type="button" 
                                @click="showCarpetModal = true; moreOpen = false;" 
                                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
                            >
                                <i class="bi bi-layers text-gray-500"></i> Edit Carpet Quantities
                            </button>
                            <button 
                                type="button" 
                                @click="showIssueModal = true; moreOpen = false;" 
                                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
                            >
                                <i class="bi bi-exclamation-triangle text-amber-500"></i> Record Vehicle Issue
                            </button>
                        </div>
                        <div class="py-1">
                            <a 
                                href="{{ route('operations.job-cards.print-bill', $jobCard) }}" 
                                target="_blank" 
                                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium no-underline"
                            >
                                <i class="bi bi-receipt text-gray-500"></i> Vehicle Intake Form
                            </a>
                            <button 
                                type="button" 
                                @click="showWhatsAppModal = true; moreOpen = false;" 
                                class="w-full text-left px-4 py-2.5 text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
                            >
                                <i class="bi bi-whatsapp text-emerald-600"></i> WhatsApp Job Summary
                            </button>
                        </div>
                        @if($status === 'completed' && Auth::user()->hasPermission('mark_delivered'))
                            <div class="py-1">
                                <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" onsubmit="return confirm('Confirm vehicle delivery to customer?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="mark_delivered">
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-emerald-700 hover:bg-emerald-50 flex items-center gap-2 font-semibold">
                                        <i class="bi bi-box-arrow-right"></i> Mark Delivered
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. TWO-COLUMN WORKSPACE -->
    <div class="lv-workspace">
        <!-- LEFT COLUMN: Catalog / Tabs (Add Items, Vehicle, Issues) -->
        <div class="lv-catalog-col">
            <!-- Section Tabs -->
            <div class="lv-section-tabs">
                <button 
                    type="button" 
                    :class="{ 'active': activeTab === 'catalog' }" 
                    @click="activeTab = 'catalog'" 
                    class="lv-tab-btn"
                >
                    <i class="bi bi-grid-fill"></i> Add Items
                </button>
                <button 
                    type="button" 
                    :class="{ 'active': activeTab === 'vehicle' }" 
                    @click="activeTab = 'vehicle'" 
                    class="lv-tab-btn"
                >
                    <i class="bi bi-car-front-fill"></i> Vehicle & Customer
                </button>
                <button 
                    type="button" 
                    :class="{ 'active': activeTab === 'issues' }" 
                    @click="activeTab = 'issues'" 
                    class="lv-tab-btn"
                >
                    <i class="bi bi-exclamation-triangle-fill"></i> Issues 
                    <span class="lv-pill">{{ $jobCard->issues->count() }}</span>
                </button>
            </div>

            <!-- TAB 1: CATALOG (Add Items) -->
            <div x-show="activeTab === 'catalog'" x-transition>
                <!-- Search & View Toggle -->
                <div class="lv-catalog-tools">
                    <div class="lv-search-box">
                        <i class="bi bi-search"></i>
                        <input 
                            type="search" 
                            x-model="searchTerm" 
                            placeholder="Search services, spare parts, SKU, or description..." 
                            id="lvCatalogSearch"
                        >
                        <kbd class="hidden md:inline">/</kbd>
                    </div>
                    <div class="lv-view-toggle">
                        <button 
                            type="button" 
                            :class="{ 'active': viewMode === 'grid' }" 
                            @click="viewMode = 'grid'" 
                            title="Grid view"
                        >
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button 
                            type="button" 
                            :class="{ 'active': viewMode === 'list' }" 
                            @click="viewMode = 'list'" 
                            title="List view"
                        >
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                </div>

                <!-- Category Chips -->
                <div class="lv-chips">
                    <button 
                        type="button" 
                        class="lv-chip" 
                        :class="{ 'active': catalogCat === 'all' }" 
                        @click="catalogCat = 'all'"
                    >
                        <i class="bi bi-grid"></i> All Items 
                        <span class="n">{{ $availableServices->count() + $availableProducts->count() }}</span>
                    </button>
                    <button 
                        type="button" 
                        class="lv-chip" 
                        :class="{ 'active': catalogCat === 'services' }" 
                        @click="catalogCat = 'services'"
                    >
                        <i class="bi bi-tools"></i> Services 
                        <span class="n">{{ $availableServices->count() }}</span>
                    </button>
                    <button 
                        type="button" 
                        class="lv-chip" 
                        :class="{ 'active': catalogCat === 'parts' }" 
                        @click="catalogCat = 'parts'"
                    >
                        <i class="bi bi-box-seam"></i> Spare Parts 
                        <span class="n">{{ $availableProducts->count() }}</span>
                    </button>
                </div>

                <!-- Catalog Grid / List -->
                <div :class="'lv-catalog ' + viewMode">
                    <!-- SERVICES -->
                    @foreach($availableServices as $srv)
                        @php
                            $srvPrice = $srv->prices->where('vehicle_type_id', $jobCard->vehicle_type_id)->first()?->service_price;
                        @endphp
                        <article 
                            class="lv-item"
                            x-show="(catalogCat === 'all' || catalogCat === 'services') && (!searchTerm || '{{ strtolower(addslashes($srv->name . ' ' . $srv->code . ' ' . $srv->description)) }}'.includes(searchTerm.toLowerCase()))"
                        >
                            <div class="lv-item-top">
                                <div class="lv-item-ico service">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="lv-item-name">{{ $srv->name }}</div>
                                    <div class="lv-item-sub">{{ $srv->code }} · {{ $srv->category?->name ?? 'Standard' }}</div>
                                </div>
                            </div>
                            <div class="lv-item-meta">
                                <span class="lv-tag"><i class="bi bi-wrench-adjustable"></i> Service</span>
                                <span class="lv-tag ok" x-show="isServiceInCart({{ $srv->id }})">
                                    <i class="bi bi-check2"></i> In Job
                                </span>
                            </div>
                            <div class="lv-item-foot">
                                <div class="lv-price">
                                    @if($srvPrice !== null)
                                        <small>LKR</small> {{ number_format($srvPrice, 2) }}
                                    @else
                                        <small class="text-amber-600 font-normal">Custom/Varies</small>
                                    @endif
                                </div>
                                <button 
                                    type="button" 
                                    @click="directAddService({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ $srvPrice !== null ? $srvPrice : 'null' }})" 
                                    class="lv-btn lv-btn-sm lv-btn-blue"
                                    :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}"
                                >
                                    <template x-if="loadingAction === 'srv_{{ $srv->id }}'">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="inline-block animate-spin border-2 border-white border-t-transparent rounded-full w-3 h-3"></span> Adding...
                                        </span>
                                    </template>
                                    <template x-if="loadingAction !== 'srv_{{ $srv->id }}'">
                                        <span class="inline-flex items-center gap-1">
                                            <i class="bi bi-plus-lg"></i> Add Service
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </article>
                    @endforeach

                    <!-- SPARE PARTS / PRODUCTS -->
                    @foreach($availableProducts as $prod)
                        <article 
                            class="lv-item"
                            :class="getProductStock({{ $prod->id }}) <= 0 ? 'opacity-60' : ''"
                            x-show="(catalogCat === 'all' || catalogCat === 'parts') && (!searchTerm || '{{ strtolower(addslashes($prod->product_name . ' ' . $prod->sku . ' ' . $prod->barcode . ' ' . $prod->description)) }}'.includes(searchTerm.toLowerCase()))"
                        >
                            <div class="lv-item-top">
                                <div class="lv-item-ico product">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="lv-item-name">{{ $prod->product_name }}</div>
                                    <div class="lv-item-sub">{{ $prod->sku }} · {{ $prod->brand ?? 'AutoCare' }}</div>
                                </div>
                            </div>
                            <div class="lv-item-meta">
                                <span class="lv-tag out" x-show="getProductStock({{ $prod->id }}) <= 0">
                                    <i class="bi bi-x-circle"></i> Out of stock
                                </span>
                                <span class="lv-tag low" x-show="getProductStock({{ $prod->id }}) > 0 && getProductStock({{ $prod->id }}) <= 5">
                                    <i class="bi bi-exclamation-circle"></i> Only <span x-text="getProductStock({{ $prod->id }})"></span> left
                                </span>
                                <span class="lv-tag ok" x-show="getProductStock({{ $prod->id }}) > 5">
                                    <i class="bi bi-check-circle"></i> <span x-text="getProductStock({{ $prod->id }})"></span> in stock
                                </span>
                                <span class="lv-tag ok" x-show="isProductInCart({{ $prod->id }})">
                                    <i class="bi bi-check2"></i> In Job
                                </span>
                                <span class="lv-tag">Part</span>
                            </div>
                            <div class="lv-item-foot">
                                <div class="lv-price">
                                    @if($prod->selling_price !== null && $prod->selling_price > 0)
                                        <small>LKR</small> {{ number_format($prod->selling_price, 2) }}
                                    @else
                                        <small class="text-amber-600 font-semibold">Price Pending</small>
                                    @endif
                                </div>
                                <button 
                                    type="button" 
                                    @click="directAddProduct({{ $prod->id }}, '{{ addslashes($prod->product_name) }}')" 
                                    class="lv-btn lv-btn-sm lv-btn-brand"
                                    :disabled="loadingAction !== null || getProductStock({{ $prod->id }}) <= 0 || {{ $isReadOnly ? 'true' : 'false' }}"
                                >
                                    <template x-if="loadingAction === 'prod_{{ $prod->id }}'">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="inline-block animate-spin border-2 border-white border-t-transparent rounded-full w-3 h-3"></span> Adding...
                                        </span>
                                    </template>
                                    <template x-if="loadingAction !== 'prod_{{ $prod->id }}'">
                                        <span class="inline-flex items-center gap-1">
                                            <i :class="getProductStock({{ $prod->id }}) <= 0 ? 'bi bi-slash-circle' : 'bi bi-plus-lg'"></i>
                                            <span x-text="getProductStock({{ $prod->id }}) <= 0 ? 'Out of Stock' : 'Issue Part'"></span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <!-- TAB 2: VEHICLE & CUSTOMER DETAILS -->
            <div x-show="activeTab === 'vehicle'" x-transition style="display: none;">
                <div class="lv-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-extrabold text-base text-[#0D1946] flex items-center gap-2 m-0">
                            <i class="bi bi-car-front text-blue-600"></i> Vehicle & Customer Information
                        </h4>
                        <div class="flex gap-2">
                            <button type="button" @click="showVehicleModal = true" class="lv-btn lv-btn-sm lv-btn-soft">
                                <i class="bi bi-pencil"></i> Edit Details
                            </button>
                            <button type="button" @click="showCarpetModal = true" class="lv-btn lv-btn-sm lv-btn-soft">
                                <i class="bi bi-layers"></i> Edit Carpets
                            </button>
                        </div>
                    </div>

                    <div class="lv-info-grid">
                        <div class="lv-info-box">
                            <span>Vehicle Number</span>
                            <b>{{ $vehicle?->vehicle_number ?? '—' }}</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Vehicle Type</span>
                            <b>{{ $vehicleType?->name ?? 'Standard' }}</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Customer Name</span>
                            <b>{{ $customer?->customer_name ?? '—' }}</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Phone Number</span>
                            <b>{{ $customer?->phone ?? '—' }}</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Starting Mileage</span>
                            <b>{{ number_format($jobCard->current_mileage) }} km</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Next Service Mileage</span>
                            <b>{{ $jobCard->next_service_mileage ? number_format($jobCard->next_service_mileage) . ' km' : '—' }}</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Assigned Technician</span>
                            <b>{{ $assignedTech?->name ?? 'Unassigned' }}</b>
                        </div>
                        <div class="lv-info-box">
                            <span>Branch</span>
                            <b>{{ $jobCard->branch?->name ?? 'Main Branch' }}</b>
                        </div>
                    </div>

                    <!-- Carpet Section Info -->
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                                <i class="bi bi-layers text-indigo-600"></i> Carpet Inspection Details
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @forelse($jobCard->carpets as $carpet)
                                <div class="px-3 py-2 bg-indigo-50/60 border border-indigo-100 rounded-lg text-xs font-medium text-indigo-900 flex items-center gap-2">
                                    <span>{{ $carpet->category_name }}:</span>
                                    <strong class="text-indigo-950 font-bold text-sm">{{ $carpet->quantity }}</strong>
                                </div>
                            @empty
                                <div class="text-xs text-gray-400 italic">No carpet records logged for this vehicle intake.</div>
                            @endforelse
                        </div>
                    </div>

                    @if($jobCard->service_notes || $jobCard->customer_notes)
                        <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            @if($jobCard->service_notes)
                                <div class="bg-amber-50/70 border border-amber-200 rounded-lg p-3">
                                    <span class="font-bold text-amber-900 block mb-1">Technician Notes:</span>
                                    <p class="text-amber-800 m-0">{{ $jobCard->service_notes }}</p>
                                </div>
                            @endif
                            @if($jobCard->customer_notes)
                                <div class="bg-blue-50/70 border border-blue-200 rounded-lg p-3">
                                    <span class="font-bold text-blue-900 block mb-1">Customer Requests / Instructions:</span>
                                    <p class="text-blue-800 m-0">{{ $jobCard->customer_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAB 3: VEHICLE ISSUES & ADDITIONAL WORK -->
            <div x-show="activeTab === 'issues'" x-transition style="display: none;">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-extrabold text-base text-[#0D1946] flex items-center gap-2 m-0">
                        <i class="bi bi-exclamation-triangle text-amber-500"></i> Vehicle Issues & Additional Work
                    </h4>
                    <button type="button" @click="showIssueModal = true" class="lv-btn lv-btn-sm lv-btn-brand">
                        <i class="bi bi-plus-lg"></i> Add Issue
                    </button>
                </div>

                <div class="lv-issues-grid">
                    @forelse($jobCard->issues as $issue)
                        @php
                            $curIssueStatus = $issue->status ?? $issue->approval_status ?? 'pending_approval';
                            $issBadge = match($curIssueStatus) {
                                'approved' => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Approved'],
                                'declined', 'rejected' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Declined'],
                                'fixed' => ['class' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Fixed'],
                                'deferred' => ['class' => 'bg-gray-50 text-gray-700 border-gray-200', 'label' => 'Deferred'],
                                default => ['class' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Pending Approval'],
                            };
                        @endphp
                        <div class="lv-issue-card">
                            <div class="flex justify-between items-start gap-2">
                                <h5>{{ $issue->description }}</h5>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border {{ $issBadge['class'] }}">
                                    {{ $issBadge['label'] }}
                                </span>
                            </div>
                            <div class="lv-issue-rep">
                                <i class="bi bi-wrench"></i> {{ $issue->recommended_repair ?? 'Inspection recommendation' }}
                            </div>
                            <div class="flex justify-between text-xs my-2">
                                <span class="text-gray-500">Estimated Cost:</span>
                                <b class="text-[#0D1946]">LKR {{ number_format($issue->estimated_cost, 2) }}</b>
                            </div>
                            @if($issue->technician_notes)
                                <div class="lv-issue-notes">
                                    <i class="bi bi-chat-left-text text-gray-400"></i> {{ $issue->technician_notes }}
                                </div>
                            @endif
                            @if(($curIssueStatus === 'approved') && ($issue->approved_at || $issue->approved_by))
                                <div class="mt-2 text-[11px] text-gray-500 flex items-center gap-1.5 flex-wrap">
                                    <i class="bi bi-check-circle-fill text-emerald-600"></i>
                                    <span>Approved by <b>{{ $issue->approvedByUser?->name ?? $issue->approver?->name ?? 'Staff' }}</b></span>
                                    @if($issue->approved_at)
                                        <span>· {{ $issue->approved_at->format('d M, h:i A') }}</span>
                                    @endif
                                    @if($issue->approval_method)
                                        <span class="capitalize">({{ $issue->approval_method }})</span>
                                    @endif
                                </div>
                            @endif
                            <div class="lv-issue-actions">
                                @if(in_array($curIssueStatus, ['pending', 'pending_approval']))
                                    <button 
                                        type="button" 
                                        @click="openApproval({{ $issue->id }}, '{{ addslashes($issue->description) }}')" 
                                        class="lv-btn lv-btn-sm lv-btn-soft text-emerald-700"
                                    >
                                        <i class="bi bi-check2"></i> Record Approval
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full lv-card p-8 text-center text-gray-400">
                            <i class="bi bi-clipboard-check text-4xl text-gray-300 block mb-2"></i>
                            <p class="m-0 text-sm">No vehicle issues recorded for this job.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: CURRENT JOB CART (Exact Lovable Sidebar Cart) -->
        <div id="current-job-cart" class="lv-cart-col">
            <div class="lv-card lv-cart">
                <!-- Cart Header -->
                <div class="lv-cart-head">
                    <div>
                        <h2>
                            <i class="bi bi-cart-check text-blue-600"></i> Current Job Cart 
                            <span class="cnt" x-text="totals.total_count"></span>
                        </h2>
                        <div class="sub">
                            <b>{{ $vehicle?->vehicle_number }}</b> · {{ $jobCard->job_number }} · <span x-text="totals.total_count"></span> item<span x-text="totals.total_count == 1 ? '' : 's'"></span>
                        </div>
                    </div>
                </div>

                <!-- Cart Body (Grouped by Services and Products) -->
                <div class="lv-cart-body">
                    <div class="lv-cart-empty" x-show="totals.total_count === 0">
                        <div class="ic"><i class="bi bi-cart3"></i></div>
                        <b class="text-gray-700 block mb-1">Job cart is empty</b>
                        <p class="text-xs m-0">Tap <b>Add Service</b> or <b>Issue Part</b> on any item from the catalog.</p>
                    </div>

                    <div x-show="totals.total_count > 0">
                        <!-- Pending Prices Alert Banner -->
                        <div x-show="hasPendingPrices()" class="p-3 mb-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-800 text-xs flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-amber-600 text-base"></i>
                                <span>Some items have <b>pending prices</b>. Please assign prices before completing the job.</span>
                            </div>
                        </div>

                        <!-- Services Group -->
                        <div class="lv-cart-group" x-show="cartServices.length > 0">
                            <h4>
                                <span><i class="bi bi-tools text-blue-600"></i> Services (<span x-text="cartServices.length"></span>)</span>
                                <span>LKR <span x-text="formatMoney(totals.service_total)"></span></span>
                            </h4>
                            <template x-for="srv in cartServices" :key="'srv_' + srv.id">
                                <div class="lv-line transition-all" :class="{'ring-2 ring-amber-400 bg-amber-50/50 rounded-xl p-2': srv.is_price_pending}">
                                    <!-- Selection Checkbox for Selective Printing -->
                                    <input 
                                        type="checkbox" 
                                        :value="srv.id" 
                                        x-model="selectedServices" 
                                        class="lv-line-chk" 
                                        title="Select for printing"
                                    >
                                    <div class="lv-line-main">
                                        <div class="lv-line-name flex items-center gap-1.5 flex-wrap">
                                            <span :title="srv.service_name" x-text="srv.service_name"></span>
                                            <template x-if="srv.is_price_pending">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    <i class="bi bi-clock-history mr-1"></i> Price Pending
                                                </span>
                                            </template>
                                        </div>
                                        <div class="lv-line-tech">
                                            <i class="bi bi-person"></i>
                                            <span x-text="srv.technician_name || '{{ $assignedTech?->name ?: 'Unassigned' }}'"></span>
                                        </div>
                                    </div>

                                    <!-- Stepper Quantity [-] [qty] [+] -->
                                    <div class="lv-stepper">
                                        <button 
                                            type="button" 
                                            @click="changeServiceQty(srv, 'decrease')" 
                                            class="lv-step-btn" 
                                            :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}"
                                            title="Decrease quantity"
                                        >
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="lv-step-val" x-text="srv.quantity"></span>
                                        <button 
                                            type="button" 
                                            @click="changeServiceQty(srv, 'increase')" 
                                            class="lv-step-btn" 
                                            :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}"
                                            title="Increase quantity"
                                        >
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>

                                    <div class="text-right">
                                        <template x-if="srv.is_price_pending">
                                            <div>
                                                <span class="text-[11px] font-bold text-amber-700 block">Not Assigned</span>
                                                @if(!$isReadOnly)
                                                    <button type="button" @click="openEditItem(srv, 'service')" class="text-[11px] font-bold text-blue-600 underline hover:text-blue-800 inline-block">
                                                        Edit Price
                                                    </button>
                                                @endif
                                            </div>
                                        </template>
                                        <template x-if="!srv.is_price_pending">
                                            <div class="lv-line-total" x-text="formatMoney(srv.line_total)"></div>
                                        </template>
                                    </div>

                                    @if(!$isReadOnly)
                                        <button 
                                            type="button" 
                                            @click="openEditItem(srv, 'service')" 
                                            class="lv-edit-btn" 
                                            :title="srv.is_price_pending ? 'Assign Price' : 'Edit technician, price or notes'"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="removeServiceItem(srv)" 
                                            class="lv-rm-btn" 
                                            :disabled="loadingAction !== null"
                                            title="Remove service"
                                        >
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif
                                </div>
                            </template>
                        </div>

                        <!-- Products Group -->
                        <div class="lv-cart-group" x-show="cartProducts.length > 0">
                            <h4>
                                <span><i class="bi bi-box-seam text-amber-600"></i> Products & Parts (<span x-text="cartProducts.length"></span>)</span>
                                <span>LKR <span x-text="formatMoney(totals.product_total)"></span></span>
                            </h4>
                            <template x-for="prod in cartProducts" :key="'prod_' + prod.id">
                                <div class="lv-line transition-all" :class="{'ring-2 ring-amber-400 bg-amber-50/50 rounded-xl p-2': prod.is_price_pending}">
                                    <!-- Selection Checkbox for Selective Printing -->
                                    <input 
                                        type="checkbox" 
                                        :value="prod.id" 
                                        x-model="selectedProducts" 
                                        class="lv-line-chk" 
                                        title="Select for printing"
                                    >
                                    <div class="lv-line-main">
                                        <div class="lv-line-name flex items-center gap-1.5 flex-wrap">
                                            <span :title="prod.product_name" x-text="prod.product_name"></span>
                                            <template x-if="prod.is_price_pending">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    <i class="bi bi-clock-history mr-1"></i> Price Pending
                                                </span>
                                            </template>
                                        </div>
                                        <div class="lv-line-tech" x-text="'SKU: ' + prod.product_sku"></div>
                                    </div>

                                    <!-- Stepper Quantity [-] [qty] [+] -->
                                    <div class="lv-stepper">
                                        <button 
                                            type="button" 
                                            @click="changeProductQty(prod, 'decrease')" 
                                            class="lv-step-btn" 
                                            :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}"
                                            title="Decrease quantity"
                                        >
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <span class="lv-step-val" x-text="prod.quantity"></span>
                                        <button 
                                            type="button" 
                                            @click="changeProductQty(prod, 'increase')" 
                                            class="lv-step-btn" 
                                            :disabled="loadingAction !== null || getProductStock(prod.product_id) <= 0 || {{ $isReadOnly ? 'true' : 'false' }}"
                                            title="Increase quantity"
                                        >
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>

                                    <div class="text-right">
                                        <template x-if="prod.is_price_pending">
                                            <div>
                                                <span class="text-[11px] font-bold text-amber-700 block">Not Assigned</span>
                                                @if(!$isReadOnly)
                                                    <button type="button" @click="openEditItem(prod, 'product')" class="text-[11px] font-bold text-blue-600 underline hover:text-blue-800 inline-block">
                                                        Edit Price
                                                    </button>
                                                @endif
                                            </div>
                                        </template>
                                        <template x-if="!prod.is_price_pending">
                                            <div class="lv-line-total" x-text="formatMoney(prod.line_total)"></div>
                                        </template>
                                    </div>

                                    @if(!$isReadOnly)
                                        <button 
                                            type="button" 
                                            @click="openEditItem(prod, 'product')" 
                                            class="lv-edit-btn" 
                                            :title="prod.is_price_pending ? 'Assign Price' : 'Edit notes or price'"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="removeProductItem(prod)" 
                                            class="lv-rm-btn" 
                                            :disabled="loadingAction !== null"
                                            title="Remove part"
                                        >
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endif
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Cart Footer (Financial Summary & Action Buttons - Strictly Operational) -->
                <div class="lv-cart-foot">
                    <div class="lv-tot">
                        <div>
                            <span>Services Subtotal</span>
                            <b>LKR <span x-text="formatMoney(totals.service_total)"></span></b>
                        </div>
                        <div>
                            <span>Parts Subtotal</span>
                            <b>LKR <span x-text="formatMoney(totals.product_total)"></span></b>
                        </div>
                        <div x-show="totals.discount_amount > 0">
                            <span>Discount</span>
                            <b class="text-rose-600">- LKR <span x-text="formatMoney(totals.discount_amount)"></span></b>
                        </div>
                        <div class="grand">
                            <span>Job Total</span>
                            <b>LKR <span x-text="formatMoney(totals.grand_total)"></span></b>
                        </div>
                    </div>

                    <!-- Operational Buttons (NO PAYMENTS / NO BALANCE / NO BILLING FORM) -->
                    <div class="lv-cart-actions">
                        @if($status === 'completed' || $status === 'delivered')
                            <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-center text-xs text-emerald-800 font-bold flex items-center justify-center gap-1.5">
                                <i class="bi bi-check-circle-fill text-emerald-600"></i> Job completed successfully.
                            </div>

                            <!-- FIRST ROW: Print Job — Full Width (Dark navy background #0B1849, White text, Printer icon) -->
                            <button 
                                type="button" 
                                @click="printJobCard()" 
                                class="lv-btn lv-btn-brand main-action-btn shadow-xs justify-center" 
                                title="Print Full Job Card"
                            >
                                <i class="bi bi-printer"></i>
                                <span>Print Job</span>
                            </button>

                            <!-- SECOND ROW: Print Selected — Full Width (Existing outlined styling) -->
                            <button 
                                type="button" 
                                @click="printSelectedItems()" 
                                class="lv-btn lv-btn-soft main-action-btn shadow-xs justify-center" 
                                title="Print Checked Items"
                            >
                                <i class="bi bi-check2-square text-indigo-600"></i>
                                <span>Print Selected</span>
                            </button>

                            <!-- THIRD ROW: + Add Job — Full Width (Green background, White text, Single plus icon) -->
                            <a 
                                href="{{ route('operations.vehicles.index', ['search' => $vehicle?->vehicle_number]) }}" 
                                class="lv-btn lv-btn-success main-action-btn shadow-xs justify-center"
                                title="Add new job card for {{ $vehicle?->vehicle_number }}"
                            >
                                <i class="bi bi-plus-lg"></i>
                                <span>Add Job</span>
                            </a>
                        @else
                            <!-- Row 1: Print Job — Full Width (Large, Navy #0B1849) -->
                            <button 
                                type="button" 
                                @click="printJobCard()" 
                                class="lv-btn lv-btn-brand main-action-btn shadow-xs justify-center" 
                                title="Print Full Job Card"
                            >
                                <i class="bi bi-printer"></i>
                                <span>Print Job</span>
                            </button>

                            <!-- Row 2: Equal-Width Buttons [ Print Selected ] [ Complete Job ] -->
                            <div class="lv-mini-grid">
                                <button 
                                    type="button" 
                                    @click="printSelectedItems()" 
                                    class="lv-btn lv-btn-soft lv-btn-sm justify-center w-full" 
                                    title="Print Checked Items"
                                >
                                    <i class="bi bi-check2-square text-indigo-600"></i>
                                    <span>Print Selected</span>
                                </button>

                                <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="w-full h-full m-0 p-0 flex" @submit.prevent="submitCompleteJob($event)">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="complete_job">
                                    <button 
                                        type="submit" 
                                        class="lv-btn lv-btn-success lv-btn-sm shadow-xs justify-center w-full h-full"
                                        title="Complete Job"
                                    >
                                        <i class="bi bi-check2-circle"></i>
                                        <span>Complete Job</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MOBILE BOTTOM CART BAR (Visible on screens < 1200px) -->
    <div class="lv-cart-bar d-xl-none lg:hidden">
        <button type="button" class="lv-cart-bar-btn" @click="document.getElementById('current-job-cart')?.scrollIntoView({ behavior: 'smooth' })">
            <span class="lv-cb-count"><i class="bi bi-cart3"></i> <span x-text="totals.total_count"></span> Items</span>
            <span class="lv-cb-total">LKR <span x-text="formatMoney(totals.grand_total)"></span></span>
            <span class="lv-cb-view">View Cart <i class="bi bi-chevron-down"></i></span>
        </button>
    </div>

    <!-- 4. MOBILE / TABLET CART DRAWER (Offcanvas Slide-Over) -->
    <div 
        x-show="mobileCartOpen" 
        x-transition 
        class="lv-modal-overlay lg:hidden" 
        style="display: none;"
    >
        <div class="lv-modal-content max-w-lg h-[92vh] flex flex-col" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-cart-check text-blue-600"></i> Current Job Cart (<span x-text="totals.total_count"></span>)</h3>
                <button type="button" @click="mobileCartOpen = false" class="text-gray-400 hover:text-gray-700 border-0 bg-transparent text-xl">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4">
                <div class="lv-cart-empty" x-show="totals.total_count === 0">
                    <div class="ic"><i class="bi bi-cart3"></i></div>
                    <b class="text-gray-700 block mb-1">Job cart is empty</b>
                </div>

                <div x-show="totals.total_count > 0">
                    <!-- Services Group -->
                    <div class="lv-cart-group" x-show="cartServices.length > 0">
                        <h4>
                            <span>Services (<span x-text="cartServices.length"></span>)</span>
                            <span>LKR <span x-text="formatMoney(totals.service_total)"></span></span>
                        </h4>
                        <template x-for="srv in cartServices" :key="'m_srv_' + srv.id">
                            <div class="lv-line" :class="{'ring-2 ring-amber-400 bg-amber-50/50 rounded-xl p-1.5': srv.is_price_pending}">
                                <input type="checkbox" :value="srv.id" x-model="selectedServices" class="lv-line-chk">
                                <div class="lv-line-main">
                                    <div class="lv-line-name flex items-center gap-1">
                                        <span x-text="srv.service_name"></span>
                                        <template x-if="srv.is_price_pending">
                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1 rounded">Pending</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="lv-stepper">
                                    <button type="button" @click="changeServiceQty(srv, 'decrease')" class="lv-step-btn" :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span class="lv-step-val" x-text="srv.quantity"></span>
                                    <button type="button" @click="changeServiceQty(srv, 'increase')" class="lv-step-btn" :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <template x-if="srv.is_price_pending">
                                        <span class="text-[10px] font-bold text-amber-700 block">Pending</span>
                                    </template>
                                    <template x-if="!srv.is_price_pending">
                                        <div class="lv-line-total" x-text="formatMoney(srv.line_total)"></div>
                                    </template>
                                </div>
                                @if(!$isReadOnly)
                                    <button type="button" @click="openEditItem(srv, 'service')" class="lv-edit-btn">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" @click="removeServiceItem(srv)" class="lv-rm-btn">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                @endif
                            </div>
                        </template>
                    </div>

                    <!-- Products Group -->
                    <div class="lv-cart-group" x-show="cartProducts.length > 0">
                        <h4>
                            <span>Parts (<span x-text="cartProducts.length"></span>)</span>
                            <span>LKR <span x-text="formatMoney(totals.product_total)"></span></span>
                        </h4>
                        <template x-for="prod in cartProducts" :key="'m_prod_' + prod.id">
                            <div class="lv-line" :class="{'ring-2 ring-amber-400 bg-amber-50/50 rounded-xl p-1.5': prod.is_price_pending}">
                                <input type="checkbox" :value="prod.id" x-model="selectedProducts" class="lv-line-chk">
                                <div class="lv-line-main">
                                    <div class="lv-line-name flex items-center gap-1">
                                        <span x-text="prod.product_name"></span>
                                        <template x-if="prod.is_price_pending">
                                            <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1 rounded">Pending</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="lv-stepper">
                                    <button type="button" @click="changeProductQty(prod, 'decrease')" class="lv-step-btn" :disabled="loadingAction !== null || {{ $isReadOnly ? 'true' : 'false' }}">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span class="lv-step-val" x-text="prod.quantity"></span>
                                    <button type="button" @click="changeProductQty(prod, 'increase')" class="lv-step-btn" :disabled="loadingAction !== null || getProductStock(prod.product_id) <= 0 || {{ $isReadOnly ? 'true' : 'false' }}">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <template x-if="prod.is_price_pending">
                                        <span class="text-[10px] font-bold text-amber-700 block">Pending</span>
                                    </template>
                                    <template x-if="!prod.is_price_pending">
                                        <div class="lv-line-total" x-text="formatMoney(prod.line_total)"></div>
                                    </template>
                                </div>
                                @if(!$isReadOnly)
                                    <button type="button" @click="openEditItem(prod, 'product')" class="lv-edit-btn">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" @click="removeProductItem(prod)" class="lv-rm-btn">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                @endif
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="lv-cart-foot">
                <div class="lv-tot">
                    <div class="grand">
                        <span>Grand Total</span>
                        <b>LKR <span x-text="formatMoney(totals.grand_total)"></span></b>
                    </div>
                </div>
                <div class="lv-cart-actions">
                    @if(in_array($status, ['completed', 'delivered']))
                        <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-center text-xs text-emerald-800 font-bold flex items-center justify-center gap-1.5">
                            <i class="bi bi-check-circle-fill text-emerald-600"></i> Job completed successfully.
                        </div>

                        <!-- FIRST ROW: Print Job — Full Width (Dark navy background #0B1849, White text, Printer icon) -->
                        <button 
                            type="button" 
                            @click="printJobCard()" 
                            class="lv-btn lv-btn-brand main-action-btn shadow-xs justify-center" 
                            title="Print Full Job Card"
                        >
                            <i class="bi bi-printer"></i>
                            <span>Print Job</span>
                        </button>

                        <!-- SECOND ROW: Print Selected — Full Width (Existing outlined styling) -->
                        <button 
                            type="button" 
                            @click="printSelectedItems()" 
                            class="lv-btn lv-btn-soft main-action-btn shadow-xs justify-center" 
                            title="Print Checked Items"
                        >
                            <i class="bi bi-check2-square text-indigo-600"></i>
                            <span>Print Selected</span>
                        </button>

                        <!-- THIRD ROW: + Add Job — Full Width (Green background, White text, Single plus icon) -->
                        <a 
                            href="{{ route('operations.vehicles.index', ['search' => $vehicle?->vehicle_number]) }}" 
                            class="lv-btn lv-btn-success main-action-btn shadow-xs justify-center"
                            title="Add new job card for {{ $vehicle?->vehicle_number }}"
                        >
                            <i class="bi bi-plus-lg"></i>
                            <span>Add Job</span>
                        </a>
                    @else
                        <!-- Row 1: Print Job — Full Width (Large, Navy #0B1849) -->
                        <button 
                            type="button" 
                            @click="printJobCard()" 
                            class="lv-btn lv-btn-brand main-action-btn shadow-xs justify-center" 
                            title="Print Full Job Card"
                        >
                            <i class="bi bi-printer"></i>
                            <span>Print Job</span>
                        </button>

                        <!-- Row 2: Equal-Width Buttons [ Print Selected ] [ Complete Job ] -->
                        <div class="lv-mini-grid">
                            <button 
                                type="button" 
                                @click="printSelectedItems()" 
                                class="lv-btn lv-btn-soft lv-btn-sm justify-center w-full" 
                                title="Print Checked Items"
                            >
                                <i class="bi bi-check2-square text-indigo-600"></i>
                                <span>Print Selected</span>
                            </button>

                            <form action="{{ route('operations.job-cards.status', $jobCard) }}" method="POST" class="w-full h-full m-0 p-0 flex" @submit.prevent="submitCompleteJob($event)">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="complete_job">
                                <button 
                                    type="submit" 
                                    class="lv-btn lv-btn-success lv-btn-sm shadow-xs justify-center w-full h-full"
                                    title="Complete Job"
                                >
                                    <i class="bi bi-check2-circle"></i>
                                    <span>Complete Job</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- =======================================================================
         LOVABLE INTERACTIVE MODALS
         ======================================================================= -->

    <!-- MODAL 1: ASSIGN TECHNICIAN -->
    <div x-show="showTechModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content max-w-sm" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-person-gear text-blue-600"></i> Assign Lead Technician</h3>
                <button type="button" @click="showTechModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('operations.job-cards.update', $jobCard) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="lv-modal-body space-y-2">
                    <p class="text-xs text-gray-500 mb-2">Select the active staff technician responsible for this job card:</p>
                    <div class="space-y-1.5 max-h-60 overflow-y-auto">
                        @foreach($technicians as $tech)
                            <label class="flex items-center justify-between p-3 border rounded-xl hover:bg-gray-50 cursor-pointer transition {{ $jobCard->assigned_to == $tech->id ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200' }}">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="assigned_to" value="{{ $tech->id }}" {{ $jobCard->assigned_to == $tech->id ? 'checked' : '' }} class="text-blue-600">
                                    <span class="text-sm font-bold text-gray-800">{{ $tech->name }}</span>
                                </div>
                                <span class="text-[11px] text-gray-400 uppercase">{{ $tech->role?->name ?? 'Staff' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="lv-modal-footer">
                    <button type="button" @click="showTechModal = false" class="lv-btn lv-btn-soft">Cancel</button>
                    <button type="submit" class="lv-btn lv-btn-brand">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT VEHICLE & MILEAGE -->
    <div x-show="showVehicleModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-car-front text-blue-600"></i> Edit Vehicle & Mileage Details</h3>
                <button type="button" @click="showVehicleModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('operations.job-cards.update', $jobCard) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="lv-modal-body space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Starting Mileage (km)</label>
                            <input type="number" name="current_mileage" value="{{ $jobCard->current_mileage }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Next Service Mileage (km)</label>
                            <input type="number" name="next_service_mileage" value="{{ $jobCard->next_service_mileage }}" min="0" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Technician / Service Notes</label>
                        <textarea name="service_notes" rows="2" class="w-full text-sm border-gray-300 rounded-lg p-2 border">{{ $jobCard->service_notes }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Customer Notes / Requests</label>
                        <textarea name="customer_notes" rows="2" class="w-full text-sm border-gray-300 rounded-lg p-2 border">{{ $jobCard->customer_notes }}</textarea>
                    </div>
                </div>
                <div class="lv-modal-footer">
                    <button type="button" @click="showVehicleModal = false" class="lv-btn lv-btn-soft">Cancel</button>
                    <button type="submit" class="lv-btn lv-btn-brand">Save Details</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: EDIT CARPET QUANTITIES -->
    <div x-show="showCarpetModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-layers text-indigo-600"></i> Edit Carpet Quantities</h3>
                <button type="button" @click="showCarpetModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('operations.job-cards.carpets.update', $jobCard) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="lv-modal-body space-y-3">
                    <p class="text-xs text-gray-500 mb-2">Record intake carpet counts for vehicle {{ $vehicle?->vehicle_number }}:</p>
                    @forelse($carpetCategories as $idx => $cat)
                        @php
                            $qty = $existingCarpets->get($cat->id)?->quantity ?? 0;
                        @endphp
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-xl bg-gray-50">
                            <span class="text-sm font-semibold text-gray-800">{{ $cat->name }}</span>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="carpets[{{ $idx }}][category_id]" value="{{ $cat->id }}">
                                <input type="number" name="carpets[{{ $idx }}][quantity]" value="{{ $qty }}" min="0" max="20" class="w-20 text-center text-sm font-bold border-gray-300 rounded-lg p-1.5 border">
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 italic">No carpet categories configured for vehicle type {{ $vehicleType?->name }}.</div>
                    @endforelse
                </div>
                <div class="lv-modal-footer">
                    <button type="button" @click="showCarpetModal = false" class="lv-btn lv-btn-soft">Cancel</button>
                    <button type="submit" class="lv-btn lv-btn-brand">Save Carpets</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: ADD VEHICLE ISSUE -->
    <div x-show="showIssueModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-exclamation-triangle text-amber-500"></i> Record Vehicle Issue</h3>
                <button type="button" @click="showIssueModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('operations.job-cards.issues.store', $jobCard) }}" method="POST">
                @csrf
                <div class="lv-modal-body space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Issue Description *</label>
                        <input type="text" name="description" required placeholder="e.g. Squeaking noise from front brakes" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Recommended Repair *</label>
                        <input type="text" name="recommended_repair" required placeholder="e.g. Replace front brake pads" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Estimated Cost (LKR)</label>
                        <input type="number" name="estimated_cost" min="0" step="0.01" placeholder="0.00" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Technician Notes</label>
                        <textarea name="notes" rows="2" placeholder="e.g. Pads at 15%. Discs OK." class="w-full text-sm border-gray-300 rounded-lg p-2 border"></textarea>
                    </div>
                </div>
                <div class="lv-modal-footer">
                    <button type="button" @click="showIssueModal = false" class="lv-btn lv-btn-soft">Cancel</button>
                    <button type="submit" class="lv-btn lv-btn-brand">Add Issue</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: RECORD ISSUE APPROVAL -->
    <div x-show="showApprovalModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content max-w-md" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-check2-circle text-emerald-600"></i> Record Customer Approval</h3>
                <button type="button" @click="showApprovalModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form :action="'{{ url('/operations/job-issues') }}/' + selectedIssueId + '/approval'" method="POST">
                @csrf
                @method('PATCH')
                <div class="lv-modal-body space-y-3">
                    <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs">
                        <span class="text-gray-500 block mb-0.5">Issue:</span>
                        <strong class="text-gray-800" x-text="selectedIssueDesc"></strong>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Customer Decision *</label>
                        <select name="decision" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                            <option value="approved">Customer Approved</option>
                            <option value="rejected">Customer Declined / Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Approval Method *</label>
                        <select name="method" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                            <option value="verbal">Verbal (In-Person)</option>
                            <option value="phone">Phone Call</option>
                            <option value="whatsapp">WhatsApp / SMS</option>
                            <option value="written">Written / Signed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Customer Contact Verified</label>
                        <input type="text" name="customer_contact" value="{{ $customer?->phone }}" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Remarks</label>
                        <textarea name="remarks" rows="2" placeholder="e.g. Customer agreed to replace pads immediately." class="w-full text-sm border-gray-300 rounded-lg p-2 border"></textarea>
                    </div>
                </div>
                <div class="lv-modal-footer">
                    <button type="button" @click="showApprovalModal = false" class="lv-btn lv-btn-soft">Cancel</button>
                    <button type="submit" class="lv-btn lv-btn-brand">Save Decision</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 6: EDIT CART ITEM (Advanced edit for notes, technician, or authorized price override) -->
    <div x-show="showEditItemModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content max-w-sm" @click.stop>
            <div class="lv-modal-header">
                <h3>
                    <i :class="editingItem.type === 'service' ? 'bi bi-tools text-blue-600' : 'bi bi-box-seam text-amber-600'"></i>
                    <span x-text="'Edit ' + (editingItem.type === 'service' ? 'Service' : 'Part')"></span>
                </h3>
                <button type="button" @click="showEditItemModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="lv-modal-body space-y-3">
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-1.5">
                    <div>
                        <span class="text-xs text-gray-500 font-bold block mb-0.5">Item:</span>
                        <strong class="text-sm text-gray-900 block" x-text="editingItem.title"></strong>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-bold block mb-0.5">Quantity:</span>
                        <span class="text-sm font-semibold text-gray-800" x-text="editingItem.quantity"></span>
                    </div>
                </div>

                <template x-if="editingItem.type === 'service'">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Technician Assigned</label>
                        <select x-model="editingItem.technician_name" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                            <option value="">— Use Lead Technician ({{ $assignedTech?->name ?? 'Unassigned' }}) —</option>
                            @foreach($technicians as $t)
                                <option value="{{ $t->name }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                <div x-show="editingItem.is_price_pending || {{ (Auth::user()->isAdmin() || Auth::user()->hasPermission('override_prices')) ? 'true' : 'false' }}">
                    <label class="block text-xs font-bold text-gray-700 mb-1">
                        <span x-text="editingItem.is_price_pending ? 'Unit Selling Price' : 'Unit Price Override (Admin/Supervisor)'"></span>
                        <span x-show="editingItem.is_price_pending" class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-xs text-gray-400 font-bold">LKR</span>
                        <input 
                            type="number" 
                            x-model="editingItem.unit_price" 
                            min="0" 
                            step="0.01" 
                            placeholder="0.00" 
                            class="w-full text-sm border-gray-300 rounded-lg py-2 pl-12 pr-3 border font-semibold focus:ring-2 focus:ring-blue-500"
                            :class="{'border-amber-400 bg-amber-50/40': editingItem.is_price_pending}"
                        >
                    </div>
                    <p x-show="editingItem.is_price_pending" class="text-[11px] text-amber-700 mt-1">
                        <i class="bi bi-info-circle"></i> This price will be saved for this Job Card item without altering Master Data.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Notes (Optional)</label>
                    <input type="text" x-model="editingItem.notes" placeholder="Optional notes" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                </div>
            </div>
            <div class="lv-modal-footer">
                <button type="button" @click="showEditItemModal = false" class="lv-btn lv-btn-soft">Cancel</button>
                <button 
                    type="button" 
                    @click="saveEditItem()" 
                    class="lv-btn lv-btn-brand"
                    :disabled="loadingAction !== null"
                >
                    <span x-show="loadingAction === 'save_edit'"><span class="inline-block animate-spin border-2 border-white border-t-transparent rounded-full w-3 h-3 mr-1"></span> Saving...</span>
                    <span x-show="loadingAction !== 'save_edit'" x-text="editingItem.is_price_pending ? 'Save Price' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 7: WHATSAPP SUMMARY -->
    <div x-show="showWhatsAppModal" x-transition class="lv-modal-overlay" style="display: none;">
        <div class="lv-modal-content max-w-md" @click.stop>
            <div class="lv-modal-header">
                <h3><i class="bi bi-whatsapp text-emerald-600"></i> WhatsApp Job Summary</h3>
                <button type="button" @click="showWhatsAppModal = false" class="border-0 bg-transparent text-gray-400 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="lv-modal-body space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Customer Mobile Number</label>
                    <input type="text" id="lvWaPhone" value="{{ $normPhone }}" placeholder="9477xxxxxxx" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Message Preview</label>
                    <textarea id="lvWaText" rows="7" class="w-full text-xs font-mono border-gray-300 rounded-lg p-2.5 border bg-gray-50">{{ $waText }}</textarea>
                </div>
            </div>
            <div class="lv-modal-footer">
                <button 
                    type="button" 
                    onclick="navigator.clipboard.writeText(document.getElementById('lvWaText').value); alert('Copied to clipboard!');" 
                    class="lv-btn lv-btn-soft"
                >
                    <i class="bi bi-clipboard"></i> Copy Text
                </button>
                <button 
                    type="button" 
                    onclick="
                        let p = document.getElementById('lvWaPhone').value.replace(/\D/g, '');
                        let t = encodeURIComponent(document.getElementById('lvWaText').value);
                        if (!p) { alert('Please enter phone number.'); return; }
                        window.open('https://wa.me/' + p + '?text=' + t, '_blank');
                    " 
                    class="lv-btn lv-btn-success"
                >
                    <i class="bi bi-whatsapp"></i> Send via WhatsApp
                </button>
            </div>
        </div>
    </div>

    <!-- 5. FLOATING TOAST NOTIFICATION -->
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 transform translate-y-2 scale-95"
        class="lv-toast"
        :class="toast.type"
        style="display: none;"
    >
        <i :class="toast.type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-triangle-fill'"></i>
        <span x-text="toast.message"></span>
    </div>
</div>
@endsection
