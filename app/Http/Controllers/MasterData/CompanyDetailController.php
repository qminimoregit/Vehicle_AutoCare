<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\CompanyDetailsRequest;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyDetailController extends Controller
{
    /**
     * Display and edit the auto care center's company information.
     */
    public function edit(Request $request): View
    {
        $company = CompanySetting::getSettings();
        $canEdit = $request->user() && $request->user()->isAdmin();

        return view('master-data.company-details.edit', compact('company', 'canEdit'));
    }

    /**
     * Update the company information and logo.
     */
    public function update(CompanyDetailsRequest $request): RedirectResponse
    {
        $company = CompanySetting::getSettings();
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            // Delete previous logo file if exists
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $path = $request->file('logo')->store('company', 'public');
            $validated['logo_path'] = $path;
        }

        unset($validated['logo']);

        $company->update($validated);

        ActivityLog::log(
            action: 'updated',
            description: 'Updated Auto Care Center business details.',
            module: 'Company Details'
        );

        return redirect()->route('master-data.company-details.edit')
            ->with('success', 'Company details saved successfully.');
    }
}
