<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report - AutoCare System</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8 text-xs font-sans">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-[#0B1849] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#0B1849]">AutoCare Service Center</h1>
                <p class="text-xs text-gray-500 mt-0.5">Inventory Stock Report</p>
                @if($selectedBranch)
                    <p class="text-xs font-semibold text-gray-700 mt-1">Branch: {{ $selectedBranch->branch_name }}</p>
                @else
                    <p class="text-xs font-semibold text-gray-700 mt-1">Branch: All Branches (Company-wide)</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div class="text-xs text-gray-500">Filtered Status: {{ ucfirst(str_replace('_', ' ', $stockStatus)) }}</div>
                <button onclick="window.print()" class="no-print mt-2 px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print</button>
            </div>
        </div>

        <!-- Table -->
        <table class="w-full text-left border-collapse border border-gray-300 text-xs">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                    <th class="p-2 border-r border-gray-300">#</th>
                    <th class="p-2 border-r border-gray-300">Product Name</th>
                    <th class="p-2 border-r border-gray-300">SKU</th>
                    <th class="p-2 border-r border-gray-300">Supplier</th>
                    <th class="p-2 border-r border-gray-300 text-center">Available Stock</th>
                    <th class="p-2 border-r border-gray-300">UOM</th>
                    <th class="p-2 border-r border-gray-300 text-right">Selling Price</th>
                    <th class="p-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($products as $idx => $p)
                    <tr>
                        <td class="p-2 border-r border-gray-200 text-gray-500">{{ $idx + 1 }}</td>
                        <td class="p-2 border-r border-gray-200 font-semibold">{{ $p->name }}</td>
                        <td class="p-2 border-r border-gray-200 font-mono">{{ $p->sku ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-200">{{ $p->preferredSupplier?->name ?? 'N/A' }}</td>
                        <td class="p-2 border-r border-gray-200 text-center font-bold">@quantity($p->computed_available_stock)</td>
                        <td class="p-2 border-r border-gray-200">{{ $p->uom?->name ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-200 text-right">LKR {{ number_format($p->selling_price, 2) }}</td>
                        <td class="p-2 text-center">
                            {{ $p->computed_available_stock <= 0 ? 'Out of Stock' : ($p->min_stock > 0 && $p->computed_available_stock <= (float)$p->min_stock ? 'Low Stock' : 'In Stock') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer -->
        <div class="pt-6 border-t border-gray-300 flex justify-between text-[11px] text-gray-500">
            <span>
                Total Products: {{ count($products) }}
                @if(!empty($hasMultipleUoms))
                    | Stock Summary: Mixed Units ({{ $uomSummaryText }})
                @else
                    | Total Units: @quantity($products->sum('computed_available_stock')) {{ $singleUomName ?? '' }}
                @endif
            </span>
            <span>AutoCare System - Inventory & Valuation Management</span>
        </div>
    </div>
</body>
</html>
