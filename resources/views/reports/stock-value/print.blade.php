<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Value Report - AutoCare System</title>
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
                <p class="text-xs text-gray-500 mt-0.5">Inventory Valuation Report (Actual FIFO Cost Valuation)</p>
                @if($selectedBranch)
                    <p class="text-xs font-semibold text-gray-700 mt-1">Branch: {{ $selectedBranch->branch_name }}</p>
                @else
                    <p class="text-xs font-semibold text-gray-700 mt-1">Branch: All Branches (Company-wide)</p>
                @endif
                @if($asOfDate)
                    <p class="text-xs text-gray-500">Valuation As Of: {{ $asOfDate }}</p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div class="text-xs font-bold text-emerald-700 mt-1">Total Valuation: LKR {{ number_format($grandTotalStockValue, 2) }}</div>
                <button onclick="window.print()" class="no-print mt-2 px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print</button>
            </div>
        </div>

        @if($viewType === 'batch')
            <!-- Batch-wise table -->
            <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                        <th class="p-2 border-r border-gray-300">Batch #</th>
                        <th class="p-2 border-r border-gray-300">Product Name</th>
                        <th class="p-2 border-r border-gray-300">GRN #</th>
                        <th class="p-2 border-r border-gray-300">Supplier</th>
                        <th class="p-2 border-r border-gray-300 text-right">Remaining Qty</th>
                        <th class="p-2 border-r border-gray-300 text-right">Batch Unit Cost</th>
                        <th class="p-2 border-r border-gray-300 text-right">Total Value</th>
                        <th class="p-2">Branch</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($batches as $b)
                        @php $val = (float)$b->remaining_quantity * (float)$b->inventory_unit_cost; @endphp
                        <tr>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $b->batch_number }}</td>
                            <td class="p-2 border-r border-gray-200 font-semibold">{{ $b->product?->name }}</td>
                            <td class="p-2 border-r border-gray-200">{{ $b->grn?->grn_number ?? '-' }}</td>
                            <td class="p-2 border-r border-gray-200">{{ $b->supplier?->name ?? 'N/A' }}</td>
                            <td class="p-2 border-r border-gray-200 text-right font-bold">{{ number_format($b->remaining_quantity, 2) }}</td>
                            <td class="p-2 border-r border-gray-200 text-right font-mono">LKR {{ number_format($b->inventory_unit_cost, 2) }}</td>
                            <td class="p-2 border-r border-gray-200 text-right font-bold">LKR {{ number_format($val, 2) }}</td>
                            <td class="p-2">{{ $b->branch?->branch_name ?? 'Main Branch' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold border-t border-gray-300">
                    <tr>
                        <td colspan="4" class="p-2 text-right">Total:</td>
                        <td class="p-2 text-right">{{ number_format($grandTotalUnits, 2) }}</td>
                        <td></td>
                        <td class="p-2 text-right text-emerald-800">LKR {{ number_format($grandTotalStockValue, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <!-- Product-wise table -->
            <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                        <th class="p-2 border-r border-gray-300">#</th>
                        <th class="p-2 border-r border-gray-300">Product Name</th>
                        <th class="p-2 border-r border-gray-300">SKU</th>
                        <th class="p-2 border-r border-gray-300">Supplier</th>
                        <th class="p-2 border-r border-gray-300 text-center">Batches</th>
                        <th class="p-2 border-r border-gray-300 text-center">Available Units</th>
                        <th class="p-2 border-r border-gray-300 text-right">Weighted Avg Cost</th>
                        <th class="p-2 border-r border-gray-300 text-right">Selling Price</th>
                        <th class="p-2 text-right">Total Stock Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($productWise as $idx => $item)
                        <tr>
                            <td class="p-2 border-r border-gray-200 text-gray-500">{{ $idx + 1 }}</td>
                            <td class="p-2 border-r border-gray-200 font-semibold">{{ $item['product']->name }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $item['product']->sku ?? '-' }}</td>
                            <td class="p-2 border-r border-gray-200">{{ $item['supplier_name'] }}</td>
                            <td class="p-2 border-r border-gray-200 text-center">{{ $item['batches_count'] }}</td>
                            <td class="p-2 border-r border-gray-200 text-center font-bold">{{ number_format($item['total_quantity'], 2) }}</td>
                            <td class="p-2 border-r border-gray-200 text-right font-mono">LKR {{ number_format($item['weighted_avg_cost'], 2) }}</td>
                            <td class="p-2 border-r border-gray-200 text-right">LKR {{ number_format($item['selling_price'], 2) }}</td>
                            <td class="p-2 text-right font-bold text-emerald-800">LKR {{ number_format($item['total_valuation'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 font-bold border-t border-gray-300">
                    <tr>
                        <td colspan="5" class="p-2 text-right">Grand Total:</td>
                        <td class="p-2 text-center">{{ number_format($grandTotalUnits, 2) }}</td>
                        <td colspan="2"></td>
                        <td class="p-2 text-right text-emerald-800">LKR {{ number_format($grandTotalStockValue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Footer -->
        <div class="pt-6 border-t border-gray-300 flex justify-between text-[11px] text-gray-500">
            <span>Valuation calculated strictly as Remaining Quantity × Recorded FIFO Batch Cost</span>
            <span>AutoCare System - Confidential Financial Record</span>
        </div>
    </div>
</body>
</html>
