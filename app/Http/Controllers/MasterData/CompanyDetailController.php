<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
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
    public function edit(): View
    {
        $company = CompanySetting::getSettings();

        return view('master-data.company-details.edit', compact('company'));
    }

    /**
     * Update the company information and logo.
     */
    public function update(Request $request): RedirectResponse
    {
        $company = CompanySetting::getSettings();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
        ], [
            'company_name.required' => 'Company Name is required.',
            'logo.max' => 'The logo file size cannot exceed 2MB.',
        ]);

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
            description: "Updated Auto Care Center business details.",
            module: 'Company Details'
        );

        return redirect()->route('master-data.company-details.edit')
            ->with('success', 'Company details saved successfully.');
    }
}
