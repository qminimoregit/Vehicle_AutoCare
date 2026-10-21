<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\JobCard;
use App\Models\JobIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobIssueController extends Controller
{
    /**
     * Record a new vehicle issue against a job card.
     */
    public function store(Request $request, JobCard $jobCard): RedirectResponse
    {
        if ($jobCard->status === 'cancelled') {
            return back()->with('error', 'Cannot record issues on a cancelled job card.');
        }

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'recommended_repair' => ['required', 'string', 'max:1000'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $jobCard->issues()->create([
            'vehicle_id' => $jobCard->vehicle_id,
            'description' => $validated['description'],
            'recommended_repair' => $validated['recommended_repair'],
            'estimated_cost' => $validated['estimated_cost'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'pending_approval',
            'recorded_by' => Auth::id(),
        ]);

        return back()->with('success', 'Vehicle issue recorded successfully.');
    }

    /**
     * Record customer decision / approval on a recorded vehicle issue.
     */
    public function recordApproval(Request $request, JobIssue $issue): RedirectResponse
    {
        if (! $request->has('customer_decision') && $request->has('status')) {
            $request->merge(['customer_decision' => $request->input('status')]);
        }

        $validated = $request->validate([
            'customer_decision' => ['required', 'in:approved,declined,deferred'],
            'approval_method' => ['required_if:customer_decision,approved', 'nullable', 'in:verbal,phone,written,whatsapp'],
            'approval_date' => ['nullable', 'date'],
            'approval_contact' => ['nullable', 'string', 'max:100'],
            'approval_note' => ['nullable', 'string', 'max:1000'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $decision = $validated['customer_decision'];
        $updateData = [
            'customer_decision' => $decision,
            'approval_method' => $validated['approval_method'] ?? null,
            'approval_contact' => $validated['approval_contact'] ?? null,
            'approval_note' => $validated['approval_note'] ?? null,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ];

        if (isset($validated['estimated_cost'])) {
            $updateData['estimated_cost'] = $validated['estimated_cost'];
        }

        if ($decision === 'approved') {
            $updateData['status'] = 'approved';
            $message = "Customer approval recorded for: '{$issue->recommended_repair}'. You may now add additional repair work.";
        } elseif ($decision === 'declined') {
            $updateData['status'] = 'declined';
            $message = "Issue marked as declined by customer.";
        } else {
            $updateData['status'] = 'deferred';
            $message = "Issue deferred for future attention.";
        }

        $issue->update($updateData);

        return back()->with('success', $message);
    }
}
