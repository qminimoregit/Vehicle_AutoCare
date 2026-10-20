<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\BranchRequest;
use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Services\BranchContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Store a new branch.
     */
    public function store(BranchRequest $request): RedirectResponse
    {
        $company = CompanySetting::getSettings();
        $validated = $request->validated();

        $isMain = $request->boolean('is_main_branch');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;

        // If this is the first branch, it must be the main branch
        if ($company->branches()->count() === 0) {
            $isMain = true;
            $isActive = true;
        }

        DB::transaction(function () use ($company, $validated, $isMain, $isActive, &$branch) {
            if ($isMain) {
                Branch::where('company_id', $company->id)->update(['is_main_branch' => false]);
                $isActive = true;
            }

            $branch = Branch::create([
                'company_id' => $company->id,
                'branch_code' => strtoupper(trim($validated['branch_code'])),
                'branch_name' => trim($validated['branch_name']),
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'manager_id' => $validated['manager_id'] ?? null,
                'is_main_branch' => $isMain,
                'is_active' => $isActive,
            ]);

            // Sync assigned staff
            $staffIds = $validated['assigned_staff'] ?? [];
            if ($branch->manager_id && ! in_array($branch->manager_id, $staffIds)) {
                $staffIds[] = $branch->manager_id;
            }
            $branch->users()->sync($staffIds);
        });

        ActivityLog::log(
            action: 'created',
            description: "Created branch: {$branch->branch_name} ({$branch->branch_code})",
            module: 'Company Details'
        );

        return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
            ->with('success', "Branch '{$branch->branch_name}' created successfully.");
    }

    /**
     * Display the specified branch details (useful for AJAX modals or view mode).
     */
    public function show(Request $request, Branch $branch): JsonResponse
    {
        $user = $request->user();
        if (! $user->isAdmin() && ! $user->canAccessBranch($branch->id)) {
            abort(403, 'Unauthorized access to this branch.');
        }

        $branch->load(['manager', 'users']);

        return response()->json([
            'id' => $branch->id,
            'branch_code' => $branch->branch_code,
            'branch_name' => $branch->branch_name,
            'address' => $branch->address,
            'phone' => $branch->phone,
            'email' => $branch->email,
            'manager_id' => $branch->manager_id,
            'manager_name' => $branch->manager ? $branch->manager->name : null,
            'is_main_branch' => (bool) $branch->is_main_branch,
            'is_active' => (bool) $branch->is_active,
            'assigned_staff_ids' => $branch->users->pluck('id')->toArray(),
            'assigned_staff_names' => $branch->users->pluck('name')->toArray(),
            'created_at' => $branch->created_at ? $branch->created_at->format('Y-m-d H:i') : null,
        ]);
    }

    /**
     * Update an existing branch.
     */
    public function update(BranchRequest $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validated();

        $wantsMain = $request->boolean('is_main_branch');
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : $branch->is_active;

        // Cannot deactivate active main branch
        if ($branch->is_main_branch && ! $isActive) {
            return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
                ->with('error', 'The main branch cannot be deactivated.');
        }

        // Cannot uncheck main branch if it's currently the only main branch
        if ($branch->is_main_branch && ! $wantsMain) {
            $otherMainExists = Branch::where('company_id', $branch->company_id)
                ->where('id', '!=', $branch->id)
                ->where('is_main_branch', true)
                ->exists();

            if (! $otherMainExists) {
                return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
                    ->with('error', 'The company must maintain at least one main branch.');
            }
        }

        DB::transaction(function () use ($branch, $validated, $wantsMain, $isActive) {
            if ($wantsMain) {
                Branch::where('company_id', $branch->company_id)
                    ->where('id', '!=', $branch->id)
                    ->update(['is_main_branch' => false]);
                $isActive = true;
            }

            $branch->update([
                'branch_code' => strtoupper(trim($validated['branch_code'])),
                'branch_name' => trim($validated['branch_name']),
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'manager_id' => $validated['manager_id'] ?? null,
                'is_main_branch' => $wantsMain,
                'is_active' => $isActive,
            ]);

            // Sync assigned staff
            $staffIds = $validated['assigned_staff'] ?? [];
            if ($branch->manager_id && ! in_array($branch->manager_id, $staffIds)) {
                $staffIds[] = $branch->manager_id;
            }
            $branch->users()->sync($staffIds);
        });

        ActivityLog::log(
            action: 'updated',
            description: "Updated branch details: {$branch->branch_name} ({$branch->branch_code})",
            module: 'Company Details'
        );

        return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
            ->with('success', "Branch '{$branch->branch_name}' updated successfully.");
    }

    /**
     * Toggle the active status of a branch.
     */
    public function toggleStatus(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user() && $request->user()->isAdmin(), 403, 'Unauthorized.');

        if ($branch->is_main_branch && $branch->is_active) {
            return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
                ->with('error', 'The main branch cannot be deactivated.');
        }

        $branch->update(['is_active' => ! $branch->is_active]);

        $statusLabel = $branch->is_active ? 'activated' : 'deactivated';

        ActivityLog::log(
            action: 'updated',
            description: "Branch '{$branch->branch_name}' was {$statusLabel}.",
            module: 'Company Details'
        );

        return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
            ->with('success', "Branch '{$branch->branch_name}' {$statusLabel} successfully.");
    }

    /**
     * Designate this branch as the company's main branch.
     */
    public function setMain(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user() && $request->user()->isAdmin(), 403, 'Unauthorized.');

        $branch->setAsMain();

        ActivityLog::log(
            action: 'updated',
            description: "Set branch '{$branch->branch_name}' as the Main Branch.",
            module: 'Company Details'
        );

        return redirect()->route('master-data.company-details.edit', ['tab' => 'branches'])
            ->with('success', "Branch '{$branch->branch_name}' is now the Main Branch.");
    }

    /**
     * Switch persistent active branch in session.
     */
    public function switchBranch(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        $branchId = (int) $request->input('branch_id');
        $user = $request->user();

        if (! $user->canAccessBranch($branchId)) {
            return back()->with('error', 'You do not have access to the selected branch.');
        }

        $branch = Branch::findOrFail($branchId);
        if (! $branch->is_active) {
            return back()->with('error', 'The selected branch is inactive.');
        }

        BranchContext::setActiveBranchId($branchId, $user);

        return back()->with('success', "Active branch switched to {$branch->branch_name}.");
    }
}
