<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CompanySetting;
use App\Models\PayrollPeriod;
use App\Models\PayrollStatutorySetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StatutoryReportController extends Controller
{
    private function authorizeStatutoryView(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isAdmin() && ! $user->hasPermission('view_salary'))) {
            abort(403, 'Unauthorized access to statutory payroll and tax records.');
        }
    }

    private function authorizeAdmin(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Administrator access required for statutory configuration.');
        }
    }

    /**
     * Display main Statutory & Tax Overview dashboard for a selected period.
     */
    public function index(Request $request): View
    {
        $this->authorizeStatutoryView();

        $now = Carbon::now('Asia/Colombo');
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $years = range($now->year - 2, $now->year + 2);

        $period = PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->with(['items.employee'])
            ->first();

        $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);
        $company = CompanySetting::getSettings();

        // Calculate statutory aggregates
        $items = $period ? $period->items : collect();

        $totalEpfQualifying = (float) $items->sum('epf_qualifying_earnings');
        $totalEpfEmployee = (float) $items->sum('epf_employee_amount');
        $totalEpfEmployer = (float) $items->sum('epf_employer_amount');
        $totalEtfEmployer = (float) $items->sum('etf_employer_amount');
        $totalApit = (float) $items->sum('apit_amount');
        $totalGross = (float) $items->sum('gross_salary');
        $totalNet = (float) $items->sum('net_salary');
        $totalCostToCompany = round($totalGross + $totalEpfEmployer + $totalEtfEmployer, 2);

        return view('payroll.statutory.index', compact(
            'year',
            'month',
            'periodDate',
            'years',
            'period',
            'items',
            'statutorySetting',
            'company',
            'totalEpfQualifying',
            'totalEpfEmployee',
            'totalEpfEmployer',
            'totalEtfEmployer',
            'totalApit',
            'totalGross',
            'totalNet',
            'totalCostToCompany'
        ));
    }

    /**
     * Monthly EPF Report (Form C / Monthly EPF Return).
     */
    public function epfReport(Request $request): View
    {
        $this->authorizeStatutoryView();

        $now = Carbon::now('Asia/Colombo');
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $period = PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->with(['items.employee'])
            ->first();

        $company = CompanySetting::getSettings();
        $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);
        $items = $period ? $period->items : collect();

        return view('payroll.statutory.epf', compact(
            'year',
            'month',
            'periodDate',
            'period',
            'company',
            'statutorySetting',
            'items'
        ));
    }

    /**
     * Monthly ETF Report (Form 04 / ETF Remittance Return).
     */
    public function etfReport(Request $request): View
    {
        $this->authorizeStatutoryView();

        $now = Carbon::now('Asia/Colombo');
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $period = PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->with(['items.employee'])
            ->first();

        $company = CompanySetting::getSettings();
        $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);
        $items = $period ? $period->items : collect();

        return view('payroll.statutory.etf', compact(
            'year',
            'month',
            'periodDate',
            'period',
            'company',
            'statutorySetting',
            'items'
        ));
    }

    /**
     * Monthly APIT Schedule (IRD Inland Revenue Department).
     */
    public function apitReport(Request $request): View
    {
        $this->authorizeStatutoryView();

        $now = Carbon::now('Asia/Colombo');
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $period = PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->with(['items.employee'])
            ->first();

        $company = CompanySetting::getSettings();
        $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);
        $items = $period ? $period->items : collect();

        return view('payroll.statutory.apit', compact(
            'year',
            'month',
            'periodDate',
            'period',
            'company',
            'statutorySetting',
            'items'
        ));
    }

    /**
     * Employer Contribution & Total Cost to Company (CTC) Summary.
     */
    public function employerSummary(Request $request): View
    {
        $this->authorizeStatutoryView();

        $now = Carbon::now('Asia/Colombo');
        $year = (int) $request->input('year', $now->year);
        $month = (int) $request->input('month', $now->month);

        $periodDate = Carbon::createFromDate($year, $month, 1, 'Asia/Colombo');
        $period = PayrollPeriod::where('year', $year)
            ->where('month', $month)
            ->with(['items.employee'])
            ->first();

        $company = CompanySetting::getSettings();
        $statutorySetting = PayrollStatutorySetting::forPeriod($year, $month);
        $items = $period ? $period->items : collect();

        return view('payroll.statutory.employer-summary', compact(
            'year',
            'month',
            'periodDate',
            'period',
            'company',
            'statutorySetting',
            'items'
        ));
    }

    /**
     * Manage configurable statutory rules and rates (Admin only).
     */
    public function settings(): View
    {
        $this->authorizeAdmin();

        $settings = PayrollStatutorySetting::orderBy('effective_from', 'desc')->get();
        $currentSetting = PayrollStatutorySetting::forPeriod(today()->year, today()->month);

        return view('payroll.statutory.settings', compact('settings', 'currentSetting'));
    }

    /**
     * Store or update statutory configuration rule (Admin only).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'epf_employee_rate' => 'required|numeric|min:0|max:100',
            'epf_employer_rate' => 'required|numeric|min:0|max:100',
            'etf_employer_rate' => 'required|numeric|min:0|max:100',
            'no_pay_divisor_type' => 'required|string|in:calendar_days,fixed_30,fixed_26,fixed_22,custom',
            'no_pay_custom_divisor' => 'nullable|integer|min:1|max:31',
            'overtime_normal_multiplier' => 'required|numeric|min:1|max:5',
            'overtime_holiday_multiplier' => 'required|numeric|min:1|max:5',
            'overtime_monthly_divisor_hours' => 'required|numeric|min:50|max:300',
            'apit_enabled' => 'required|boolean',
            'apit_monthly_relief' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        PayrollStatutorySetting::create([
            'effective_from' => $validated['effective_from'],
            'effective_to' => $validated['effective_to'] ?? null,
            'epf_employee_rate' => $validated['epf_employee_rate'],
            'epf_employer_rate' => $validated['epf_employer_rate'],
            'etf_employer_rate' => $validated['etf_employer_rate'],
            'no_pay_divisor_type' => $validated['no_pay_divisor_type'],
            'no_pay_custom_divisor' => $validated['no_pay_custom_divisor'] ?? null,
            'overtime_normal_multiplier' => $validated['overtime_normal_multiplier'],
            'overtime_holiday_multiplier' => $validated['overtime_holiday_multiplier'],
            'overtime_monthly_divisor_hours' => $validated['overtime_monthly_divisor_hours'],
            'apit_enabled' => (bool) $validated['apit_enabled'],
            'apit_monthly_relief' => $validated['apit_monthly_relief'],
            'apit_tax_slabs' => PayrollStatutorySetting::defaultTaxSlabs(),
            'is_active' => true,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        ActivityLog::log(
            'update_statutory_settings',
            "Created updated statutory configuration rule effective from {$validated['effective_from']} (EPF {$validated['epf_employee_rate']}%/{$validated['epf_employer_rate']}%, ETF {$validated['etf_employer_rate']}%)",
            'Payroll'
        );

        return back()->with('success', 'Statutory payroll configuration successfully saved with effective date.');
    }
}
