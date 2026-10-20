<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service History - {{ $vehicle->vehicle_number }} - AutoCare</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 p-8 text-xs font-sans">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-[#0B1849] pb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#0B1849]">AutoCare Service Center</h1>
                <p class="text-xs text-gray-500 mt-0.5">Official Vehicle Maintenance & Service Record</p>
                <p class="text-xs text-gray-500">Document Issued: {{ now()->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold font-mono text-[#0B1849] border-2 border-[#0B1849] px-3 py-1 rounded inline-block">
                    {{ $vehicle->vehicle_number }}
                </div>
                <button onclick="window.print()" class="no-print mt-2 block ml-auto px-3 py-1 bg-[#0B1849] text-white rounded text-xs">Print A4 Record</button>
            </div>
        </div>

        <!-- Vehicle Details Box -->
        <div class="border border-gray-300 rounded p-3 grid grid-cols-2 gap-4">
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Vehicle Details</div>
                <div class="mt-1 space-y-0.5">
                    <div><span class="text-gray-500">Plate Number:</span> <span class="font-bold">{{ $vehicle->vehicle_number }}</span></div>
                    <div><span class="text-gray-500">Make & Model:</span> <span class="font-medium">{{ $vehicle->make_model ?? 'Standard' }}</span></div>
                    <div><span class="text-gray-500">Vehicle Type:</span> <span>{{ $vehicle->vehicleType?->name ?? 'Standard' }}</span></div>
                    @if($vehicle->chassis_number)
                        <div><span class="text-gray-500">Chassis No:</span> <span class="font-mono">{{ $vehicle->chassis_number }}</span></div>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Customer & Service Milestone</div>
                <div class="mt-1 space-y-0.5">
                    <div><span class="text-gray-500">Customer:</span> <span class="font-semibold">{{ $vehicle->customer?->display_name ?? 'N/A' }}</span></div>
                    <div><span class="text-gray-500">Contact:</span> <span>{{ $vehicle->customer?->phone ?? 'N/A' }}</span></div>
                    <div><span class="text-gray-500">Latest Recorded Mileage:</span> <span class="font-bold text-[#0B1849]">{{ $summary['latest_mileage'] ? number_format($summary['latest_mileage']).' km' : 'N/A' }}</span></div>
                    <div><span class="text-gray-500">Next Service Due:</span> <span class="font-bold text-emerald-800">{{ $summary['next_service_mileage'] ? number_format($summary['next_service_mileage']).' km' : 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Service Table -->
        <div>
            <div class="font-bold text-[#0B1849] mb-2 uppercase tracking-wider text-xs">Completed Maintenance History ({{ count($serviceHistory) }} visits)</div>
            <table class="w-full text-left border-collapse border border-gray-300 text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 font-bold text-gray-800">
                        <th class="p-2 border-r border-gray-300">Date</th>
                        <th class="p-2 border-r border-gray-300">Job #</th>
                        <th class="p-2 border-r border-gray-300 text-center">Mileage</th>
                        <th class="p-2 border-r border-gray-300">Services Performed</th>
                        <th class="p-2 border-r border-gray-300">Products & Parts Used</th>
                        <th class="p-2 border-r border-gray-300">Invoice #</th>
                        <th class="p-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($serviceHistory as $job)
                        <tr>
                            <td class="p-2 border-r border-gray-200 whitespace-nowrap">{{ $job->date?->format('Y-m-d') }}</td>
                            <td class="p-2 border-r border-gray-200 font-mono font-bold">{{ $job->job_number }}</td>
                            <td class="p-2 border-r border-gray-200 text-center font-mono">{{ $job->current_mileage ? number_format($job->current_mileage).' km' : '-' }}</td>
                            <td class="p-2 border-r border-gray-200">
                                @foreach($job->services as $s)
                                    <div>• {{ $s->service_name ?? $s->service?->name ?? 'Service' }}</div>
                                @endforeach
                            </td>
                            <td class="p-2 border-r border-gray-200">
                                @foreach($job->products as $p)
                                    <div>• {{ $p->product_name ?? 'Item' }} (×{{ $p->quantity }})</div>
                                @endforeach
                            </td>
                            <td class="p-2 border-r border-gray-200 font-mono">{{ $job->invoice?->invoice_number ?? '-' }}</td>
                            <td class="p-2 text-right font-medium">LKR {{ number_format($job->grand_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500 italic">No previous service history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signature & Stamp Section -->
        <div class="pt-8 grid grid-cols-2 gap-8 border-t border-gray-300">
            <div>
                <div class="text-[11px] text-gray-500 italic leading-relaxed">
                    <strong>Disclaimer:</strong> This is an authentic service center record of work performed at AutoCare Service Center. It is issued for customer maintenance, licensing, and administrative reference. It does not constitute an official government roadworthiness certificate.
                </div>
            </div>
            <div class="text-right flex flex-col justify-end items-end space-y-8">
                <div class="w-48 border-b border-gray-400"></div>
                <div class="text-xs font-semibold text-gray-700">Authorized Service Manager & Center Stamp</div>
            </div>
        </div>
    </div>
</body>
</html>
